<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\LazyCriteriaCollection;
use Ordermind\LogicalAuthorizationBundle\Interfaces\ModelInterface;
use Ordermind\LogicalAuthorizationBundle\Interfaces\UserInterface;
use Ordermind\LogicalAuthorizationBundle\Services\HelperInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory\EntityDecoratorFactoryInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface;
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
   * @var Doctrine\ORM\EntityManager
   */
    protected $em;

  /**
   * @var Doctrine\ORM\EntityRepository
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
   * @param Doctrine\ORM\EntityManager                                                                       $em                     The entity manager to use in this decorator
   * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Factory\EntityDecoratorFactoryInterface $entityDecoratorFactory The factory to use for creating new entity decorators
   * @param Symfony\Component\EventDispatcher\EventDispatcherInterface                                       $dispatcher             The event dispatcher to use in this decorator
   * @param Ordermind\LogicalAuthorizationBundle\Services\HelperInterface                                    $helper                 LogicalAuthorizaton helper service
   * @param string                                                                                           $class                  The entity class to use in this decorator
   */
    public function __construct(EntityManager $em, EntityDecoratorFactoryInterface $entityDecoratorFactory, EventDispatcherInterface $dispatcher, HelperInterface $helper, $class)
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
    public function getClassName(): string
    {
        $repository = $this->getRepository();

        return $repository->getClassName();
    }

  /**
   * {@inheritdoc}
   */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

  /**
   * {@inheritdoc}
   */
    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

  /**
   * {@inheritdoc}
   */
    public function getRepository(): EntityRepository
    {
        return $this->repository;
    }

  /**
   * {@inheritdoc}
   */
    public function find($id, $lockMode = null, $lockVersion = null): ?EntityDecoratorInterface
    {
        $repository = $this->getRepository();
        $result = $repository->find($id, $lockMode, $lockVersion);
        $event = new SingleEntityResultEvent($repository, 'find', [$id, $lockMode, $lockVersion], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.repository_decorator.single_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntity($result);
    }

  /**
   * {@inheritdoc}
   */
    public function findAll(): array
    {
        $repository = $this->getRepository();
        $result = $repository->findAll();
        $event = new MultipleEntityResultEvent($repository, 'findAll', [], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.repository_decorator.multiple_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntities($result);
    }

  /**
   * {@inheritdoc}
   */
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null): array
    {
        $repository = $this->getRepository();
        $result = $repository->findBy($criteria, $sort, $limit, $skip);
        $event = new MultipleEntityResultEvent($repository, 'findBy', [$criteria, $sort, $limit, $skip], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.repository_decorator.multiple_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntities($result);
    }

  /**
   * {@inheritdoc}
   */
    public function findOneBy(array $criteria): ?EntityDecoratorInterface
    {
        $repository = $this->getRepository();
        $result = $repository->findOneBy($criteria);
        $event = new SingleEntityResultEvent($repository, 'findOneBy', [$criteria], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.repository_decorator.single_entity_result', $event);
        $result = $event->getResult();

        return $this->wrapEntity($result);
    }

  /**
   * {@inheritdoc}
   */
    public function matching(Criteria $criteria): LazyCriteriaCollection
    {
        $repository = $this->getRepository();
        $result = $repository->matching($criteria);
        $event = new LazyEntityCollectionResultEvent($repository, 'matching', [$criteria], $result);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.repository_decorator.lazy_entity_collection_result', $event);
        $result = $event->getResult();

        return $result;
    }

  /**
   * {@inheritdoc}
   */
    public function create(): ?EntityDecoratorInterface
    {
        $class = $this->getClassName();

        $event = new BeforeCreateEvent($class);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.repository_decorator.before_create', $event);
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
    public function wrapEntities($entities): ?array
    {
        if (!is_array($entities)) {
            if (is_null($entities)) {
                return $entities;
            }

            return [$this->wrapEntity($entities)];
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
   * Traps all method calls on the repository and fires the event 'logauth_doctrine_orm.event.repository_decorator.unknown_result' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\UnknownResultEvent, allowing tampering with the result before returning it to the caller.
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
        $dispatcher->dispatch('logauth_doctrine_orm.event.repository_decorator.unknown_result', $event);
        $result = $event->getResult();

        if (!is_array($result)) {
            return $this->wrapEntity($result);
        }

        return $this->wrapEntities($result);
    }

    /**
     * @internal
     *
     * @return Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @internal
     *
     * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface $entityDecorator
     *
     * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface
     */
    protected function setAuthor(EntityDecoratorInterface $entityDecorator)
    {
        $entity = $entityDecorator->getEntity();
        if (!($entity instanceof ModelInterface)) {
            return $entityDecorator;
        }

        $author = $this->helper->getCurrentUser();
        if (!($author instanceof UserInterface)) {
            return $entityDecorator;
        }

        $entity->setAuthor($author);

        return $entityDecorator;
    }
}
