<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Event\RepositoryDecoratorEvents;

/**
 * Event for lazy-loaded collection result
 *
 * This event is fired when a repository returns a lazy-loaded collection, notably when using the matching() method. Please be careful with looping through the collection as the reason for such a collection is to avoid loading all the entities inside of it.
 */
interface LazyEntityCollectionResultEventInterface extends AbstractResultEventInterface
{
}
