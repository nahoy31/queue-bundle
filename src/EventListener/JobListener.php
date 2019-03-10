<?php

namespace Nahoy\ApiPlatform\QueueBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * JobListener
 *
 * ## prePersist
 */
class JobListener
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * Constructor
     *
     * @param TokenStorage $tokenStorage
     */
    public function __construct($className, TokenStorage $tokenStorage)
    {
        $this->className    = $className;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($this->className !== get_class($entity)) {
            return false;
        }

        $entity->setCreatedAt(new \DateTime());
    }
}
