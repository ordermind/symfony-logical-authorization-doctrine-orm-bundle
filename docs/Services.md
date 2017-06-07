# API Documentation

## Table of Contents

* [ManagerRegistry](#managerregistry)
    * [getManagerForClass](#getmanagerforclass)
* [ModelDecorator](#modelmanager)
    * [getModel](#getmodel)
    * [setObjectManager](#setobjectmanager)
    * [getObjectManager](#getobjectmanager)
    * [isNew](#isnew)
    * [save](#save)
    * [delete](#delete)
    * [__call](#__call)
* [ModelDecoratorFactory](#modelmanagerfactory)
    * [getModelDecorator](#getmodelmanager)
* [RepositoryDecorator](#repositorymanager)
    * [getClassName](#getclassname)
    * [setObjectManager](#setobjectmanager-1)
    * [getObjectManager](#getobjectmanager-1)
    * [getRepository](#getrepository)
    * [find](#find)
    * [findAll](#findall)
    * [findBy](#findby)
    * [findOneBy](#findoneby)
    * [matching](#matching)
    * [create](#create)
    * [wrapModels](#wrapmodels)
    * [wrapModel](#wrapmodel)
    * [__call](#__call-1)
* [RepositoryDecoratorFactory](#repositorymanagerfactory)
    * [setManagerRegistry](#setmanagerregistry)
    * [setModelDecoratorFactory](#setmodelmanagerfactory)
    * [setDispatcher](#setdispatcher)
    * [getRepositoryDecorator](#getrepositorymanager)

## ManagerRegistry

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Services\ManagerRegistry
* This class implements: \Ordermind\DoctrineDecoratorBundle\Services\ManagerRegistryInterface



### getManagerForClass

Gets the object manager associated with a given class.

```php
ManagerRegistry::getManagerForClass( string $class )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string** | The class that is associated with an object manager |




---

## ModelDecorator

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecorator
* This class implements: \Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface



### getModel

Gets the model that is wrapped by this manager

```php
ModelDecorator::getModel(  ): mixed
```







---


### setObjectManager

Overrides the object manager that is used in this manager

```php
ModelDecorator::setObjectManager( \Doctrine\Common\Persistence\ObjectManager $om ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecorator
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$om` | **\Doctrine\Common\Persistence\ObjectManager** | The object manager that is to be used in this manager |




---


### getObjectManager

Gets the object manager that is used in this manager

```php
ModelDecorator::getObjectManager(  ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Doctrine\Common\Persistence\ObjectManager
```







---


### isNew

Returns TRUE if the model is new. Returns FALSE if the model is persisted.

```php
ModelDecorator::isNew(  ): boolean
```







---


### save

Saves the wrapped model

```php
ModelDecorator::save( boolean $andFlush = true ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface|FALSE
```

Before the save is performed, the manager fires the event 'ordermind_doctrine_decorator.event.model_decorator.before_save' and passes Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeSaveEvent.
If the abort flag in the event is then found to be TRUE the model is not saved and the method returns FALSE.
If the save succeeds the method returns the model decorator.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$andFlush` | **boolean** | (optional) Determines whether the model decorator should be flushed after persisting the model. Default value is TRUE. |




---


### delete

Deletes the wrapped model

```php
ModelDecorator::delete( boolean $andFlush = true ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface|FALSE
```

Before the deletion is performed, the manager fires the event 'ordermind_doctrine_decorator.event.model_decorator.before_delete' and passes Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeDeleteEvent.
If the abort flag in the event is then found to be TRUE the model is not deleted and the method returns FALSE.
If the deletion succeeds the method returns the model decorator.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$andFlush` | **boolean** | (optional) Determines whether the model decorator should be flushed after removing the model. Default value is TRUE. |




---


### __call

Catch-all for method calls on the model

```php
ModelDecorator::__call( string $method, array $arguments ): mixed|NULL
```

Traps all method calls on the model and fires the event 'ordermind_doctrine_decorator.event.model_decorator.before_method_call' passing Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeMethodCallEvent.
If the abort flag in the event is then found to be TRUE the call is never transmitted to the model and instead the method returns NULL.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$method` | **string** | The method used for the call |
| `$arguments` | **array** | The arguments used for the call |




---

## ModelDecoratorFactory

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Services\Factory\ModelDecoratorFactory
* This class implements: \Ordermind\DoctrineDecoratorBundle\Services\Factory\ModelDecoratorFactoryInterface



### getModelDecorator

Gets a new model decorator

```php
ModelDecoratorFactory::getModelDecorator( \Doctrine\Common\Persistence\ObjectManager $om, \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher, mixed $model ): \Ordermind\DoctrineDecoratorBundle\Services\Factory\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$om` | **\Doctrine\Common\Persistence\ObjectManager** | The object manager to use for the new model decorator |
| `$dispatcher` | **\Symfony\Component\EventDispatcher\EventDispatcherInterface** | The event dispatcher to use for the new model decorator |
| `$model` | **mixed** | The model to wrap in the manager |




---

## RepositoryDecorator

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Services\Decorator\RepositoryDecorator
* This class implements: \Ordermind\DoctrineDecoratorBundle\Services\Decorator\RepositoryDecoratorInterface



### getClassName

Gets the model class name that is associated with this manager

```php
RepositoryDecorator::getClassName(  ): string
```







---


### setObjectManager

Overrides the object manager that is used in this manager

```php
RepositoryDecorator::setObjectManager( \Doctrine\Common\Persistence\ObjectManager $om ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\RepositoryDecorator
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$om` | **\Doctrine\Common\Persistence\ObjectManager** | The object manager that is to be used in this manager |




---


### getObjectManager

Gets the object manager that is used in this manager

```php
RepositoryDecorator::getObjectManager(  ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Doctrine\Common\Persistence\ObjectManager
```







---


### getRepository

Gets the repository that is wrapped by this manager

```php
RepositoryDecorator::getRepository(  ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Doctrine\Common\Persistence\ObjectRepository
```







---


### find

Finds a model by its identifier

```php
RepositoryDecorator::find( mixed $id, integer $lockMode = null, integer $lockVersion = null ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface|NULL
```

This method forwards the call to the managed repository and fires the event 'ordermind_doctrine_decorator.event.repository_decorator.single_model_result' passing Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\SingleModelResultEvent, allowing tampering with the result before returning it to the caller. If no result is found, NULL is returned.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$id` | **mixed** | The identifier |
| `$lockMode` | **integer** | (optional) One of the constants in either \Doctrine\DBAL\LockMode::* (for ORM) or \Doctrine\ODM\MongoDB\LockMode::* (for ODM) if a specific lock mode should be used during the search. Default value is NULL. |
| `$lockVersion` | **integer** | (optional) The lock version. Default value is NULL. |




---


### findAll

Finds all models for this repository decorator

```php
RepositoryDecorator::findAll(  ): array
```

This method forwards the call to the managed repository and fires the event 'ordermind_doctrine_decorator.event.repository_decorator.multiple_model_result' passing Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\MultipleModelResultEvent, allowing tampering with the result before returning it to the caller.





---


### findBy

Finds models for this repository decorator filtered by a set of criteria

```php
RepositoryDecorator::findBy( array $criteria, array $sort = null, array $limit = null, array $skip = null ): array
```

This method forwards the call to the managed repository and fires the event 'ordermind_doctrine_decorator.event.repository_decorator.multiple_model_result' passing Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\MultipleModelResultEvent, allowing tampering with the result before returning it to the caller.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$criteria` | **array** | Query criteria |
| `$sort` | **array** | (optional) Sort array for Cursor::sort(). Default value is NULL. |
| `$limit` | **array** | (optional) Limit for Cursor::limit(). Default value is NULL. |
| `$skip` | **array** | (optional) Skip for Cursor::skip(). Default value is NULL. |




---


### findOneBy

Finds a model for this repository decorator filtered by a set of criteria

```php
RepositoryDecorator::findOneBy( array $criteria ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface|NULL
```

This method forwards the call to the managed repository and fires the event 'ordermind_doctrine_decorator.event.repository_decorator.single_model_result' passing Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\SingleModelResultEvent, allowing tampering with the result before returning it to the caller. If no result is found, NULL is returned.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$criteria` | **array** | Query criteria |




---


### matching

Finds models for this repository decorator filtered by a set of criteria

```php
RepositoryDecorator::matching( \Doctrine\Common\Collections\Criteria $criteria ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Doctrine\Common\Collections\ArrayCollection
```

This method forwards the call to the managed repository and fires the event 'ordermind_doctrine_decorator.event.repository_decorator.lazy_model_collection_result' passing Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\LazyModelCollectionResultEvent, allowing tampering with the result before returning it to the caller.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$criteria` | **\Doctrine\Common\Collections\Criteria** | Query criteria |




---


### create

Creates a new model decorator

```php
RepositoryDecorator::create(  ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface|NULL
```

Before the creation is performed, the manager fires the event 'ordermind_doctrine_decorator.event.repository_decorator.before_create' passing Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\BeforeCreateEvent.
If the abort flag in the event is then found to be TRUE the model is not created and the method returns NULL.
If the creation succeeds the method returns the created model decorator.
Any parameters that are provided to this method will be passed on to the model constructor.





---


### wrapModels

Wraps an array of models in model decorators

```php
RepositoryDecorator::wrapModels( array $models ): array
```

This method runs wrapModel() for each of the models in the array.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$models` | **array** | The models to be wrapped in model decorators |




---


### wrapModel

Wraps a model in a model decorator

```php
RepositoryDecorator::wrapModel( mixed $model ): \Ordermind\DoctrineDecoratorBundle\Services\Decorator\Ordermind\DoctrineDecoratorBundle\Services\Decorator\ModelDecoratorInterface|mixed
```

If the class of the supplied model is not the same as the class from getClassName() the model is not wrapped but returned as is.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$model` | **mixed** | The model to be wrapped in a model decorator |




---


### __call

Catch-all for method calls on the repository

```php
RepositoryDecorator::__call( string $method, array $arguments ): mixed
```

Traps all method calls on the repository and fires the event 'ordermind_doctrine_decorator.event.repository_decorator.unknown_result' passing Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\UnknownResultEvent, allowing tampering with the result before returning it to the caller.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$method` | **string** | The method used for the call |
| `$arguments` | **array** | The arguments used for the call |




---

## RepositoryDecoratorFactory

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Services\Factory\RepositoryDecoratorFactory
* This class implements: \Ordermind\DoctrineDecoratorBundle\Services\Factory\RepositoryDecoratorFactoryInterface



### setManagerRegistry

Sets the manager registry

```php
RepositoryDecoratorFactory::setManagerRegistry( \Ordermind\DoctrineDecoratorBundle\Services\ManagerRegistry $managerRegistry )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$managerRegistry` | **\Ordermind\DoctrineDecoratorBundle\Services\ManagerRegistry** | The manager registry to use for this repository decorator factory |




---


### setModelDecoratorFactory

Sets the model decorator factory

```php
RepositoryDecoratorFactory::setModelDecoratorFactory( \Ordermind\DoctrineDecoratorBundle\Services\Factory\ModelDecoratorFactoryInterface $modelDecoratorFactory )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modelDecoratorFactory` | **\Ordermind\DoctrineDecoratorBundle\Services\Factory\ModelDecoratorFactoryInterface** | The model decorator factory to use for this repository decorator factory |




---


### setDispatcher

Sets the event dispatcher

```php
RepositoryDecoratorFactory::setDispatcher( \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dispatcher` | **\Symfony\Component\EventDispatcher\EventDispatcherInterface** | The event dispatcher to use for this repository decorator factory |




---


### getRepositoryDecorator

Gets a new repository decorator

```php
RepositoryDecoratorFactory::getRepositoryDecorator( string $class )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string** | The model class to use for the new repository decorator |




---



--------
> This document was automatically generated from source code comments on 2017-04-17 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
