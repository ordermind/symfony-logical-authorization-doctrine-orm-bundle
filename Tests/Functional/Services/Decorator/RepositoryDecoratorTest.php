<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Functional\Services\Decorator;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\RepositoryDecorator;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecorator;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Repository\Misc\TestEntityRepository;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc\TestEntity;

class RepositoryDecoratorTest extends DecoratorBase {

  public function testClass() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $this->assertTrue($repositoryDecorator instanceof RepositoryDecorator);
  }

  public function testGetClassName() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $class = $repositoryDecorator->getClassName();
    $this->assertEquals('Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc\TestEntity', $class);
  }

  public function testGetObjectManager() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $em = $repositoryDecorator->getEntityManager();
    $this->assertTrue($em instanceof EntityManager);
  }

  public function testSetObjectManager() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $repositoryDecorator->setEntityManager($this->container->get('doctrine.orm.entity_manager'));
    $em = $repositoryDecorator->getEntityManager();
    $this->assertTrue($em instanceof EntityManager);
  }

  public function testGetRepository() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $repository = $repositoryDecorator->getRepository();
    $this->assertTrue($repository instanceof TestEntityRepository);
  }

  public function testFind() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $entityDecorator = $repositoryDecorator->find($entityDecorator->getId());
    $this->assertEquals('test', $entityDecorator->getField1());
  }

  public function testFindEvent() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $entityDecorator = $repositoryDecorator->find($entityDecorator->getId());
    $this->assertEquals('hej', $entityDecorator->getField2());
  }

  public function testFindAll() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->save();
    $result = $repositoryDecorator->findAll();
    $this->assertEquals(1, count($result));
  }

  public function testFindAllEvent() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->save();
    $result = $repositoryDecorator->findAll();
    $entityDecorator = reset($result);
    $this->assertEquals('hej', $entityDecorator->getField2());
  }

  public function testFindBy() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
  }

  public function testFindByEvent() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $entityDecorator = reset($result);
    $this->assertEquals('hej', $entityDecorator->getField2());
  }

  public function testFindOneBy() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $entityDecorator = $repositoryDecorator->findOneBy(array('field1' => 'test'));
    $this->assertEquals('test', $entityDecorator->getField1());
  }

  public function testFindOneByEvent() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $entityDecorator = $repositoryDecorator->findOneBy(array('field1' => 'test'));
    $this->assertEquals('hej', $entityDecorator->getField2());
  }

  public function testMatching() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $expr = Criteria::expr();
    $criteria = Criteria::create();
    $criteria->where($expr->eq('field1', 'test'));
    $result = $repositoryDecorator->matching($criteria);
    $this->assertEquals(1, $result->count());
  }

  public function testMatchingEvent() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $expr = Criteria::expr();
    $criteria = Criteria::create();
    $criteria->where($expr->eq('field1', 'test'));
    $result = $repositoryDecorator->matching($criteria);
    foreach($result as $entity) {
      $this->assertEquals('hej', $entity->getField2());
    }
  }

  public function testCall() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findByField1('test');
    $this->assertEquals(1, count($result));
  }

  public function testCallEvent() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $entityDecorator = $repositoryDecorator->findOneByField1('test');
    $this->assertEquals('hej', $entityDecorator->getField2());
  }

  public function testCreate() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entityDecorator = $repositoryDecorator->create();
    $entityDecorator->setField1('test');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test'));
    $this->assertEquals(1, count($result));
  }

  public function testCreateWithParams() {
    $repositoryDecorator = $this->container->get('repository.test_entity_constructor_params');
    $entityDecorator = $repositoryDecorator->create('test1', 'test2', 'test3');
    $entityDecorator->save();
    $result = $repositoryDecorator->findBy(array('field1' => 'test1', 'field2' => 'test2', 'field3' => 'test3'));
    $this->assertEquals(1, count($result));
    $this->assertSame('test1', $entityDecorator->getField1());
    $this->assertSame('hej', $entityDecorator->getField2());
    $this->assertSame('test3', $entityDecorator->getField3());
  }

  public function testCreateAbort() {
    $repositoryDecorator = $this->container->get('repository.test_entity_abort_create');
    $entityDecorator = $repositoryDecorator->create();
    $this->assertNull($entityDecorator);
  }

  public function testWrapEntitiesSingleEntity() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entity = new TestEntity();
    $entityDecorators = $repositoryDecorator->wrapEntities($entity);
    $this->assertEquals(1, count($entityDecorators));
    foreach($entityDecorators as $entityDecorator) {
      $this->assertTrue($entityDecorator instanceof EntityDecorator);
    }
  }

  public function testWrapEntities() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entities = array(
      new TestEntity(),
      new TestEntity(),
    );
    $entityDecorators = $repositoryDecorator->wrapEntities($entities);
    $this->assertEquals(2, count($entityDecorators));
    foreach($entityDecorators as $entityDecorator) {
      $this->assertTrue($entityDecorator instanceof EntityDecorator);
    }
  }

  public function testWrapEntityWrongEntityType() {
    $repositoryDecorator = $this->container->get('repository.test_entity_constructor_params');
    $entity = new TestEntity();
    $this->assertNull($repositoryDecorator->wrapEntity(null));
    $this->assertSame($entity, $repositoryDecorator->wrapEntity($entity));
  }

  public function testWrapEntity() {
    $repositoryDecorator = $this->container->get('repository.test_entity');
    $entity = new TestEntity();
    $entityDecorator = $repositoryDecorator->wrapEntity($entity);
    $this->assertTrue($entityDecorator instanceof EntityDecorator);
  }

}
