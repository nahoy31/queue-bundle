<?php

namespace Nahoy\ApiPlatform\QueueBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Annotations\Reader;

use Nahoy\ApiPlatform\QueueBundle\Annotation\UserAware;

/**
 * Class UserFilter
 *
 * @package App\Filter
 */
class UserFilter extends SQLFilter
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @param ClassMetaData $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (empty($this->reader)) {
            return '';
        }

        // The Doctrine filter is called for any query on any entity
        // Check if the current entity is "user aware" (marked with an annotation)
        $userAware = $this->reader->getClassAnnotation(
            $targetEntity->getReflectionClass(),
            UserAware::class
        );

        if (!$userAware) {
            return '';
        }

        $fieldName = $userAware->userFieldName;

        try {
            // Don't worry, getParameter automatically quotes parameters
            $userId = $this->getParameter('id');
        } catch (\InvalidArgumentException $e) {
            // No user id has been defined
            return '';
        }

        if (empty($fieldName) || empty($userId)) {
            return '';
        }

        if (!$userAware->useAssociation) {
            $query = sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $userId);
        } else {
            $createdBy[] = $userId;

            // get the list of user ID with "created_by = THE_CURRENT_USER_ID"
            $conn = $this->getConnection();
            $statement = $conn->query('SELECT id FROM fos_user WHERE `created_by` = ' . $userId);
            $users = $statement->fetchAll();

            foreach ($users as $object) {
                $createdBy[] = $object['id'];
            }

            $query = sprintf('(%s.%s IN (%s) OR %s.id = %s)', $targetTableAlias, $fieldName, implode(',', $createdBy), $targetTableAlias, $userId);
        }

        return $query;
    }

    /**
     * @param Reader $reader
     */
    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
