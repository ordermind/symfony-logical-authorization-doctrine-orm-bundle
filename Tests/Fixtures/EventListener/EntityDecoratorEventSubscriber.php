<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeMethodCallEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeSaveEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeDeleteEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc\TestEntityAbortCalls;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc\TestEntityAbortSave;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc\TestEntityAbortDelete;


class EntityDecoratorEventSubscriber implements EventSubscriberInterface {
  public static function getSubscribedEvents() {
    return array(
      'ordermind_logical_authorization_doctrine_orm.event.entity_decorator.before_method_call' => array(
        array('onBeforeMethodCall'),
      ),
      'ordermind_logical_authorization_doctrine_orm.event.entity_decorator.before_save' => array(
        array('onBeforeSave'),
      ),
      'ordermind_logical_authorization_doctrine_orm.event.entity_decorator.before_delete' => array(
        array('onBeforeDelete'),
      ),
    );
  }

  public function onBeforeMethodCall(BeforeMethodCallEvent $event) {
    $entity = $event->getEntity();
    if($entity instanceof TestEntityAbortCalls) {
      $event->setAbort(true);
    }
  }

  public function onBeforeSave(BeforeSaveEvent $event) {
    $entity = $event->getEntity();
    if($entity instanceof TestEntityAbortSave) {
      $event->setAbort(true);
    }
  }

  public function onBeforeDelete(BeforeDeleteEvent $event) {
    $entity = $event->getEntity();
    if($entity instanceof TestEntityAbortDelete) {
      $event->setAbort(true);
    }
  }
}