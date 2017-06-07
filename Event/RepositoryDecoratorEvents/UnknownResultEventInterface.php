<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

/**
 * Event for unknown result
 *
 * This event is fired when a repository returns a result from a custom method.
 */
interface UnknownResultEventInterface extends AbstractResultEventInterface
{
}
