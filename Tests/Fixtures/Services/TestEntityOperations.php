<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Services;

use Doctrine\Common\Collections\Criteria;

use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\RepositoryDecoratorInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Services\Decorator\EntityDecoratorInterface;
use Ordermind\LogicalAuthorizationBundle\Interfaces\UserInterface;

class TestEntityOperations
{
    private $repositoryDecorator;

    public function setRepositoryDecorator(RepositoryDecoratorInterface $repositoryDecorator)
    {
        $this->repositoryDecorator = $repositoryDecorator;
    }

    public function getUnknownResult($bypassAccess = false)
    {
        if ($bypassAccess) {
            $entities = $this->repositoryDecorator->getRepository()->customMethod();
            return $this->repositoryDecorator->wrapEntities($entities);
        }
        return $this->repositoryDecorator->customMethod();
    }

    public function getSingleEntityResult($id, $bypassAccess = false)
    {
        if ($bypassAccess) {
            $entity = $this->repositoryDecorator->getRepository()->find($id);
            return $this->repositoryDecorator->wrapEntity($entity);
        }
        return $this->repositoryDecorator->find($id);
    }

    public function getMultipleEntityResult($bypassAccess = false)
    {
        if ($bypassAccess) {
            $entities = $this->repositoryDecorator->getRepository()->findAll();
            return $this->repositoryDecorator->wrapEntities($entities);
        }
        return $this->repositoryDecorator->findAll();
    }

    public function getLazyLoadedEntityResult($bypassAccess = false)
    {
        if ($bypassAccess) {
            return $this->repositoryDecorator->getRepository()->matching(Criteria::create());
        }
        return $this->repositoryDecorator->matching(Criteria::create());
    }

    public function createTestEntity($user = null, $bypassAccess = false)
    {
        if ($user && $user instanceof EntityDecoratorInterface) {
            $this->repositoryDecorator->setEntityManager($user->getEntityManager());
            $user = $user->getEntity();
        }

        if ($bypassAccess) {
            $class = $this->repositoryDecorator->getClassName();
            $entity = new $class();
            $entityDecorator = $this->repositoryDecorator->wrapEntity($entity);
        } else {
            $entityDecorator = $this->repositoryDecorator->create();
        }

        if ($entityDecorator) {
            if ($bypassAccess) {
                $entity = $entityDecorator->getEntity();
                if ($user instanceof UserInterface) {
                    $entity->setAuthor($user);
                }
                $em = $entityDecorator->getEntityManager();
                $em->persist($entity);
                $em->flush();
            } else {
                if ($user instanceof UserInterface) {
                    $entityDecorator->setAuthor($user);
                }
                $entityDecorator->save();
            }
        }

        return $entityDecorator;
    }

    public function callMethodGetter(EntityDecoratorInterface $entityDecorator, $bypassAccess = false)
    {
        if ($bypassAccess) {
            return $entityDecorator->getEntity()->getField1();
        }
        return $entityDecorator->getField1();
    }

    public function callMethodSetter(EntityDecoratorInterface $entityDecorator, $bypassAccess = false)
    {
        if ($bypassAccess) {
            $entityDecorator->getEntity()->setField1('test');
        } else {
            $entityDecorator->setField1('test');
        }
        return $entityDecorator;
    }
}
