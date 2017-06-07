<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * {@inheritdoc}
 */
class BeforeSaveEvent extends Event implements BeforeSaveEventInterface
{

  /**
   * @var mixed
   */
    protected $entity;

  /**
   * @var bool
   */
    protected $isNew;

  /**
   * @var bool
   */
    protected $abort = false;

  /**
   * @internal
   *
   * @param mixed $entity The entity that is about to be saved
   * @param bool  $isNew A flag for the persistence status of the entity
   */
    public function __construct($entity, $isNew)
    {
        $this->entity = $entity;
        $this->isNew = $isNew;
    }

  /**
   * {@inheritdoc}
   */
    public function getEntity()
    {
        return $this->entity;
    }

  /**
   * {@inheritdoc}
   */
    public function isNew()
    {
        return $this->isNew;
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
