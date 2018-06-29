<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

/**
 * Event that is fired from a EntityDecorator when a method call is attempted on a entity
 */
interface BeforeMethodCallEventInterface
{

  /**
   * Gets the entity on which the call is made
   *
   * @return object
   */
    public function getEntity();

  /**
   * Returns TRUE if the entity is new or FALSE if the entity is already persisted
   *
   * @return bool
   */
    public function isNew(): bool;

  /**
   * Gets the metadata for the entity
   *
   * @return Doctrine\Common\Persistence\Mapping\ClassMetadata
   */
    public function getMetadata(): \Doctrine\Common\Persistence\Mapping\ClassMetadata;

  /**
   * Gets the method that is used for the call
   *
   * @return string
   */
    public function getMethod(): string;

  /**
   * Gets the arguments for the call
   *
   * @return array
   */
    public function getArguments(): array;

  /**
   * Gets the abort flag value for this call
   *
   * @return bool
   */
    public function getAbort(): bool;

  /**
   * Sets the abort flag value for this delete call. If the abort flag is set to true the call won't be passed to the entity
   *
   * @param bool $abort The new value for the abort flag
   */
    public function setAbort(bool $abort);
}
