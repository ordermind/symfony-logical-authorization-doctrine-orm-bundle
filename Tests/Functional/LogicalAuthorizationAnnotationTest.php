<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Functional;

class LogicalAuthorizationAnnotationTest extends LogicalAuthorizationBase
{
  /**
   * This method is run before each public test method
   */
  protected function setUp() {
    $this->load_services = array(
      'testEntityRoleAuthorRepositoryDecorator' => 'repository.test_entity_roleauthor_annotation',
      'testEntityHasAccountNoInterfaceRepositoryDecorator' => 'repository.test_entity_hasaccount_annotation',
      'testEntityNoBypassRepositoryDecorator' => 'repository.test_entity_nobypass_annotation',
      'testEntityOverriddenPermissionsRepositoryDecorator' => 'repository.test_entity_overridden_permissions_annotation',
      'testEntityVariousPermissionsRepositoryDecorator' => 'repository.test_entity_various_permissions_annotation',
    );

    parent::setUp();
  }
}
