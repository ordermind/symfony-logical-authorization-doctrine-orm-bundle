<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\AbstractResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\UnknownResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\SingleEntityResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\MultipleEntityResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\LazyEntityCollectionResultEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\BeforeCreateEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\TestEntityAbortCreate;

class RepositoryDecoratorEventSubscriber implements EventSubscriberInterface {
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
      'logauth_doctrine_orm.event.repository_decorator.lazy_entity_collection_result' => array(
        array('onLazyEntityCollectionResult'),
      ),
      'logauth_doctrine_orm.event.repository_decorator.before_create' => array(
        array('onBeforeCreate'),
      ),
    );
  }

  public function onUnknownResult(UnknownResultEvent $event) {
    $this->onResult($event);
  }

  public function onSingleEntityResult(SingleEntityResultEvent $event) {
    $this->onResult($event);
  }

  public function onMultipleEntityResult(MultipleEntityResultEvent $event) {
    $this->onResult($event);
  }

  public function onLazyEntityCollectionResult(LazyEntityCollectionResultEvent $event) {
    $repository = $event->getRepository();
    $result = $event->getResult();
    $class = $repository->getClassName();
    foreach($result as $i => $item) {
      $result[$i] = $this->processEntity($item, $class);
    }
  }

  public function onBeforeCreate(BeforeCreateEvent $event) {
    $class = $event->getEntityClass();
    if($class === 'Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc\TestEntityAbortCreate') {
      $event->setAbort(true);
    }
  }

  protected function onResult(AbstractResultEvent $event) {
    $repository = $event->getRepository();
    $result = $event->getResult();
    $class = $repository->getClassName();
    $result = $this->processEntities($result, $class);
    $event->setResult($result);
  }

  protected function processEntities($entities, $class) {
    if(!is_array($entities)) return $this->processEntity($entities, $class);
    foreach($entities as $i => $entity) {
      $entities[$i] = $this->processEntity($entity, $class);
    }
    return $entities;
  }

  protected function processEntity($entity, $class) {
    if(!is_object($entity) || get_class($entity) !== $class) return $entity;
    $entity->setField2('hej');
    return $entity;
  }
}