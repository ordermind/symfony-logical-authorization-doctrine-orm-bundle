<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Functional;

class LogicalAuthorizationYMLTest extends LogicalAuthorizationBase
{
    /**
     * This method is run before each public test method
     */
    protected function setUp()
    {
        $this->load_services = array(
      'testEntityRoleAuthorRepositoryDecorator' => 'repository.test_entity_roleauthor_yml',
      'testEntityHasAccountNoInterfaceRepositoryDecorator' => 'repository.test_entity_hasaccount_yml',
      'testEntityNoBypassRepositoryDecorator' => 'repository.test_entity_nobypass_yml',
      'testEntityOverriddenPermissionsRepositoryDecorator' => 'repository.test_entity_overridden_permissions_yml',
      'testEntityVariousPermissionsRepositoryDecorator' => 'repository.test_entity_various_permissions_yml',
    );

        parent::setUp();
    }
}
