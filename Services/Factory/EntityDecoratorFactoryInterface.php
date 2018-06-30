<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory;

/**
 * Factory for Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface
 */
interface EntityDecoratorFactoryInterface
{

  /**
   * Gets a new entity decorator
   *
   * @param Doctrine\ORM\EntityManager                                 $em         The entity manager to use for the new entity decorator
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher The event dispatcher to use for the new entity decorator
   * @param object                                                     $entity     The entity to wrap in the manager
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface
   */
    public function getEntityDecorator(\Doctrine\ORM\EntityManager $em, \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher, $entity): \Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface;
}
