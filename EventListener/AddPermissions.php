<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\EventListener;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;

use Ordermind\LogicalAuthorizationBundle\Event\AddPermissionsEvent;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Annotation\Doctrine\Permissions;

class AddPermissions {
  protected $managerRegistry;
  protected $annotationDriverClass;
  protected $xmlDriverClass;
  protected $ymlDriverClass;

  public function __construct(ManagerRegistry $managerRegistry, $annotationDriverClass, $xmlDriverClass, $ymlDriverClass) {
    $this->managerRegistry = $managerRegistry;
    $this->annotationDriverClass = $annotationDriverClass;
    $this->xmlDriverClass = $xmlDriverClass;
    $this->ymlDriverClass = $ymlDriverClass;
  }

  public function onAddPermissions(AddPermissionsEvent $event) {
    $object_managers = $this->managerRegistry->getManagers();
    foreach($object_managers as $em) {
      $metadataDriverImplementation = $em->getConfiguration()->getMetadataDriverImpl();
      $drivers = $metadataDriverImplementation->getDrivers();
      foreach($drivers as $driver) {
        $driver_class = get_class($driver);
        if($driver_class === $this->annotationDriverClass) {
          $this->addAnnotationPermissions($event, $driver, $em);
        }
        elseif($driver_class === $this->xmlDriverClass) {
          $this->addXMLPermissions($event, $driver);
        }
        elseif($driver_class === $this->ymlDriverClass) {
          $this->addYMLPermissions($event, $driver);
        }
      }
    }
  }

  protected function addAnnotationPermissions(AddPermissionsEvent $event, MappingDriver $driver, ObjectManager $em) {
    $classes = $driver->getAllClassNames();
    $annotationReader = $driver->getReader();
    $permissionTree = [];
    foreach($classes as $class) {
      $reflectionClass = new \ReflectionClass($class);
      $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
      foreach ($classAnnotations as $annotation) {
        if ($annotation instanceof Permissions) {
          if(!isset($permissionTree['models'])) $permissionTree['models'] = [];
          $permissionTree['models'][$class] = $annotation->getPermissions();
        }
      }
      foreach($reflectionClass->getProperties() as $property) {
        $field_name = $property->getName();
        $propertyAnnotations = $annotationReader->getPropertyAnnotations($property);
        foreach ($propertyAnnotations as $annotation) {
          if ($annotation instanceof Permissions) {
            if(!isset($permissionTree['models'])) $permissionTree['models'] = [];
            $permissionTree['models'] += [$class => ['fields' => []]];
            $permissionTree['models'][$class]['fields'][$field_name] = $annotation->getPermissions();
          }
        }
      }
    }
    $event->insertTree($permissionTree);
  }

  protected function addXMLPermissions(AddPermissionsEvent $event, MappingDriver $driver) {
    $classes = $driver->getAllClassNames();
    $permissionTree = [];
    foreach($classes as $class) {
      $xmlRoot = $driver->getElement($class);
      // Parse XML structure in $element
      if(isset($xmlRoot->permissions)) {
        if(!isset($permissionTree['models'])) $permissionTree['models'] = [];
        $permissionTree['models'][$class] = json_decode(json_encode($xmlRoot->permissions), TRUE);
      }
      $reflectionClass = new \ReflectionClass($class);
      foreach($reflectionClass->getProperties() as $property) {
        $field_name = $property->getName();
        if($result = $xmlRoot->xpath("*[@name='$field_name' or @field='$field_name']")) {
          $field = $result[0];
          if(isset($field->permissions)) {
            if(!isset($permissionTree['models'])) $permissionTree['models'] = [];
            $permissionTree['models'] += [$class => ['fields' => []]];
            $permissionTree['models'][$class]['fields'][$field_name] = json_decode(json_encode($field->permissions), TRUE);
          }
        }
      }
    }
    $permissionTree = $this->massagePermissionsRecursive($permissionTree);
    $event->insertTree($permissionTree);
  }

  protected function addYMLPermissions(AddPermissionsEvent $event, MappingDriver $driver) {
    $classes = $driver->getAllClassNames();
    $permissionTree = [];
    foreach($classes as $class) {
      $mapping = $driver->getElement($class);
      if(isset($mapping['permissions'])) {
        if(!isset($permissionTree['models'])) $permissionTree['models'] = [];
        $permissionTree['models'][$class] = $mapping['permissions'];
      }
      foreach($mapping as $key => $data) {
        if(!is_array($data)) continue;
        foreach($data as $field_name => $field_mapping) {
          if(isset($field_mapping['permissions'])) {
            if(!isset($permissionTree['models'])) $permissionTree['models'] = [];
            $permissionTree['models'] += [$class => ['fields' => []]];
            $permissionTree['models'][$class]['fields'][$field_name] = $field_mapping['permissions'];
          }
        }
      }
    }
    $permissionTree = $this->massagePermissionsRecursive($permissionTree);
    $event->insertTree($permissionTree);
  }

  protected function massagePermissionsRecursive($permissions) {
    $massaged_permissions = [];
    foreach($permissions as $key => $value) {
      if(is_array($value)) {
        $parsed_value = $this->massagePermissionsRecursive($value);
      }
      elseif(is_string($value)) {
        $lowercase_value = strtolower($value);
        if($lowercase_value === 'true') {
          $parsed_value = TRUE;
        }
        elseif($lowercase_value === 'false') {
          $parsed_value = FALSE;
        }
        else {
          $parsed_value = $value;
        }
      }
      else {
        $parsed_value = $value;
      }

      if($key === 'value') {
        $massaged_permissions[] = $parsed_value;
      }
      else {
        $massaged_permissions[$key] = $parsed_value;
      }
    }

    return $massaged_permissions;
  }
}

