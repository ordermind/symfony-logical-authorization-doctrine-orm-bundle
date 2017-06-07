<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

/**
 * Event that is fired from a EntityDecorator when a method call is attempted on a entity
 */
interface BeforeMethodCallEventInterface
{

  /**
   * Gets the entity on which the call is made
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
   * Gets the metadata for the entity
   *
   * @return Doctrine\Common\Persistence\Mapping\ClassMetadata
   */
    public function getMetadata();

  /**
   * Gets the method that is used for the call
   *
   * @return string
   */
    public function getMethod();

  /**
   * Gets the arguments for the call
   *
   * @return array
   */
    public function getArguments();

  /**
   * Gets the abort flag value for this call
   *
   * @return bool
   */
    public function getAbort();

  /**
   * Sets the abort flag value for this delete call. If the abort flag is set to true the call won't be passed to the entity
   *
   * @param bool $abort The new value for the abort flag
   */
    public function setAbort($abort);
}
