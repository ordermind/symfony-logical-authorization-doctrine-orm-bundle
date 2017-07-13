<?php

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

class RepositoryDecoratorSubscriber implements EventSubscriberInterface {
  protected $laModel;
  protected $config;

  public function __construct(LogicalAuthorizationModelInterface $laModel, array $config) {
    $this->laModel = $laModel;
    $this->config = $config;
  }

  public static function getSubscribedEvents() {
    return array(
      'logauth_doctrine_orm.event.repository_decorator.unknown_result' => array(
        array('onUnknownResult'),
      ),
      'logauth_doctrine_orm.event.repository_decorator.single_entity_result' => array(
        array('onSingleEntityResult'),
      ),
      'logauth_doctrine_orm.event.repository_decorator.multiple_entity_result' => array(
        array('onMultipleEntityResult'),
      ),
      'logauth_doctrine_orm.event.repository_decorator.before_create' => array(
        array('onBeforeCreate'),
      ),
      'logauth_doctrine_orm.event.repository_decorator.lazy_entity_collection_result' => array(
        array('onLazyEntityCollectionResult'),
      ),
    );
  }

  public function onUnknownResult(UnknownResultEventInterface $event) {
    $this->onResult($event);
  }
  public function onSingleEntityResult(SingleEntityResultEventInterface $event) {
    $this->onResult($event);
  }
  public function onMultipleEntityResult(MultipleEntityResultEventInterface $event) {
    $this->onResult($event);
  }
  public function onBeforeCreate(BeforeCreateEventInterface $event) {
    $class = $event->getEntityClass();
    if(!$this->laModel->checkModelAccess($class, 'create')) {
      $event->setAbort(true);
    }
  }
  public function onLazyEntityCollectionResult(LazyEntityCollectionResultEventInterface $event) {
    if(empty($this->config['check_lazy_loaded_entities'])) return;

    $this->onResult($event);
  }

  protected function onResult(AbstractResultEventInterface $event) {
    $repository = $event->getRepository();
    $result = $event->getResult();
    $class = $repository->getClassName();
    if(is_array($result)) {
      $filtered_result = $this->filterEntities($result, $class);
    }
    elseif($result instanceof Collection) {
      $filtered_result = $this->filterEntityCollection($result, $class);
    }
    else {
      $filtered_result = $this->filterEntityByPermissions($result, $class);
    }

    $event->setResult($filtered_result);
  }
  protected function filterEntities($entities, $class) {
    foreach($entities as $i => $entity) {
      $entities[$i] = $this->filterEntityByPermissions($entity, $class);
    }
    $entities = array_filter($entities);
    return $entities;
  }
  protected function filterEntityCollection($collection, $class) {
    foreach($collection as $i => $entity) {
      if(is_null($this->filterEntityByPermissions($entity, $class))) {
        $collection->remove($i);
      };
    }
    return $collection;
  }
  protected function filterEntityByPermissions($entity, $class) {
    if(!is_object($entity) || get_class($entity) !== $class) return $entity;

    if(!$this->laModel->checkModelAccess($entity, 'read')) {
      return null;
    }

    return $entity;
  }
}
