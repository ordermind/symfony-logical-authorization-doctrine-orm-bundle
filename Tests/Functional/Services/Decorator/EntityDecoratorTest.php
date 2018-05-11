<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Functional\Services\Decorator;

use Doctrine\ORM\EntityManager;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecorator;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc\TestEntity;

class EntityDecoratorTest extends DecoratorBase {

  public function testClass() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $this->assertTrue($entityDecorator instanceof EntityDecorator);
  }

  public function testGetEntity() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entity = $entityDecorator->getEntity();
    $this->assertTrue($entity instanceof TestEntity);
  }

  public function testSetObjectManager() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setEntityManager($this->container->get('doctrine.orm.entity_manager'));
    $em = $entityDecorator->getEntityManager();
    $this->assertTrue($em instanceof EntityManager);
  }

  public function testGetObjectManager() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $em = $entityDecorator->getEntityManager();
    $this->assertTrue($em instanceof EntityManager);
  }

  public function testSave() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
  }

  public function testSaveNoFlush() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save(false);
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(0, count($result));
    $em = $entityDecorator->getEntityManager();
    $em->flush();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
  }

  public function testSaveAbort() {
    $repositoryDecorator = $this->container->get('repository.test_entity_abort_save');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $response = $entityDecorator->save();
    $this->assertFalse($response);
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(0, count($result));
  }

  public function testDelete() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
    $entityDecorator->delete();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(0, count($result));
  }

  public function testDeleteNoFlush() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
    $entityDecorator->delete(false);
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
    $em = $entityDecorator->getEntityManager();
    $em->flush();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(0, count($result));
  }

  public function testDeleteAbort() {
    $repositoryDecorator = $this->container->get('repository.test_entity_abort_delete');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
    $response = $entityDecorator->delete();
    $this->assertFalse($response);
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
  }

  public function testCall() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $this->assertEquals('test', $entityDecorator->getEntity()->getField1());
  }

  public function testCallAbort() {
    $repositoryDecorator = $this->container->get('repository.test_entity_abort_calls');
    $entityDecorator = $repositoryDecorator->create();
    $entity = $entityDecorator->getEntity();
    $response = $entityDecorator->setField1('test');
    $this->assertNull($response);
    $this->assertEmpty($entity->getField1());
    $entity->setField1('test');
    $this->assertEquals('test', $entity->getField1());
    $this->assertNull($entityDecorator->getField1());
  }

  public function testIsNew() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $this->assertTrue($entityDecorator->isNew());
    $entityDecorator->save();
    $this->assertFalse($entityDecorator->isNew());
    $loadedEntityDecorator = $repositoryDecorator->find($entityDecorator->getId());
    $this->assertFalse($loadedEntityDecorator->isNew());
  }

  public function testGetAvailableActions() {
    $laModel = $this->container->get('test.logauth.service.logauth_model');
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entity = $entityDecorator->getEntity();
    $available_actions_decorator = $entityDecorator->getAvailableActions('anon.');
    $available_actions_class = $laModel->getAvailableActions(get_class($entity), ['create', 'read', 'update', 'delete'], ['get', 'set'], 'anon.');
    $this->assertSame($available_actions_decorator, $available_actions_class);
  }
}
