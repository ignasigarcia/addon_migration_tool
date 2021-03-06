<?php
namespace PortlandLabs\Concrete5\MigrationTool\Publisher\Routine;

use PortlandLabs\Concrete5\MigrationTool\Batch\BatchInterface;
use PortlandLabs\Concrete5\MigrationTool\Batch\ContentMapper\Item\Item;
use PortlandLabs\Concrete5\MigrationTool\Batch\ContentMapper\MapperManagerInterface;
use PortlandLabs\Concrete5\MigrationTool\Batch\ContentMapper\TargetItemList;
use PortlandLabs\Concrete5\MigrationTool\Entity\ContentMapper\IgnoredTargetItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\ContentMapper\UnmappedTargetItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\Batch;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\ObjectCollection;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPageRoutine implements RoutineInterface
{
    protected $batch;

    /**
     * @return mixed
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param mixed $batch
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;
    }

    protected function getPageByPath(Batch $batch, $path)
    {
        return \Page::getByPath('/!import_batches/' . $batch->getID() . $path, 'RECENT');
    }

    public function getPageCollection(BatchInterface $batch)
    {
        return $batch->getObjectCollection('page');
    }

    public function getPages(ObjectCollection $collection)
    {
        return $collection->getPages();
    }

    public function getPagesOrderedForImport(Batch $batch)
    {
        $collection = $this->getPageCollection($batch);

        if (!$collection) {
            return array();
        }

        $pages = array();
        foreach ($this->getPages($collection) as $page) {
            $pages[] = $page;
        }
        usort($pages, function ($pageA, $pageB) {
            $pathA = (string) $pageA->getBatchPath();
            $pathB = (string) $pageB->getBatchPath();
            $numA = count(explode('/', $pathA));
            $numB = count(explode('/', $pathB));
            if ($numA == $numB) {
                if (intval($pageA->getPosition()) < intval($pageB->getPosition())) {
                    return -1;
                } else {
                    if (intval($pageA->getPosition()) > intval($pageB->getPosition())) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            } else {
                return ($numA < $numB) ? -1 : 1;
            }
        });

        return $pages;
    }

    public function getTargetItem($mapper, $subject)
    {
        if ($subject) {
            /**
             * @var $mappers MapperManagerInterface
             */
            $mappers = \Core::make('migration/manager/mapping');
            $mapper = $mappers->driver($mapper);
            $list = $mappers->createTargetItemList($this->batch, $mapper);
            $item = new Item($subject);
            $targetItem = $list->getSelectedTargetItem($item);
            if (!($targetItem instanceof UnmappedTargetItem || $targetItem instanceof IgnoredTargetItem)) {
                return $mapper->getTargetItemContentObject($targetItem);
            }
        }
    }
}
