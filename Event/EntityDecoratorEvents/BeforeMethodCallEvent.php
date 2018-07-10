<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\EntityDecoratorEvents;

use Symfony\Component\EventDispatcher\Event;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * {@inheritdoc}
 */
class BeforeMethodCallEvent extends Event implements BeforeMethodCallEventInterface
{

  /**
   * @var object
   */
    protected $entity;

    /**
     * @var bool
     */
    protected $isNew;

    /**
     * @var Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var bool
     */
    protected $abort = false;

    /**
     * @internal
     *
     * @param object                                            $entity    The entity on which the call is made
     * @param bool                                              $isNew     A flag for the persistence status of the entity
     * @param Doctrine\Common\Persistence\Mapping\ClassMetadata $metadata  The metadata for the entity
     * @param string                                            $method    The method for the call
     * @param array                                             $arguments The arguments for the call
     */
    public function __construct($entity, bool $isNew, ClassMetadata $metadata, string $method, array $arguments)
    {
        $this->entity = $entity;
        $this->isNew = $isNew;
        $this->metadata = $metadata;
        $this->method = $method;
        $this->arguments = $arguments;
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
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): ClassMetadata
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbort(): bool
    {
        return $this->abort;
    }

    /**
     * {@inheritdoc}
     */
    public function setAbort(bool $abort)
    {
        $this->abort = $abort;
    }
}
