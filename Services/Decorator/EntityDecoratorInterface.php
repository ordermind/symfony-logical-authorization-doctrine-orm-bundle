<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator;

/**
 * Decorator for entity
 *
 * Wraps a entity and monitors all communication with it. It also provides a few handy methods.
 */
interface EntityDecoratorInterface
{

  /**
   * Gets the entity that is wrapped by this decorator
   *
   * @return mixed
   */
    public function getEntity();

  /**
   * Overrides the entity manager that is used in this decorator
   *
   * @param Doctrine\Common\Persistence\ObjectManager $em The entity manager that is to be used in this decorator
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecorator
   */
    public function setEntityManager(\Doctrine\Common\Persistence\ObjectManager $em);

  /**
   * Gets the entity manager that is used in this decorator
   *
   * @return Doctrine\Common\Persistence\ObjectManager
   */
    public function getEntityManager();

  /**
   * Gets all available entity and field actions on this entity for a given user
   *
   * This method is primarily meant to facilitate client-side authorization by providing a map of all available actions on a entity. The map has the structure ['entity_action1' => 'entity_action1', 'entity_action3' => 'entity_action3', 'fields' => ['field_name1' => ['field_action1' => 'field_action1']]].
   *
   * @param object|string $user (optional) Either a user object or a string to signify an anonymous user. If no user is supplied, the current user will be used.
   * @param array $entity_actions (optional) A list of entity actions that should be evaluated. Default actions are the standard CRUD actions.
   * @param array $field_actions (optional) A list of field actions that should be evaluated. Default actions are 'get' and 'set'.
   *
   * @return array A map of available actions
   */
    public function getAvailableActions($user = null, $entity_actions = array('create', 'read', 'update', 'delete'), $field_actions = array('get', 'set'));

  /**
   * Returns TRUE if the entity is new. Returns FALSE if the entity is persisted.
   *
   * @return bool
   */
    public function isNew();

  /**
   * Saves the wrapped entity
   *
   * Before the save is performed, the decorator fires the event 'ordermind_logical_authorization_doctrine_orm.event.entity_decorator.before_save' and passes Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeSaveEvent.
   * If the abort flag in the event is then found to be TRUE the entity is not saved and the method returns FALSE.
   * If the save succeeds the method returns the entity decorator.
   *
   * @param bool $andFlush (optional) Determines whether the entity decorator should be flushed after persisting the entity. Default value is TRUE.
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|FALSE
   */
    public function save($andFlush = true);

  /**
   * Deletes the wrapped entity
   *
   * Before the deletion is performed, the decorator fires the event 'ordermind_logical_authorization_doctrine_orm.event.entity_decorator.before_delete' and passes Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeDeleteEvent.
   * If the abort flag in the event is then found to be TRUE the entity is not deleted and the method returns FALSE.
   * If the deletion succeeds the method returns the entity decorator.
   *
   * @param bool $andFlush (optional) Determines whether the entity decorator should be flushed after removing the entity. Default value is TRUE.
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|FALSE
   */
    public function delete($andFlush = true);
}