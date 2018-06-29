<?php
declare(strict_types=1);

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
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

  /**
   * {@inheritdoc}
   */
    public function getEntityClass(): string
    {
        return $this->entityClass;
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
