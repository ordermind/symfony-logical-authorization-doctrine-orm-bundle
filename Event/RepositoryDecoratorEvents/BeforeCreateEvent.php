<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * {@inheritdoc}
 */
class BeforeCreateEvent extends Event implements BeforeCreateEventInterface
{

  /**
   * @var string
   */
    protected $entityClass;

  /**
   * @var bool
   */
    protected $abort = false;

  /**
   * @internal
   *
   * @param string $entityClass The class of the entity that is about to be created
   */
    public function __construct($entityClass)
    {
        $this->entityClass = $entityClass;
    }

  /**
   * {@inheritdoc}
   */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

  /**
   * {@inheritdoc}
   */
    public function getAbort()
    {
        return $this->abort;
    }

  /**
   * {@inheritdoc}
   */
    public function setAbort($abort)
    {
        $this->abort = (bool) $abort;
    }
}
