<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator;

use Ordermind\LogicalAuthorizationBundle\Interfaces\ModelDecoratorInterface;

/**
 * Decorator for entity
 *
 * Wraps a entity and monitors all communication with it. It also provides a few handy methods.
 */
interface EntityDecoratorInterface extends ModelDecoratorInterface
{

  /**
   * Gets the entity that is wrapped by this decorator
   *
   * @return object
   */
    public function getEntity();

    /**
     * Overrides the entity manager that is used in this decorator
     *
     * @param Doctrine\ORM\EntityManager $em The entity manager that is to be used in this decorator
     *
     * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecorator
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em);

    /**
     * Gets the entity manager that is used in this decorator
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager(): \Doctrine\ORM\EntityManager;

    /**
     * Gets all available entity and field actions on this entity for a given user
     *
     * This method is primarily meant to facilitate client-side authorization by providing a map of all available actions on a entity. The map has the structure ['entity_action1' => 'entity_action1', 'entity_action3' => 'entity_action3', 'fields' => ['field_name1' => ['field_action1' => 'field_action1']]].
     *
     * @param object|string $user          (optional) Either a user object or a string to signify an anonymous user. If no user is supplied, the current user will be used.
     * @param array         $entityActions (optional) A list of entity actions that should be evaluated. Default actions are the standard CRUD actions.
     * @param array         $fieldActions  (optional) A list of field actions that should be evaluated. Default actions are 'get' and 'set'.
     *
     * @return array A map of available actions
     */
    public function getAvailableActions($user = null, array $entityActions = ['create', 'read', 'update', 'delete'], array $fieldActions = ['get', 'set']);

    /**
     * Returns TRUE if the entity is new. Returns FALSE if the entity is persisted.
     *
     * @return bool
     */
    public function isNew(): bool;

    /**
     * Saves the wrapped entity
     *
     * Before the save is performed, the decorator fires the event 'logauth_doctrine_orm.event.entity_decorator.before_save' and passes Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeSaveEvent.
     * If the abort flag in the event is then found to be TRUE the entity is not saved and the method returns FALSE.
     * If the save succeeds the method returns the entity decorator.
     *
     * @param bool $andFlush (optional) Determines whether the entity decorator should be flushed after persisting the entity. Default value is TRUE.
     *
     * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|FALSE
     */
    public function save(bool $andFlush = true);

    /**
     * Deletes the wrapped entity
     *
     * Before the deletion is performed, the decorator fires the event 'logauth_doctrine_orm.event.entity_decorator.before_delete' and passes Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeDeleteEvent.
     * If the abort flag in the event is then found to be TRUE the entity is not deleted and the method returns FALSE.
     * If the deletion succeeds the method returns the entity decorator.
     *
     * @param bool $andFlush (optional) Determines whether the entity decorator should be flushed after removing the entity. Default value is TRUE.
     *
     * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|FALSE
     */
    public function delete(bool $andFlush = true);
}
