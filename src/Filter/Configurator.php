<?php

namespace Nahoy\ApiPlatform\QueueBundle\Filter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Annotations\Reader;

/**
 * Class Configurator
 * @package App\Filter
 */
class Configurator
{
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Configurator constructor
     *
     * @param ObjectManager $em
     * @param TokenStorageInterface $tokenStorage
     * @param Reader $reader
     */
    public function __construct(ObjectManager $em, TokenStorageInterface $tokenStorage, Reader $reader)
    {
        $this->em           = $em;
        $this->tokenStorage = $tokenStorage;
        $this->reader       = $reader;
    }

    public function onKernelRequest()
    {
        if ($user = $this->getUser()) {
            $roles = $user->getRoles();

            // if not ROLE_ADMIN
            if (!in_array('ROLE_ADMIN', $roles)) {
                $filter = $this->em->getFilters()->enable('nahoy_api_queue_user_filter');
                $filter->setParameter('id', $user->getId());
                $filter->setAnnotationReader($this->reader);
            }
        }
    }

    /**
     * @return null|object
     */
    private function getUser()
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return null;
        }

        return $user;
    }
}
