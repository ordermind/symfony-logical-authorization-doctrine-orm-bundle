<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Collections\Selectable;

/**
 * Decorator for repository
 *
 * Wraps a repository and monitors all communication with it. It also provides a few handy methods.
 */
interface RepositoryDecoratorInterface extends ObjectRepository, Selectable
{

  /**
   * Gets the entity class name that is associated with this decorator
   *
   * @return string
   */
    public function getClassName();

  /**
   * Overrides the entity manager that is used in this decorator
   *
   * @param Doctrine\Common\Persistence\ObjectManager $em The entity manager that is to be used in this decorator
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\RepositoryDecorator
   */
    public function setEntityManager(\Doctrine\Common\Persistence\ObjectManager $em);

  /**
   * Gets the entity manager that is used in this decorator
   *
   * @return Doctrine\Common\Persistence\ObjectManager
   */
    public function getEntityManager();

  /**
   * Gets the repository that is wrapped by this decorator
   *
   * @return Doctrine\Common\Persistence\ObjectRepository
   */
    public function getRepository();

  /**
   * Finds a entity by its identifier
   *
   * This method forwards the call to the wrapped repository and fires the event 'ordermind_logical_authorization_doctrine_orm.event.repository_decorator.single_entity_result' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\SingleEntityResultEvent, allowing tampering with the result before returning it to the caller. If no result is found, NULL is returned.
   *
   * @param mixed   $id          The identifier
   * @param integer $lockMode    (optional) One of the constants in either \Doctrine\DBAL\LockMode::* (for ORM) or \Doctrine\ODM\MongoDB\LockMode::* (for ODM) if a specific lock mode should be used during the search. Default value is NULL.
   * @param integer $lockVersion (optional) The lock version. Default value is NULL.
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|NULL
   */
    public function find($id, $lockMode = null, $lockVersion = null);

  /**
   * Finds all entities for this repository decorator
   *
   * This method forwards the call to the wrapped repository and fires the event 'ordermind_logical_authorization_doctrine_orm.event.repository_decorator.multiple_entity_result' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\MultipleEntityResultEvent, allowing tampering with the result before returning it to the caller.
   *
   * @return array
   */
    public function findAll();

  /**
   * Finds entities for this repository decorator filtered by a set of criteria
   *
   * This method forwards the call to the managed repository and fires the event 'ordermind_logical_authorization_doctrine_orm.event.repository_decorator.multiple_entity_result' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\MultipleEntityResultEvent, allowing tampering with the result before returning it to the caller.
   *
   * @param array $criteria Query criteria
   * @param array $sort     (optional) Sort array for Cursor::sort(). Default value is NULL.
   * @param array $limit    (optional) Limit for Cursor::limit(). Default value is NULL.
   * @param array $skip     (optional) Skip for Cursor::skip(). Default value is NULL.
   *
   * @return array
   */
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null);

  /**
   * Finds a entity for this repository decorator filtered by a set of criteria
   *
   * This method forwards the call to the managed repository and fires the event 'ordermind_logical_authorization_doctrine_orm.event.repository_decorator.single_entity_result' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\SingleEntityResultEvent, allowing tampering with the result before returning it to the caller. If no result is found, NULL is returned.
   *
   * @param array $criteria Query criteria
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|NULL
   */
    public function findOneBy(array $criteria);

  /**
   * Finds entities for this repository decorator filtered by a set of criteria
   *
   * This method forwards the call to the managed repository and fires the event 'ordermind_logical_authorization_doctrine_orm.event.repository_decorator.lazy_entity_collection_result' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\LazyEntityCollectionResultEvent, allowing tampering with the result before returning it to the caller.
   *
   * @param Doctrine\Common\Collections\Criteria $criteria Query criteria
   *
   * @return Doctrine\Common\Collections\ArrayCollection
   */
    public function matching(\Doctrine\Common\Collections\Criteria $criteria);

  /**
   * Creates a new entity decorator
   *
   * Before the creation is performed, the decorator fires the event 'ordermind_logical_authorization_doctrine_orm.event.repository_decorator.before_create' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents\BeforeCreateEvent.
   * If the abort flag in the event is then found to be TRUE the entity is not created and the method returns NULL.
   * Any parameters that are provided to this method will be passed on to the entity constructor.
   * If the entity implements Ordermind\LogicalAuthorizationBundle\Interfaces\ModelInterface and the current user implements Ordermind\LogicalAuthorizationBundle\Interfaces\UserInterface, it will automatically set the entity's author to the current user.
   * If the current user is not authorized to create the target entity, it will not be created and NULL will be returned. Otherwise the created entity decorator will be returned.
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|NULL
   */
    public function create();

  /**
   * Wraps an array of entities in entity decorators
   *
   * This method runs wrapEntity() for each of the entities in the array.
   *
   * @param array $entities The entities to be wrapped in entity decorators
   *
   * @return array
   */
    public function wrapEntities($entities);

  /**
   * Wraps a entity in a entity decorator
   *
   * If the class of the supplied entity is not the same as the class from getClassName() the entity is not wrapped but returned as is.
   *
   * @param mixed $entity The entity to be wrapped in a entity decorator
   *
   * @return Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface|mixed
   */
    public function wrapEntity($entity);
}
