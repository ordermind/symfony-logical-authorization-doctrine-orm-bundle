<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Doctrine\Common\Collections\Collection;

use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\AbstractResultEventInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\UnknownResultEventInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\SingleEntityResultEventInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\MultipleEntityResultEventInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\LazyEntityCollectionResultEventInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\BeforeCreateEventInterface;

use Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface;

/**
 * Event subscriber for repository decorator events
 */
class RepositoryDecoratorSubscriber implements EventSubscriberInterface
{
    /**
     * @var Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface
     */
    protected $laModel;

    /**
     * @var array
     */
    protected $config;

    /**
     * @internal
     *
     * @param Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface $laModel LogicalAuthorizationModel service for checking model permissions
     * @param array                                                                            $config  The logauth_doctrine_orm.config parameter
     */
    public function __construct(LogicalAuthorizationModelInterface $laModel, array $config)
    {
        $this->laModel = $laModel;
        $this->config = $config;
    }

    /**
      * {@inheritdoc}
      */
    public static function getSubscribedEvents()
    {
        return [
        'logauth_doctrine_orm.event.repository_decorator.unknown_result' => [
        ['onUnknownResult'],
        ],
        'logauth_doctrine_orm.event.repository_decorator.single_entity_result' => [
        ['onSingleEntityResult'],
        ],
        'logauth_doctrine_orm.event.repository_decorator.multiple_entity_result' => [
        ['onMultipleEntityResult'],
        ],
        'logauth_doctrine_orm.event.repository_decorator.before_create' => [
        ['onBeforeCreate'],
        ],
        'logauth_doctrine_orm.event.repository_decorator.lazy_entity_collection_result' => [
        ['onLazyEntityCollectionResult'],
        ],
        ];
    }

    /**
     * Event subscriber callback for modifying an unknown result from a repository decorator if access is not granted
     *
     * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\UnknownResultEventInterface $event The subscribed event
     */
    public function onUnknownResult(UnknownResultEventInterface $event)
    {
        $this->onResult($event);
    }

    /**
     * Event subscriber callback for modifying a single entity result from a repository decorator if access is not granted
     *
     * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\SingleEntityResultEventInterface $event The subscribed event
     */
    public function onSingleEntityResult(SingleEntityResultEventInterface $event)
    {
        $this->onResult($event);
    }

    /**
     * Event subscriber callback for modifying a multiple entity result from a repository decorator if access is not granted
     *
     * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\MultipleEntityResultEventInterface $event The subscribed event
     */
    public function onMultipleEntityResult(MultipleEntityResultEventInterface $event)
    {
        $this->onResult($event);
    }

    /**
     * Event subscriber callback for aborting the creation of a entity by a repository decorator if access is not granted
     *
     * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\BeforeCreateEventInterface $event The subscribed event
     */
    public function onBeforeCreate(BeforeCreateEventInterface $event)
    {
        $class = $event->getEntityClass();
        if (!$this->laModel->checkModelAccess($class, 'create')) {
            $event->setAbort(true);
        }
    }

    /**
     * Event subscriber callback for modifying a lazy entity collection result from a repository decorator if access is not granted
     *
     * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\LazyEntityCollectionResultEventInterface $event The subscribed event
     */
    public function onLazyEntityCollectionResult(LazyEntityCollectionResultEventInterface $event)
    {
        if (empty($this->config['check_lazy_loaded_entities'])) {
            return;
        }

        $this->onResult($event);
    }

    /**
     * @internal
     *
     * @param Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\AbstractResultEventInterface $event
     */
    protected function onResult(AbstractResultEventInterface $event)
    {
        $repository = $event->getRepository();
        $result = $event->getResult();
        $class = $repository->getClassName();
        if (is_array($result)) {
            $filteredResult = $this->filterEntities($result, $class);
        } elseif ($result instanceof Collection) {
            $filteredResult = $this->filterEntityCollection($result, $class);
        } else {
            $filteredResult = $this->filterEntityByPermissions($result, $class);
        }

        $event->setResult($filteredResult);
    }

    /**
     * @internal
     *
     * @param array  $entities
     * @param string $class
     *
     * @return array
     */
    protected function filterEntities(array $entities, string $class): array
    {
        foreach ($entities as $i => $entity) {
            $entities[$i] = $this->filterEntityByPermissions($entity, $class);
        }
        $entities = array_filter($entities);

        return $entities;
    }

    /**
     * @internal
     *
     * @param Doctrine\Common\Collections\Collection $collection
     * @param string                                 $class
     *
     * @return Doctrine\Common\Collections\Collection
     */
    protected function filterEntityCollection(Collection $collection, string $class): Collection
    {
        foreach ($collection as $i => $entity) {
            if (is_null($this->filterEntityByPermissions($entity, $class))) {
                $collection->remove($i);
            };
        }

        return $collection;
    }

    /**
     * @internal
     *
     * @param mixed  $entity
     * @param string $class
     *
     * @return mixed
     */
    protected function filterEntityByPermissions($entity, string $class)
    {
        if (!is_object($entity) || get_class($entity) !== $class) {
            return $entity;
        }

        if (!$this->laModel->checkModelAccess($entity, 'read')) {
            return null;
        }

        return $entity;
    }
}
