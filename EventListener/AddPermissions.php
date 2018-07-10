<?php
declare(strict_types=1);

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\EventListener;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

use Ordermind\LogicalAuthorizationBundle\Event\AddPermissionsEventInterface;
use Ordermind\LogicalAuthorizationDoctrineORMBundle\Annotation\Doctrine\Permissions;

/**
 * Event listener for adding Doctrine ORM entity permissions
 */
class AddPermissions
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var string
     */
    protected $annotationDriverClass;

    /**
     * @var string
     */
    protected $xmlDriverClass;

    /**
     * @var string
     */
    protected $ymlDriverClass;

    /**
     * @internal
     *
     * @param Doctrine\Common\Persistence\ManagerRegistry $managerRegistry       ManagerRegistry service
     * @param string                                      $annotationDriverClass The class for the annotation driver
     * @param string                                      $xmlDriverClass        The class for the XML driver
     * @param string                                      $ymlDriverClass        The class for the Yaml driver
     */
    public function __construct(ManagerRegistry $managerRegistry, string $annotationDriverClass, string $xmlDriverClass, string $ymlDriverClass)
    {
        $this->managerRegistry = $managerRegistry;
        $this->annotationDriverClass = $annotationDriverClass;
        $this->xmlDriverClass = $xmlDriverClass;
        $this->ymlDriverClass = $ymlDriverClass;
    }

    /**
     * Event listener callback for adding permissions to the tree
     *
     * @param Ordermind\LogicalAuthorizationBundle\Event\AddPermissionsEventInterface $event
     */
    public function onAddPermissions(AddPermissionsEventInterface $event)
    {
        $eventManagers = $this->managerRegistry->getManagers();
        foreach ($eventManagers as $em) {
            $metadataDriverImplementation = $em->getConfiguration()->getMetadataDriverImpl();
            $drivers = $metadataDriverImplementation->getDrivers();
            foreach ($drivers as $driver) {
                $driverClass = get_class($driver);
                if ($driverClass === $this->annotationDriverClass) {
                    $this->addAnnotationPermissions($event, $driver, $em);
                } elseif ($driverClass === $this->xmlDriverClass) {
                    $this->addXMLPermissions($event, $driver);
                } elseif ($driverClass === $this->ymlDriverClass) {
                    $this->addYMLPermissions($event, $driver);
                }
            }
        }
    }

    /**
     * @internal
     *
     * @param Ordermind\LogicalAuthorizationBundle\Event\AddPermissionsEventInterface $event
     * @param Doctrine\Common\Persistence\Mapping\Driver\MappingDriver                $driver
     * @param Doctrine\ORM\EntityManager                                              $em
     */
    protected function addAnnotationPermissions(AddPermissionsEventInterface $event, MappingDriver $driver, EntityManager $em)
    {
        $classes = $driver->getAllClassNames();
        $annotationReader = $driver->getReader();
        $permissionTree = [];
        foreach ($classes as $class) {
            $reflectionClass = new \ReflectionClass($class);
            $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
            foreach ($classAnnotations as $annotation) {
                if ($annotation instanceof Permissions) {
                    if (!isset($permissionTree['models'])) {
                        $permissionTree['models'] = [];
                    }
                    $permissionTree['models'][$class] = $annotation->getPermissions();
                }
            }
            foreach ($reflectionClass->getProperties() as $property) {
                $fieldName = $property->getName();
                $propertyAnnotations = $annotationReader->getPropertyAnnotations($property);
                foreach ($propertyAnnotations as $annotation) {
                    if ($annotation instanceof Permissions) {
                        if (!isset($permissionTree['models'])) {
                            $permissionTree['models'] = [];
                        }
                        $permissionTree['models'] += [$class => ['fields' => []]];
                        $permissionTree['models'][$class]['fields'][$fieldName] = $annotation->getPermissions();
                    }
                }
            }
        }
        $event->insertTree($permissionTree);
    }

    /**
     * @internal
     *
     * @param Ordermind\LogicalAuthorizationBundle\Event\AddPermissionsEventInterface $event
     * @param Doctrine\Common\Persistence\Mapping\Driver\MappingDriver                $driver
     */
    protected function addXMLPermissions(AddPermissionsEventInterface $event, MappingDriver $driver)
    {
        $classes = $driver->getAllClassNames();
        $permissionTree = [];
        foreach ($classes as $class) {
            $xmlRoot = $driver->getElement($class);
            // Parse XML structure in $element
            if (isset($xmlRoot->permissions)) {
                if (!isset($permissionTree['models'])) {
                    $permissionTree['models'] = [];
                }
                $permissionTree['models'][$class] = json_decode(json_encode($xmlRoot->permissions), true);
            }
            $reflectionClass = new \ReflectionClass($class);
            foreach ($reflectionClass->getProperties() as $property) {
                $fieldName = $property->getName();
                if ($result = $xmlRoot->xpath("*[@name='$fieldName' or @field='$fieldName']")) {
                    $field = $result[0];
                    if (isset($field->permissions)) {
                        if (!isset($permissionTree['models'])) {
                            $permissionTree['models'] = [];
                        }
                        $permissionTree['models'] += [$class => ['fields' => []]];
                        $permissionTree['models'][$class]['fields'][$fieldName] = json_decode(json_encode($field->permissions), true);
                    }
                }
            }
        }
        $permissionTree = $this->massagePermissionsRecursive($permissionTree);
        $event->insertTree($permissionTree);
    }

    /**
     * @internal
     *
     * @param Ordermind\LogicalAuthorizationBundle\Event\AddPermissionsEventInterface $event
     * @param Doctrine\Common\Persistence\Mapping\Driver\MappingDriver                $driver
     */
    protected function addYMLPermissions(AddPermissionsEventInterface $event, MappingDriver $driver)
    {
        $classes = $driver->getAllClassNames();
        $permissionTree = [];
        foreach ($classes as $class) {
            $mapping = $driver->getElement($class);
            if (isset($mapping['permissions'])) {
                if (!isset($permissionTree['models'])) {
                    $permissionTree['models'] = [];
                }
                $permissionTree['models'][$class] = $mapping['permissions'];
            }
            foreach ($mapping as $key => $data) {
                if (!is_array($data)) {
                    continue;
                }
                foreach ($data as $fieldName => $fieldMapping) {
                    if (isset($fieldMapping['permissions'])) {
                        if (!isset($permissionTree['models'])) {
                            $permissionTree['models'] = [];
                        }
                        $permissionTree['models'] += [$class => ['fields' => []]];
                        $permissionTree['models'][$class]['fields'][$fieldName] = $fieldMapping['permissions'];
                    }
                }
            }
        }
        $permissionTree = $this->massagePermissionsRecursive($permissionTree);
        $event->insertTree($permissionTree);
    }

    /**
     * @internal
     *
     * @param array $permissions
     *
     * @return array
     */
    protected function massagePermissionsRecursive($permissions): array
    {
        $massagedPermissions = [];
        foreach ($permissions as $key => $value) {
            if (is_array($value)) {
                $parsedValue = $this->massagePermissionsRecursive($value);
            } elseif (is_string($value)) {
                $lowercaseValue = strtolower($value);
                if ('true' === $lowercaseValue) {
                    $parsedValue = true;
                } elseif ('false' === $lowercaseValue) {
                    $parsedValue = false;
                } else {
                    $parsedValue = $value;
                }
            } else {
                $parsedValue = $value;
            }

            if ('value' === $key) {
                $massagedPermissions[] = $parsedValue;
            } else {
                $massagedPermissions[$key] = $parsedValue;
            }
        }

        return $massagedPermissions;
    }
}
