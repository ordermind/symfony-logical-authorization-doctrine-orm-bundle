<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * {@inheritdoc}
 */
class BeforeDeleteEvent extends Event implements BeforeDeleteEventInterface
{

  /**
   * @var object
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
   * @param object $entity The entity that is about to be deleted
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
