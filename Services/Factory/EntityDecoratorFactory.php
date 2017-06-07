<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecorator;

/**
 * {@inheritdoc}
 */
class EntityDecoratorFactory implements EntityDecoratorFactoryInterface
{

  /**
   * @var Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface
   */
    protected $laModel;

  /**
   * @internal
   *
   * @param Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface $laModel LogicalAuthorizationEntity service
   */
    public function __construct(LogicalAuthorizationModelInterface $laModel)
    {
        $this->laModel = $laModel;
    }

  /**
   * {@inheritdoc}
   */
    public function getEntityDecorator(ObjectManager $em, EventDispatcherInterface $dispatcher, $entity)
    {
        return new EntityDecorator($em, $dispatcher, $this->laModel, $entity);
    }
}
