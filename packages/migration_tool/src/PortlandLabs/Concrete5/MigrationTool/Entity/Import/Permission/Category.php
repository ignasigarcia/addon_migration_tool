<?php
namespace PortlandLabs\Concrete5\MigrationTool\Entity\Import\Permission;

use PortlandLabs\Concrete5\MigrationTool\Publisher\PublishableInterface;
use PortlandLabs\Concrete5\MigrationTool\Publisher\Validator\PermissionKeyCategoryValidator;

/**
 * @Entity
 * @Table(name="MigrationImportPermissionKeyCategories")
 */
class Category implements PublishableInterface
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="CategoryObjectCollection")
     **/
    protected $collection;

    /**
     * @Column(type="string")
     */
    protected $handle;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $package = null;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param mixed $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

    public function getPublisherValidator()
    {
        return new PermissionKeyCategoryValidator($this);
    }
}
