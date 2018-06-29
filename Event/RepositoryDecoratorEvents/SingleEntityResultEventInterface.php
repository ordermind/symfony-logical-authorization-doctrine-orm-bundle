<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

/**
 * Event for single entity result
 *
 * This event is fired when a repository returns a single entity.
 */
interface SingleEntityResultEventInterface extends AbstractResultEventInterface
{
}
