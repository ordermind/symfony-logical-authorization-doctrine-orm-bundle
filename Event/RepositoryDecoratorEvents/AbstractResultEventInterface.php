<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

/**
 * Base result event
 *
 * This event is fired from a RepositoryDecorator to give you the opportunity to modify the returned result from a repository method
 */
interface AbstractResultEventInterface
{

  /**
   * Gets the repository which returned the result
   *
   * @return Doctrine\ORM\EntityRepository
   */
    public function getRepository(): \Doctrine\ORM\EntityRepository;

  /**
   * Gets the method that was used for the call
   *
   * @return string
   */
    public function getMethod(): string;

  /**
   * Gets the arguments for the call
   *
   * @return array
   */
    public function getArguments(): array;

  /**
   * Gets the returned result
   *
   * @return mixed
   */
    public function getResult();

  /**
   * Sets a modified result
   *
   * @param mixed $result The modified result
   */
    public function setResult($result);
}
