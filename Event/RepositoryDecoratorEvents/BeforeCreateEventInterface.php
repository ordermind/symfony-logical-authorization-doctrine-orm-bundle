<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

/**
 * Event that is fired from a RepositoryDecorator when a entity is about to be created
 */
interface BeforeCreateEventInterface
{

  /**
   * Gets the class of the entity that is about to be created
   *
   * @return string
   */
    public function getEntityClass(): string;

  /**
   * Gets the abort flag value for this creation
   *
   * @return bool
   */
    public function getAbort(): bool;

  /**
   * Sets the abort flag value for this creation
   *
   * If the abort flag is set to true the entity won't be created.
   *
   * @param bool $abort The new value for the abort flag
   */
    public function setAbort(bool $abort);
}
