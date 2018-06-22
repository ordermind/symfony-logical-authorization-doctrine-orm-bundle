<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

/**
 * Event that is fired from a EntityDecorator when a deletion is attempted on a entity
 */
interface BeforeDeleteEventInterface
{

  /**
   * Gets the entity that is about to be deleted
   *
   * @return object
   */
    public function getEntity();

  /**
   * Returns TRUE if the entity is new or FALSE if the entity is already persisted
   *
   * @return bool
   */
    public function isNew();

  /**
   * Gets the abort flag value for this delete call
   *
   * @return bool
   */
    public function getAbort();

  /**
   * Sets the abort flag value for this delete call
   *
   * If the abort flag is set to true the entity won't be deleted.
   *
   * @param bool $abort The new value for the abort flag
   */
    public function setAbort($abort);
}
