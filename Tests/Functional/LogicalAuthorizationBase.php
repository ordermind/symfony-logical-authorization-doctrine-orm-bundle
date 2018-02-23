<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Encoder\JsonDecode;

abstract class LogicalAuthorizationBase extends WebTestCase {
  protected static $superadmin_user;
  protected static $admin_user;
  protected static $authenticated_user;
  protected $user_credentials = [
    'authenticated_user' => 'userpass',
    'admin_user' => 'adminpass',
    'superadmin_user' => 'superadminpass',
  ];
  protected $load_services = array();
  protected $testEntityRepositoryDecorator;
  protected $testEntityRoleAuthorRepositoryDecorator;
  protected $testEntityHasAccountNoInterfaceRepositoryDecorator;
  protected $testEntityNoBypassRepositoryDecorator;
  protected $testEntityOverriddenPermissionsRepositoryDecorator;
  protected $testEntityVariousPermissionsRepositoryDecorator;
  protected $testUserRepositoryDecorator;
  protected $testEntityOperations;
  protected $client;

  /**
   * This method is run before each public test method
   */
  protected function setUp() {
    require_once __DIR__.'/../AppKernel.php';
    $kernel = new \AppKernel('test', true);
    $kernel->boot();
    $this->client = static::createClient();

    $this->load_services['testEntityRepositoryDecorator'] = 'repository.test_entity';
    $this->load_services['testUserRepositoryDecorator'] = 'repository.test_user';
    $this->load_services['testEntityOperations'] = 'test_entity_operations';
    $container = $kernel->getContainer();
    foreach($this->load_services as $property_name => $service_name) {
      $this->$property_name = $container->get($service_name);
    }

    $this->deleteAll(array(
      $this->testEntityRepositoryDecorator,
      $this->testEntityRoleAuthorRepositoryDecorator,
      $this->testEntityHasAccountNoInterfaceRepositoryDecorator,
      $this->testEntityNoBypassRepositoryDecorator,
      $this->testEntityOverriddenPermissionsRepositoryDecorator,
      $this->testEntityVariousPermissionsRepositoryDecorator,
    ));

    $this->addUsers();
  }

  /**
   * This method is run after each public test method
   *
   * It is important to reset all non-static properties to minimize memory leaks.
   */
  protected function tearDown() {
    if(!is_null($this->testEntityRepositoryDecorator)) {
      $this->testEntityRepositoryDecorator->getEntityManager()->getConnection()->close();
      $this->testEntityRepositoryDecorator = null;
    }
    if(!is_null($this->testEntityRoleAuthorRepositoryDecorator)) {
      $this->testEntityRoleAuthorRepositoryDecorator->getEntityManager()->getConnection()->close();
      $this->testEntityRoleAuthorRepositoryDecorator = null;
    }
    if(!is_null($this->testEntityHasAccountNoInterfaceRepositoryDecorator)) {
      $this->testEntityHasAccountNoInterfaceRepositoryDecorator->getEntityManager()->getConnection()->close();
      $this->testEntityHasAccountNoInterfaceRepositoryDecorator = null;
    }
    if(!is_null($this->testEntityNoBypassRepositoryDecorator)) {
      $this->testEntityNoBypassRepositoryDecorator->getEntityManager()->getConnection()->close();
      $this->testEntityNoBypassRepositoryDecorator = null;
    }
    if(!is_null($this->testEntityOverriddenPermissionsRepositoryDecorator)) {
      $this->testEntityOverriddenPermissionsRepositoryDecorator->getEntityManager()->getConnection()->close();
      $this->testEntityOverriddenPermissionsRepositoryDecorator = null;
    }
    if(!is_null($this->testEntityVariousPermissionsRepositoryDecorator)) {
      $this->testEntityVariousPermissionsRepositoryDecorator->getEntityManager()->getConnection()->close();
      $this->testEntityVariousPermissionsRepositoryDecorator = null;
    }
    if(!is_null($this->testUserRepositoryDecorator)) {
      $this->testUserRepositoryDecorator->getEntityManager()->getConnection()->close();
      $this->testUserRepositoryDecorator = null;
    }
    $this->testEntityOperations = null;
    $this->client = null;

    parent::tearDown();
  }

