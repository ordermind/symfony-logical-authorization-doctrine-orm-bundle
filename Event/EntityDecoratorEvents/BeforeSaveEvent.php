<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * {@inheritdoc}
 */
class BeforeSaveEvent extends Event implements BeforeSaveEventInterface
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
     * @param object $entity The entity that is about to be saved
     * @param bool   $isNew  A flag for the persistence status of the entity
     */
    public function __construct($entity, bool $isNew)
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
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbort(): bool
    {
        return $this->abort;
    }

    /**
     * {@inheritdoc}
     */
    public function setAbort(bool $abort)
    {
        $this->abort = $abort;
    }
}
