<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory;

/**
 * Factory for Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\RepositoryDecoratorInterface
 */
interface RepositoryDecoratorFactoryInterface
{

  /**
   * Sets the manager registry
   *
   * @param Doctrine\Common\Persistence\ManagerRegistry $managerRegistry The manager registry to use for this repository decorator factory
   */
    public function setManagerRegistry(\Doctrine\Common\Persistence\ManagerRegistry $managerRegistry);

  /**
   * Sets the entity decorator factory
   *
   * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory\EntityDecoratorFactoryInterface $entityDecoratorFactory The entity decorator factory to use for this repository decorator factory
   */
    public function setEntityDecoratorFactory(\Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory\EntityDecoratorFactoryInterface $entityDecoratorFactory);

  /**
   * Sets the event dispatcher
   *
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher The event dispatcher to use for this repository decorator factory
   */
    public function setDispatcher(\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher);

  /**
   * Sets the helper service
   *
   * @param Ordermind\LogicalAuthorizationBundle\Services\HelperInterface $helper The helper service to use for this factory
   */
    public function setHelper(\Ordermind\LogicalAuthorizationBundle\Services\HelperInterface $helper);

  /**
   * Gets a new repository decorator
   *
   * @param string $class The entity class to use for the new repository decorator
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\RepositoryDecoratorInterface A new repository decorator
   */
    public function getRepositoryDecorator($class);
}
