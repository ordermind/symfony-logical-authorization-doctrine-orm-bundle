<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Functional\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class DecoratorBase extends WebTestCase {
  protected $em;
  protected $container;

  /**
   * This method is run before each public test method
   */
  protected function setUp() {
    require_once __DIR__.'/../../AppKernel.php';
    $kernel = new \AppKernel('test', true);
    $kernel->boot();
    $this->container = $kernel->getContainer();
    $this->em = $this->container->get('doctrine.orm.entity_manager');

    $repository_services = array(
      'repository.test_entity',
      'repository.test_entity_constructor_params',
      'repository.test_entity_abort_calls',
      'repository.test_entity_abort_save',
      'repository.test_entity_abort_delete',
    );
    foreach($repository_services as $repository_service) {
      $this->deleteAllEntities($repository_service);
    }
  }

  protected function deleteAllEntities($repository_service) {
    $repositoryDecorator = $this->container->get($repository_service);
    $entityDecorators = $repositoryDecorator->findAll();
    foreach($entityDecorators as $entityDecorator) {
      $entityDecorator->delete(false);
    }
    $repositoryDecorator->getEntityManager()->flush();
  }
}
