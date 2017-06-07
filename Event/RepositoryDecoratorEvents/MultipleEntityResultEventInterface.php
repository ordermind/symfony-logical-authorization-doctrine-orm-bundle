<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

/**
 * Event for multiple entity result
 *
 * This event is fired when a repository returns an array of entities.
 */
interface MultipleEntityResultEventInterface extends AbstractResultEventInterface
{
}
