# API Documentation

## Table of Contents

* [BeforeCreateEvent](#beforecreateevent)
    * [getModelClass](#getmodelclass)
    * [getAbort](#getabort)
    * [setAbort](#setabort)
* [BeforeDeleteEvent](#beforedeleteevent)
    * [getModel](#getmodel)
    * [isNew](#isnew)
    * [getAbort](#getabort-1)
    * [setAbort](#setabort-1)
* [BeforeMethodCallEvent](#beforemethodcallevent)
    * [getModel](#getmodel-1)
    * [isNew](#isnew-1)
    * [getMetadata](#getmetadata)
    * [getMethod](#getmethod)
    * [getArguments](#getarguments)
    * [getAbort](#getabort-2)
    * [setAbort](#setabort-2)
* [BeforeSaveEvent](#beforesaveevent)
    * [getModel](#getmodel-2)
    * [isNew](#isnew-2)
    * [getAbort](#getabort-3)
    * [setAbort](#setabort-3)
* [LazyModelCollectionResultEvent](#lazymodelcollectionresultevent)
    * [getRepository](#getrepository)
    * [getMethod](#getmethod-1)
    * [getArguments](#getarguments-1)
    * [getResult](#getresult)
    * [setResult](#setresult)
* [MultipleModelResultEvent](#multiplemodelresultevent)
    * [getRepository](#getrepository-1)
    * [getMethod](#getmethod-2)
    * [getArguments](#getarguments-2)
    * [getResult](#getresult-1)
    * [setResult](#setresult-1)
* [SingleModelResultEvent](#singlemodelresultevent)
    * [getRepository](#getrepository-2)
    * [getMethod](#getmethod-3)
    * [getArguments](#getarguments-3)
    * [getResult](#getresult-2)
    * [setResult](#setresult-2)
* [UnknownResultEvent](#unknownresultevent)
    * [getRepository](#getrepository-3)
    * [getMethod](#getmethod-4)
    * [getArguments](#getarguments-4)
    * [getResult](#getresult-3)
    * [setResult](#setresult-3)

## BeforeCreateEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\BeforeCreateEvent
* Parent class: 
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\BeforeCreateEventInterface



### getModelClass

{@inheritdoc}

```php
BeforeCreateEvent::getModelClass(  )
```







---


### getAbort

{@inheritdoc}

```php
BeforeCreateEvent::getAbort(  )
```







---


### setAbort

{@inheritdoc}

```php
BeforeCreateEvent::setAbort(  $abort )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$abort` | **** |  |




---

## BeforeDeleteEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeDeleteEvent
* Parent class: 
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeDeleteEventInterface



### getModel

{@inheritdoc}

```php
BeforeDeleteEvent::getModel(  )
```







---


### isNew

{@inheritdoc}

```php
BeforeDeleteEvent::isNew(  )
```







---


### getAbort

{@inheritdoc}

```php
BeforeDeleteEvent::getAbort(  )
```







---


### setAbort

{@inheritdoc}

```php
BeforeDeleteEvent::setAbort(  $abort )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$abort` | **** |  |




---

## BeforeMethodCallEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeMethodCallEvent
* Parent class: 
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeMethodCallEventInterface



### getModel

{@inheritdoc}

```php
BeforeMethodCallEvent::getModel(  )
```







---


### isNew

{@inheritdoc}

```php
BeforeMethodCallEvent::isNew(  )
```







---


### getMetadata

{@inheritdoc}

```php
BeforeMethodCallEvent::getMetadata(  )
```







---


### getMethod

{@inheritdoc}

```php
BeforeMethodCallEvent::getMethod(  )
```







---


### getArguments

{@inheritdoc}

```php
BeforeMethodCallEvent::getArguments(  )
```







---


### getAbort

{@inheritdoc}

```php
BeforeMethodCallEvent::getAbort(  )
```







---


### setAbort

{@inheritdoc}

```php
BeforeMethodCallEvent::setAbort(  $abort )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$abort` | **** |  |




---

## BeforeSaveEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeSaveEvent
* Parent class: 
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\ModelDecoratorEvents\BeforeSaveEventInterface



### getModel

{@inheritdoc}

```php
BeforeSaveEvent::getModel(  )
```







---


### isNew

{@inheritdoc}

```php
BeforeSaveEvent::isNew(  )
```







---


### getAbort

{@inheritdoc}

```php
BeforeSaveEvent::getAbort(  )
```







---


### setAbort

{@inheritdoc}

```php
BeforeSaveEvent::setAbort(  $abort )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$abort` | **** |  |




---

## LazyModelCollectionResultEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\LazyModelCollectionResultEvent
* Parent class: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\AbstractResultEvent
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\LazyModelCollectionResultEventInterface



### getRepository

{@inheritdoc}

```php
LazyModelCollectionResultEvent::getRepository(  )
```







---


### getMethod

{@inheritdoc}

```php
LazyModelCollectionResultEvent::getMethod(  )
```







---


### getArguments

{@inheritdoc}

```php
LazyModelCollectionResultEvent::getArguments(  )
```







---


### getResult

{@inheritdoc}

```php
LazyModelCollectionResultEvent::getResult(  )
```







---


### setResult

{@inheritdoc}

```php
LazyModelCollectionResultEvent::setResult(  $result )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$result` | **** |  |




---

## MultipleModelResultEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\MultipleModelResultEvent
* Parent class: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\AbstractResultEvent
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\MultipleModelResultEventInterface



### getRepository

{@inheritdoc}

```php
MultipleModelResultEvent::getRepository(  )
```







---


### getMethod

{@inheritdoc}

```php
MultipleModelResultEvent::getMethod(  )
```







---


### getArguments

{@inheritdoc}

```php
MultipleModelResultEvent::getArguments(  )
```







---


### getResult

{@inheritdoc}

```php
MultipleModelResultEvent::getResult(  )
```







---


### setResult

{@inheritdoc}

```php
MultipleModelResultEvent::setResult(  $result )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$result` | **** |  |




---

## SingleModelResultEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\SingleModelResultEvent
* Parent class: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\AbstractResultEvent
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\SingleModelResultEventInterface



### getRepository

{@inheritdoc}

```php
SingleModelResultEvent::getRepository(  )
```







---


### getMethod

{@inheritdoc}

```php
SingleModelResultEvent::getMethod(  )
```







---


### getArguments

{@inheritdoc}

```php
SingleModelResultEvent::getArguments(  )
```







---


### getResult

{@inheritdoc}

```php
SingleModelResultEvent::getResult(  )
```







---


### setResult

{@inheritdoc}

```php
SingleModelResultEvent::setResult(  $result )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$result` | **** |  |




---

## UnknownResultEvent

{@inheritdoc}



* Full name: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\UnknownResultEvent
* Parent class: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\AbstractResultEvent
* This class implements: \Ordermind\DoctrineDecoratorBundle\Event\RepositoryDecoratorEvents\UnknownResultEventInterface



### getRepository

{@inheritdoc}

```php
UnknownResultEvent::getRepository(  )
```







---


### getMethod

{@inheritdoc}

```php
UnknownResultEvent::getMethod(  )
```







---


### getArguments

{@inheritdoc}

```php
UnknownResultEvent::getArguments(  )
```







---


### getResult

{@inheritdoc}

```php
UnknownResultEvent::getResult(  )
```







---


### setResult

{@inheritdoc}

```php
UnknownResultEvent::setResult(  $result )
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$result` | **** |  |




---



--------
> This document was automatically generated from source code comments on 2017-04-17 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
