<a href="https://travis-ci.org/Ordermind/doctrine-decorator-bundle" target="_blank"><img src="https://travis-ci.org/Ordermind/doctrine-decorator-bundle.svg?branch=master" /></a> (Please read [this note](#compatibility) on why tests currently fail)
# doctrine-decorator-bundle

This is a Symfony bundle that provides decorators for repositories and entities/documents (henceforth referred to as **models**) in order to facilitate container awareness and convenience methods as well as events. It also encourages a good application structure in accordance with [this blog post](http://php-and-symfony.matthiasnoback.nl/2014/05/inject-a-repository-instead-of-an-entity-manager).

## Getting started

### Compatibility

This bundle has been tested on PHP 5.4 and higher as well as HHVM. Both Symfony 2.8 and Symfony 3.x are supported. For Doctrine both ORM and ODM (with MongoDB) are supported, however currently there is an issue with Doctrine's MongoDB dependencies which makes the ODM tests fail on PHP 7 and HHVM. Read more [here](https://github.com/doctrine/mongodb/issues/239).

### Installation

#### 1. Install with composer

`composer require ordermind/doctrine-decorator-bundle`

#### 2. Add the bundle to app/AppKernel.php

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Ordermind\DoctrineDecoratorBundle\OrdermindDoctrineDecoratorBundle(),
        // ...
    );
}
```

### Configuration

#### Create services for your repository decorators

You should do this for every model that you have.

ORM example:
```
# file: app/config/services.yml
services:
    my_repository_decorator:
        class: Ordermind\DoctrineDecoratorBundle\Services\Decorator\RepositoryDecorator
        factory: ['@ordermind_doctrine_decorator.service.repository_decorator_factory', getRepositoryDecorator]
        arguments:
            - AppBundle\Entity\MyEntity
```

ODM example:
```
# file: app/config/services.yml
services:
    my_repository_decorator:
        class: Ordermind\DoctrineDecoratorBundle\Services\Decorator\RepositoryDecorator
        factory: ['@ordermind_doctrine_decorator.service.repository_decorator_factory', getRepositoryDecorator]
        arguments:
            - AppBundle\Document\MyDocument
```

### Usage

#### 1. Inject the repository decorator instead of Doctrine's entity/document manager

Example from dependency injection (recommended):
```
# file: app/config/services.yml
services:
    my_service:
        class: AppBundle\Services\MyService
        arguments:
            - ['@my_repository_decorator']
```

Example from controller (discouraged):
```php
$repositoryDecorator = $this->get('my_repository_decorator');
```

#### 2. Create a model decorator instead of creating an entity/document

When the first two steps are done, you have full access to the repository decorator. You can use this just like a regular repository but it also provides some convenience methods which are detailed in the [API documentation](#api-documentation). You also get access to a number of events both in the repository decorator and the model decorator that allow you to interrupt or modify communications with the repository and model, respectively.

The repository decorator will in almost all cases return model decorators instead of models in the result. It also has the capability of creating a new model decorator. There are two main ways of doing this:

##### Use RepositoryDecorator::create() to create a completely new model instance with the model decorator

Example:
```php
$modelDecorator = $repositoryDecorator->create();
```

Example with constructor parameters:
```php
$modelDecorator = $repositoryDecorator->create($arg1, $arg2);
```

##### Use RepositoryDecorator::wrapModel() to create a decorator from an existing model

Example:
```php
$modelDecorator = $repositoryDecorator->wrapModel($model);
```

#### 3. Use the decorators as proxies for all calls to the repository and model

By treating the model decorator as an enhanced model and the repository decorator as an enhanced repository and always using them instead of calling the repository or model directly, you will be able to maximize the benefits of this bundle. They will then be able to fire events that allow you to control if and how the calls are passed on to the underlying repository/model. For more information, please consult the [API documentation](#api-documentation).

Instead of doing this:
```php
$result = $repository->findAll(); //Returns all models in this repository
```

Do this:
```php
$result = $repositoryDecorator->findAll(); //Returns all models in this repository wrapped by modelDecorators and allows you to tamper with the result before it is returned.
```

Similarly, instead of doing this:
```php
$value = $model->getMyProperty(); //Returns the value of $myProperty in $model
```

Do this:
```php
$value = $modelDecorator->getMyProperty(); //Still returns the value of $myProperty in $model but allows you to abort the call before it is passed on to the model.
```

## API Documentation

- [Services overview](docs/Services.md)
- [Events overview](docs/Events.md) NOTE: This didn't come out so great due to phpdoc issues. When in doubt, please check the interface file for the event you're interested in for complete documentation.
