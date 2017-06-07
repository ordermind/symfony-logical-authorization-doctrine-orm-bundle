<?php

namespace Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Entity\Misc;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestEntityAbortCalls
 *
 * @ORM\Table(name="test_entity_abort_calls")
 * @ORM\Entity(repositoryClass="Ordermind\LogicalAuthorizationDoctrineORMBundle\Tests\Fixtures\Repository\Misc\TestEntityAbortCallsRepository")
 */
class TestEntityAbortCalls
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="field1", type="string", length=255)
     */
    private $field1 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="field2", type="string", length=255)
     */
    private $field2 = '';

    /**
     * @var string
     *
     * @ORM\Column(name="field3", type="string", length=255)
     */
    private $field3 = '';

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set field1
     *
     * @param string $field1
     *
     * @return TestEntity
     */
    public function setField1($field1)
    {
        $this->field1 = $field1;

        return $this;
    }

    /**
     * Get field1
     *
     * @return string
     */
    public function getField1()
    {
        return $this->field1;
    }

    /**
     * Set field2
     *
     * @param string $field2
     *
     * @return TestEntity
     */
    public function setField2($field2)
    {
        $this->field2 = $field2;

        return $this;
    }

    /**
     * Get field2
     *
     * @return string
     */
    public function getField2()
    {
        return $this->field2;
    }

    /**
     * Set field3
     *
     * @param string $field3
     *
     * @return TestEntity
     */
    public function setField3($field3)
    {
        $this->field3 = $field3;

        return $this;
    }

    /**
     * Get field3
     *
     * @return string
     */
    public function getField3()
    {
        return $this->field3;
    }
}


