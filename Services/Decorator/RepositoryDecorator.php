<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\Criteria;
use Ordermind\LogicalAuthorizationBundle\Services\HelperInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory\EntityDecoratorFactoryInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\UnknownResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\SingleEntityResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\MultipleEntityResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\LazyEntityCollectionResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\BeforeCreateEvent;

/**
 * {@inheritdoc}
 */
class RepositoryDecorator implements RepositoryDecoratorInterface
{

  /**
   * @var Doctrine\Common\Persistence\ObjectManager
   */
    protected $em;

  /**
   * @var Doctrine\Common\Persistence\ObjectRepository
   */
    protected $repository;

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
   * @internal
   *
   * @param Doctrine\Common\Persistence\ObjectManager                                     $em                  The entity manager to use in this decorator
   * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory\EntityDecoratorFactoryInterface $entityDecoratorFactory The factory to use for creating new entity decorators
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface                    $dispatcher          The event dispatcher to use in this decorator
   * @param Ordermind\LogicalAuthorizationBundle\Services\HelperInterface $helper LogicalAuthorizaton helper service
   * @param string                                                                        $class               The entity class to use in this decorator
   */
    public function __construct(ObjectManager $em, EntityDecoratorFactoryInterface $entityDecoratorFactory, EventDispatcherInterface $dispatcher, HelperInterface $helper, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->entityDecoratorFactory = $entityDecoratorFactory;
        $this->dispatcher = $dispatcher;
        $this->helper = $helper;
    }

  /**
   * {@inheritdoc}
   */
    public function getClassName()
    {
        $repository = $this->getRepository();

        return $repository->getClassName();
    }

  /**
   * {@inheritdoc}
   */
    public function setEntityManager(ObjectManager $em)
    {
        $this->em = $em;

        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getEntityManager()
    {
        return $this->em;
    }

  /**
   * {@inheritdoc}
   */
    public function getRepository()
    {
        return $this->repository;
    }

  /**
   * {@inheritdoc}
   */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $repository = $this->getRepository();
        $result = $repository->find($id, $lockMode, $lockVersion);
        $event = new SingleEntityResultEvent($repository, 'find', [$id, $lockMode, $lockVersion], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('ordermind_logical_authorization_doctrine_orm.event.repository_decorator.single_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntity($result);
    }

  /**
   * {@inheritdoc}
   */
    public function findAll()
    {
        $repository = $this->getRepository();
        $result = $repository->findAll();
        $event = new MultipleEntityResultEvent($repository, 'findAll', [], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('ordermind_logical_authorization_doctrine_orm.event.repository_decorator.multiple_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntities($result);
    }

  /**
   * {@inheritdoc}
   */
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        $repository = $this->getRepository();
        $result = $repository->findBy($criteria, $sort, $limit, $skip);
        $event = new MultipleEntityResultEvent($repository, 'findBy', [$criteria, $sort, $limit, $skip], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('ordermind_logical_authorization_doctrine_orm.event.repository_decorator.multiple_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntities($result);
    }

  /**
   * {@inheritdoc}
   */
    public function findOneBy(array $criteria)
    {
        $repository = $this->getRepository();
        $result = $repository->findOneBy($criteria);
        $event = new SingleEntityResultEvent($repository, 'findOneBy', [$criteria], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('ordermind_logical_authorization_doctrine_orm.event.repository_decorator.single_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntity($result);
    }

  /**
   * {@inheritdoc}
   */
    public function matching(Criteria $criteria)
    {
        $repository = $this->getRepository();
        $result = $repository->matching($criteria);
        $event = new LazyEntityCollectionResultEvent($repository, 'matching', [$criteria], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('ordermind_logical_authorization_doctrine_orm.event.repository_decorator.lazy_entity_collection_result', $event);
        $result = $event->getResult();

        return $result;
    }

  /**
   * {@inheritdoc}
   */
    public function create()
    {
        $class = $this->getClassName();

        $event = new BeforeCreateEvent($class);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('ordermind_logical_authorization_doctrine_orm.event.repository_decorator.before_create', $event);
        if ($event->getAbort()) {
            return null;
        }

        $params = func_get_args();
        if ($params) {
            $reflector = new \ReflectionClass($class);
            $entity = $reflector->newInstanceArgs($params);
        } else {
            $entity = new $class();
        }

        $entityDecorator = $this->wrapEntity($entity);

        $this->setAuthor($entityDecorator);

        return $entityDecorator;
    }

  /**
   * {@inheritdoc}
   */
    public function wrapEntities($entities)
    {
        if (!is_array($entities)) {
            return $this->wrapEntity($entities);
        }

        foreach ($entities as $i => $entity) {
            $entities[$i] = $this->wrapEntity($entity);
        }

        return $entities;
    }

  /**
   * {@inheritdoc}
   */
    public function wrapEntity($entity)
    {
        if (!is_object($entity) || get_class($entity) !== $this->getClassName()) {
            return $entity;
        }

        return $this->entityDecoratorFactory->getEntityDecorator($this->getEntityManager(), $this->getDispatcher(), $entity);
    }

  /**
   * Catch-all for method calls on the repository
   *
   * Traps all method calls on the repository and fires the event 'ordermind_logical_authorization_doctrine_orm.event.repository_decorator.unknown_result' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\UnknownResultEvent, allowing tampering with the result before returning it to the caller.
   *
   * @param string $method    The method used for the call
   * @param array  $arguments The arguments used for the call
   *
   * @return mixed
   */
    public function __call($method, array $arguments)
    {
        $repository = $this->getRepository();
        $result = call_user_func_array([$repository, $method], $arguments);
        $event = new UnknownResultEvent($repository, $method, $arguments, $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('ordermind_logical_authorization_doctrine_orm.event.repository_decorator.unknown_result', $event);
        $result = $event->getResult();

        return $this->wrapEntities($result);
    }

    protected function getDispatcher()
    {
        return $this->dispatcher;
    }

    protected function setAuthor(EntityDecoratorInterface $entityDecorator)
    {
        $entity = $entityDecorator->getEntity();
        if(!($entity instanceof ModelInterface)) return $entityDecorator;

        $author = $this->helper->getCurrentUser();
        if(!($author instanceof UserInterface)) return $entityDecorator;

        $entity->setAuthor($author);
    }
}
