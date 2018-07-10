<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;
use Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeMethodCallEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeSaveEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeDeleteEvent;

/**
 * {@inheritdoc}
 */
class EntityDecorator implements EntityDecoratorInterface
{

  /**
   * @var Doctrine\ORM\EntityManager
   */
    protected $em;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface
     */
    protected $laModel;

    /**
     * @var object
     */
    protected $entity;

    /**
     * @internal
     *
     * @param Doctrine\ORM\EntityManager                                                       $em         The entity manager to use in this decorator
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface                       $dispatcher The event dispatcher to use in this decorator
     * @param Ordermind\LogicalAuthorizationBundle\Services\LogicalAuthorizationModelInterface $laModel    LogicalAuthorizationEntity service
     * @param object                                                                           $entity     The entity to wrap in this decorator
     */
    public function __construct(EntityManager $em, EventDispatcherInterface $dispatcher, LogicalAuthorizationModelInterface $laModel, $entity)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->laModel = $laModel;
        $this->entity = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->getEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableActions($user = null, array $entityActions = ['create', 'read', 'update', 'delete'], array $fieldActions = ['get', 'set'])
    {
        return $this->laModel->getAvailableActions($this->getEntity(), $entityActions, $fieldActions, $user);
    }

    /**
     * {@inheritdoc}
     */
    public function isNew(): bool
    {
        $em = $this->getEntityManager();
        $entity = $this->getEntity();

        return !$em->contains($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function save(bool $andFlush = true)
    {
        $entity = $this->getEntity();
        $event = new BeforeSaveEvent($entity, $this->isNew());
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.entity_decorator.before_save', $event);
        if ($event->getAbort()) {
            return false;
        }

        $em = $this->getEntityManager();
        $em->persist($entity);
        if ($andFlush) {
            $em->flush();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(bool $andFlush = true)
    {
        $entity = $this->getEntity();
        $event = new BeforeDeleteEvent($entity, $this->isNew());
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.entity_decorator.before_delete', $event);
        if ($event->getAbort()) {
            return false;
        }

        $em = $this->getEntityManager();
        $em->remove($entity);
        if ($andFlush) {
            $em->flush();
        }

        return $this;
    }

    /**
     * Catch-all for method calls on the entity
     *
     * Traps all method calls on the entity and fires the event 'logauth_doctrine_orm.event.entity_decorator.before_method_call' passing Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents\BeforeMethodCallEvent.
     * If the abort flag in the event is then found to be TRUE the call is never transmitted to the entity and instead the method returns NULL.
     *
     * @param string $method    The method used for the call
     * @param array  $arguments The arguments used for the call
     *
     * @return mixed|NULL
     */
    public function __call(string $method, array $arguments)
    {
        $em = $this->getEntityManager();
        $entity = $this->getEntity();
        $metadata = $em->getClassMetadata(get_class($entity));
        $event = new BeforeMethodCallEvent($entity, $this->isNew(), $metadata, $method, $arguments);
        $dispatcher = $this->getDispatcher();
        $dispatcher->dispatch('logauth_doctrine_orm.event.entity_decorator.before_method_call', $event);
        if ($event->getAbort()) {
            return null;
        }

        $result = call_user_func_array([$entity, $method], $arguments);

        return $result;
    }

    /**
     * @internal
     *
     * @return Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }
}
