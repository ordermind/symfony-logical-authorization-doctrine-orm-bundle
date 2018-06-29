<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Functional;

class LogicalAuthorizationXMLTest extends LogicalAuthorizationBase
{
  /**
   * This method is run before each public test method
   */
  protected function setUp() {
    $this->load_services = array(
      'testEntityRoleAuthorRepositoryDecorator' => 'repository.test_entity_roleauthor_xml',
      'testEntityHasAccountNoInterfaceRepositoryDecorator' => 'repository.test_entity_hasaccount_xml',
      'testEntityNoBypassRepositoryDecorator' => 'repository.test_entity_nobypass_xml',
      'testEntityOverriddenPermissionsRepositoryDecorator' => 'repository.test_entity_overridden_permissions_xml',
      'testEntityVariousPermissionsRepositoryDecorator' => 'repository.test_entity_various_permissions_xml',
    );

    parent::setUp();
  }
}