  protected function deleteAll($decorators) {
    foreach($decorators as $repositoryDecorator) {
      $entityDecorators = $repositoryDecorator->findAll();
      foreach($entityDecorators as $entityDecorator) {
        $entityDecorator->delete(false);
      }
      $repositoryDecorator->getEntityManager()->flush();
    }
  }

  protected function addUsers() {
    //Create new normal user
    if(!static::$authenticated_user || get_class(static::$authenticated_user->getEntity()) !== $this->testUserRepositoryDecorator->getClassName()) {
      static::$authenticated_user = $this->testUserRepositoryDecorator->create('authenticated_user', $this->user_credentials['authenticated_user'], [], 'user@email.com');
      static::$authenticated_user->save();
    }

    //Create new admin user
    if(!static::$admin_user || get_class(static::$admin_user->getEntity()) !== $this->testUserRepositoryDecorator->getClassName()) {
      static::$admin_user = $this->testUserRepositoryDecorator->create('admin_user', $this->user_credentials['admin_user'], ['ROLE_ADMIN'], 'admin@email.com');
      static::$admin_user->save();
    }

    //Create superadmin user
    if(!static::$superadmin_user || get_class(static::$superadmin_user->getEntity()) !== $this->testUserRepositoryDecorator->getClassName()) {
      static::$superadmin_user = $this->testUserRepositoryDecorator->create('superadmin_user', $this->user_credentials['superadmin_user'], [], 'superadmin@email.com');
      static::$superadmin_user->setBypassAccess(true);
      static::$superadmin_user->save();
    }
  }

  protected function sendRequestAs($method = 'GET', $slug, array $params = array(), $user = null) {
    $headers = array();
    if($user) {
      $headers = array(
        'PHP_AUTH_USER' => $user->getUsername(),
        'PHP_AUTH_PW'   => $this->user_credentials[$user->getUsername()],
      );
    }
    $this->client->request($method, $slug, $params, array(), $headers);
  }

  /*------------Miscellaneous tests---------------*/

  public function testRouteLoadEntityAllow() {
    $testEntityDecorator = $this->testEntityRepositoryDecorator->create()->save();
    $this->sendRequestAs('GET', '/test/load-test-entity/' . $testEntityDecorator->getId(), [], static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
  }

  /**
   * @expectedException Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   */
  public function testRouteLoadEntityDisallow() {
    $testEntityDecorator = $this->testEntityRepositoryDecorator->create()->save();
    $this->sendRequestAs('GET', '/test/load-test-entity/' . $testEntityDecorator->getId(), [], static::$authenticated_user);
  }

  public function testRepositoryDecoratorCreateSetAuthor() {
    $entityDecorator = $this->testEntityRoleAuthorRepositoryDecorator->create();
    $author = $entityDecorator->getAuthor();
    $this->assertNull($author);

    $this->sendRequestAs('GET', '/test/create-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $id = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue((bool) $id);
    $entityDecorator = $this->testEntityRoleAuthorRepositoryDecorator->find($id);
    $author = $entityDecorator->getAuthor();
    $this->assertNotNull($author);
    $this->assertEquals($author->getId(), static::$admin_user->getId());
  }

  /*------------RepositoryDecorator event tests------------*/

  /*---onUnknownResult---*/

  public function testOnUnknownResultRoleAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnUnknownResultRoleDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $entities_count = $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getUnknownResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnUnknownResultFlagBypassAccessAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnUnknownResultFlagBypassAccessDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityNoBypassRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getUnknownResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnUnknownResultFlagHasAccountAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnUnknownResultFlagHasAccountDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getUnknownResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnUnknownResultFlagIsAuthorAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity(static::$authenticated_user);
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnUnknownResultFlagIsAuthorDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getUnknownResult();
    $this->assertEquals(1, count($entities));
  }

  /*---onSingleEntityResult---*/

  public function testOnSingleEntityResultRoleAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue($entity_found);
  }

  public function testOnSingleEntityResultRoleDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertFalse($entity_found);
    //Kolla att entiteten fortfarande finns i databasen
    $this->assertTrue((bool) $this->testEntityOperations->getSingleEntityResult($entityDecorator->getId()));
  }

  public function testOnSingleEntityResultFlagBypassAccessAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue($entity_found);
  }

  public function testOnSingleEntityResultFlagBypassAccessDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityNoBypassRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertFalse($entity_found);
    //Kolla att entiteten fortfarande finns i databasen
    $this->assertTrue((bool) $this->testEntityOperations->getSingleEntityResult($entityDecorator->getId()));
  }

  public function testOnSingleEntityResultFlagHasAccountAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue($entity_found);
  }

  public function testOnSingleEntityResultFlagHasAccountDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertFalse($entity_found);
    //Kolla att entiteten fortfarande finns i databasen
    $this->assertTrue((bool) $this->testEntityOperations->getSingleEntityResult($entityDecorator->getId()));
  }

  public function testOnSingleEntityResultFlagIsAuthorAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity(static::$authenticated_user);
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue($entity_found);
  }

  public function testOnSingleEntityResultFlagIsAuthorDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $entityDecorator = $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/find-single-entity-result/' . $entityDecorator->getId(), array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_found = $decoder->decode($response->getContent(), 'json');
    $this->assertFalse($entity_found);
    //Kolla att entiteten fortfarande finns i databasen
    $this->assertTrue((bool) $this->testEntityOperations->getSingleEntityResult($entityDecorator->getId()));
  }

  /*---onMultipleEntityResult---*/

  public function testOnMultipleEntityResultRoleAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnMultipleEntityResultRoleDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $entities_count = $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getMultipleEntityResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnMultipleEntityResultFlagBypassAccessAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnMultipleEntityResultFlagBypassAccessDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityNoBypassRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getMultipleEntityResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnMultipleEntityResultFlagHasAccountAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnMultipleEntityResultFlagHasAccountDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getMultipleEntityResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnMultipleEntityResultFlagIsAuthorAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity(static::$authenticated_user);
    $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnMultipleEntityResultFlagIsAuthorDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-multiple-entity-result', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getMultipleEntityResult();
    $this->assertEquals(1, count($entities));
  }

  /*---onBeforeCreate---*/

  public function testOnBeforeCreateRoleAllow() {
    $this->sendRequestAs('GET', '/test/create-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_created = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue((bool) $entity_created);
  }

  public function testOnBeforeCreateRoleDisallow() {
    $this->sendRequestAs('GET', '/test/create-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_created = $decoder->decode($response->getContent(), 'json');
    $this->assertFalse((bool) $entity_created);
  }

  public function testOnBeforeCreateFlagBypassAccessAllow() {
    $this->sendRequestAs('GET', '/test/create-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_created = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue((bool) $entity_created);
  }

  public function testOnBeforeCreateFlagBypassAccessDisallow() {
    $this->sendRequestAs('GET', '/test/create-entity', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_created = $decoder->decode($response->getContent(), 'json');
    $this->assertFalse((bool) $entity_created);
  }

  public function testOnBeforeCreateFlagHasAccountAllow() {
    $this->sendRequestAs('GET', '/test/create-entity', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_created = $decoder->decode($response->getContent(), 'json');
    $this->assertTrue((bool) $entity_created);
  }

  public function testOnBeforeCreateFlagHasAccountDisallow() {
    $this->sendRequestAs('GET', '/test/create-entity', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $decoder = new JsonDecode();
    $entity_created = $decoder->decode($response->getContent(), 'json');
    $this->assertFalse((bool) $entity_created);
  }

  /*---onLazyEntityCollectionResult---*/

  public function testOnLazyEntityCollectionResultRoleAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnLazyEntityCollectionResultRoleDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $entities_count = $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getLazyLoadedEntityResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnLazyEntityCollectionResultFlagBypassAccessAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnLazyEntityCollectionResultFlagBypassAccessDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityNoBypassRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getLazyLoadedEntityResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnLazyEntityCollectionResultFlagHasAccountAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnLazyEntityCollectionResultFlagHasAccountDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityHasAccountNoInterfaceRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getLazyLoadedEntityResult();
    $this->assertEquals(1, count($entities));
  }

  public function testOnLazyEntityCollectionResultFlagIsAuthorAllow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity(static::$authenticated_user);
    $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnLazyEntityCollectionResultFlagIsAuthorDisallow() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityRoleAuthorRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-entities-lazy', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
    //Kolla att entiteten fortfarande finns i databasen
    $entities = $this->testEntityOperations->getLazyLoadedEntityResult();
    $this->assertEquals(1, count($entities));
  }

  /*----------EntityDecorator event tests------------*/

  /*---onBeforeMethodCall getter---*/

  public function testOnBeforeMethodCallGetterRoleAllow() {
    $this->sendRequestAs('GET', '/test/call-method-getter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallGetterRoleDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-getter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeMethodCallGetterFlagBypassAccessAllow() {
    $this->sendRequestAs('GET', '/test/call-method-getter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallGetterFlagBypassAccessDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-getter', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeMethodCallGetterFlagHasAccountAllow() {
    $this->sendRequestAs('GET', '/test/call-method-getter', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallGetterFlagHasAccountDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-getter', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeMethodCallGetterFlagIsAuthorAllow() {
    $this->sendRequestAs('GET', '/test/call-method-getter-author', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallGetterFlagIsAuthorDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-getter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  /*---onBeforeMethodCall setter---*/

  public function testOnBeforeMethodCallSetterRoleAllow() {
    $this->sendRequestAs('GET', '/test/call-method-setter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallSetterRoleDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-setter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeMethodCallSetterFlagBypassAccessAllow() {
    $this->sendRequestAs('GET', '/test/call-method-setter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallSetterFlagBypassAccessDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-setter', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeMethodCallSetterFlagHasAccountAllow() {
    $this->sendRequestAs('GET', '/test/call-method-setter', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallSetterFlagHasAccountDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-setter', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeMethodCallSetterFlagIsAuthorAllow() {
    $this->sendRequestAs('GET', '/test/call-method-setter-author', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeMethodCallSetterFlagIsAuthorDisallow() {
    $this->sendRequestAs('GET', '/test/call-method-setter', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  /*---onBeforeSave create---*/

  public function testOnBeforeSaveCreateRoleAllow() {
    $this->sendRequestAs('GET', '/test/save-entity-create', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnBeforeSaveCreateRoleDisallow() {
    $this->sendRequestAs('GET', '/test/save-entity-create', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
  }

  public function testOnBeforeSaveCreateFlagBypassAccessAllow() {
    $this->sendRequestAs('GET', '/test/save-entity-create', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnBeforeSaveCreateFlagBypassAccessDisallow() {
    $this->sendRequestAs('GET', '/test/save-entity-create', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
  }

  public function testOnBeforeSaveCreateFlagHasAccountAllow() {
    $this->sendRequestAs('GET', '/test/save-entity-create', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnBeforeSaveCreateFlagHasAccountDisallow() {
    $this->sendRequestAs('GET', '/test/save-entity-create', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
  }

  /*---onBeforeSave update---*/

  public function testOnBeforeSaveUpdateRoleAllow() {
    $this->sendRequestAs('GET', '/test/save-entity-update', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeSaveUpdateRoleDisallow() {
    $this->sendRequestAs('GET', '/test/save-entity-update', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeSaveUpdateFlagBypassAccessAllow() {
    $this->sendRequestAs('GET', '/test/save-entity-update', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeSaveUpdateFlagBypassAccessDisallow() {
    $this->sendRequestAs('GET', '/test/save-entity-update', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeSaveUpdateFlagHasAccountAllow() {
    $this->sendRequestAs('GET', '/test/save-entity-update', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeSaveUpdateFlagHasAccountDisallow() {
    $this->sendRequestAs('GET', '/test/save-entity-update', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  public function testOnBeforeSaveUpdateFlagIsAuthorAllow() {
    $this->sendRequestAs('GET', '/test/save-entity-update-author', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertSame('test', $field_value);
  }

  public function testOnBeforeSaveUpdateFlagIsAuthorDisallow() {
    $this->sendRequestAs('GET', '/test/save-entity-update', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $field_value = $response->getContent();
    $this->assertNotSame('test', $field_value);
  }

  /*---onBeforeDelete---*/

  public function testOnBeforeDeleteRoleAllow() {
    $this->sendRequestAs('GET', '/test/delete-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
  }

  public function testOnBeforeDeleteRoleDisallow() {
    $this->sendRequestAs('GET', '/test/delete-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnBeforeDeleteFlagBypassAccessAllow() {
    $this->sendRequestAs('GET', '/test/delete-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
  }

  public function testOnBeforeDeleteFlagBypassAccessDisallow() {
    $this->sendRequestAs('GET', '/test/delete-entity', array('repository_decorator_service' => $this->load_services['testEntityNoBypassRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnBeforeDeleteFlagHasAccountAllow() {
    $this->sendRequestAs('GET', '/test/delete-entity', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
  }

  public function testOnBeforeDeleteFlagHasAccountDisallow() {
    $this->sendRequestAs('GET', '/test/delete-entity', array('repository_decorator_service' => $this->load_services['testEntityHasAccountNoInterfaceRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testOnBeforeDeleteFlagIsAuthorAllow() {
    $this->sendRequestAs('GET', '/test/delete-entity-author', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(0, $entities_count);
  }

  public function testOnBeforeDeleteFlagIsAuthorDisallow() {
    $this->sendRequestAs('GET', '/test/delete-entity', array('repository_decorator_service' => $this->load_services['testEntityRoleAuthorRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testPermissionsOverride() {
    $this->testEntityOperations->setRepositoryDecorator($this->testEntityOverriddenPermissionsRepositoryDecorator);
    $this->testEntityOperations->createTestEntity();
    $this->sendRequestAs('GET', '/test/count-unknown-result', array('repository_decorator_service' => $this->load_services['testEntityOverriddenPermissionsRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $entities_count = $response->getContent();
    $this->assertEquals(1, $entities_count);
  }

  public function testAvailableActionsAnonymous() {
    $this->sendRequestAs('GET', '/test/get-available-actions', array('repository_decorator_service' => $this->load_services['testEntityVariousPermissionsRepositoryDecorator']));
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $actions = json_decode($response->getContent(), true);
    $expected_actions = [
      'fields'=> [
        'id' => [
          'get' => 'get',
        ],
        'field3' => [
          'get' => 'get',
          'set' => 'set',
        ],
        'author' => [
          'get' => 'get',
        ],
      ],
    ];
    $this->assertSame($expected_actions, $actions);
  }

  public function testAvailableActionsAuthenticated() {
    $this->sendRequestAs('GET', '/test/get-available-actions', array('repository_decorator_service' => $this->load_services['testEntityVariousPermissionsRepositoryDecorator']), static::$authenticated_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $actions = json_decode($response->getContent(), true);
    $expected_actions = [
      'read' => 'read',
      'fields' => [
        'id' => [
          'get' => 'get',
        ],
        'field1' => [
          'get' => 'get',
        ],
        'field2' => [
          'set' => 'set',
        ],
        'field3' => [
          'get' => 'get',
          'set' => 'set',
        ],
        'author' => [
          'get' => 'get',
        ],
      ],
    ];
    $this->assertSame($expected_actions, $actions);
  }

  public function testAvailableActionsAdmin() {
    $this->sendRequestAs('GET', '/test/get-available-actions', array('repository_decorator_service' => $this->load_services['testEntityVariousPermissionsRepositoryDecorator']), static::$admin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $actions = json_decode($response->getContent(), true);
    $expected_actions = [
      'read' => 'read',
      'update' => 'update',
      'fields' => [
        'id' => [
          'get' => 'get',
        ],
        'field1' => [
          'get' => 'get',
          'set' => 'set',
        ],
        'field2' => [
          'set' => 'set',
        ],
        'field3' => [
          'get' => 'get',
          'set' => 'set',
        ],
        'author' => [
          'get' => 'get',
        ],
      ],
    ];
    $this->assertSame($expected_actions, $actions);
  }

  public function testAvailableActionsSuperadmin() {
    $this->sendRequestAs('GET', '/test/get-available-actions', array('repository_decorator_service' => $this->load_services['testEntityVariousPermissionsRepositoryDecorator']), static::$superadmin_user);
    $response = $this->client->getResponse();
    $this->assertEquals(200, $response->getStatusCode());
    $actions = json_decode($response->getContent(), true);
    $expected_actions = [
      'create' => 'create',
      'read' => 'read',
      'update' => 'update',
      'fields' => [
        'id' => [
          'get' => 'get',
        ],
        'field1' => [
          'get' => 'get',
          'set' => 'set',
        ],
        'field2' => [
          'get' => 'get',
          'set' => 'set',
        ],
        'field3' => [
          'get' => 'get',
          'set' => 'set',
        ],
        'author' => [
          'get' => 'get',
          'set' => 'set',
        ],
      ],
    ];
    $this->assertSame($expected_actions, $actions);
  }
}
