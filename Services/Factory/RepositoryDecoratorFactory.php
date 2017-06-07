<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\RepositoryDecorator;
use Ordermind\LogicalAuthorizationBundle\Services\HelperInterface;

/**
 * {@inheritdoc}
 */
class RepositoryDecoratorFactory implements RepositoryDecoratorFactoryInterface
{

  /**
   * @var Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\ManagerRegistryInterface
   */
    protected $managerRegistry;

  /**
   * @var Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory\EntityDecoratorFactoryInterface
   */
    protected $entityDecoratorFactory;

  /**
   * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
    protected $dispatcher;

  /**
   * @var Ordermind\LogicalAuthorizationBundle\Services\HelperInterface
   */
    protected $helper;

  /**
   * {@inheritdoc}
   */
    public function setManagerRegistry(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

  /**
   * {@inheritdoc}
   */
    public function setEntityDecoratorFactory(EntityDecoratorFactoryInterface $entityDecoratorFactory)
    {
        $this->entityDecoratorFactory = $entityDecoratorFactory;
    }

  /**
   * {@inheritdoc}
   */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

  /**
   * {@inheritdoc}
   */
    public function setHelper(HelperInterface $helper)
    {
        $this->helper = $helper;
    }

  /**
   * {@inheritdoc}
   */
    public function getRepositoryDecorator($class)
    {
        $em = $this->managerRegistry->getManagerForClass($class);

        return new RepositoryDecorator($em, $this->entityDecoratorFactory, $this->dispatcher, $this->helper, $class);
    }
}
