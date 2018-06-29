<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecorator;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface;

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
    public function getEntityDecorator(EntityManager $em, EventDispatcherInterface $dispatcher, $entity): EntityDecoratorInterface
    {
        return new EntityDecorator($em, $dispatcher, $this->laModel, $entity);
    }
}
