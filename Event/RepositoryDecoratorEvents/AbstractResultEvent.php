<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

use Symfony\Component\EventDispatcher\Event;
use Doctrine\ORM\EntityRepository;

/**
 * {@inheritdoc}
 */
abstract class AbstractResultEvent extends Event implements AbstractResultEventInterface
{

  /**
   * @var Doctrine\ORM\EntityRepository
   */
    protected $repository;

  /**
   * @var string
   */
    protected $method;

  /**
   * @var array
   */
    protected $arguments;

  /**
   * @var mixed
   */
    protected $result;

  /**
   * @internal
   *
   * @param Doctrine\ORM\EntityRepository $repository The repository that returned the result
   * @param string                        $method     The method that was used for the call
   * @param array                         $arguments  The arguments for the call
   * @param mixed                         $result     The returned result
   */
    public function __construct(EntityRepository $repository, string $method, array $arguments, $result)
    {
        $this->repository = $repository;
        $this->method = $method;
        $this->arguments = $arguments;
        $this->result = $result;
    }

  /**
   * {@inheritdoc}
   */
    public function getRepository(): EntityRepository
    {
        return $this->repository;
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
    public function getResult()
    {
        return $this->result;
    }

  /**
   * {@inheritdoc}
   */
    public function setResult($result)
    {
        $this->result = $result;
    }
}
