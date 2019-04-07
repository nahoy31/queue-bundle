<?php

namespace Nahoy\ApiPlatform\QueueBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class UserAware
 *
 * @Annotation
 * @Target("CLASS")
 * @package Nahoy\ApiPlatform\QueueBundle\Annotation
 */
final class UserAware
{
    /**
     * @var string
     */
    public $userFieldName;

    /**
     * @var boolean
     */
    public $useAssociation = false;
}
