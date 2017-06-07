<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

/**
 * Event that is fired from a EntityDecorator when a entity is attempted to be saved
 */
interface BeforeSaveEventInterface
{

  /**
   * Gets the entity that is about to be saved
   *
   * @return mixed
   */
    public function getEntity();

  /**
   * Returns TRUE if the entity is new or FALSE if the entity is already persisted
   *
   * @return bool
   */
    public function isNew();

  /**
   * Gets the abort flag value for this save call
   *
   * @return bool
   */
    public function getAbort();

  /**
   * Sets the abort flag value for this save call. If the abort flag is set to true the entity won't be saved
   *
   * @param bool $abort The new value for the abort flag
   */
    public function setAbort($abort);
}
