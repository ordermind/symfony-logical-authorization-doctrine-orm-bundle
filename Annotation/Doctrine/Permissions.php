<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Annotation\Doctrine;

/**
 * @Annotation
 */
class Permissions {
  /**
   * @var mixed
   */
  protected $permissions;

  public function __construct(array $data) {
    $this->permissions = $data['value'];
  }

  /**
   * Gets the permission tree for this entity
   *
   * @return array|string|bool The permission tree for this entity
   */
  public function getPermissions() {
    return $this->permissions;
  }
}
