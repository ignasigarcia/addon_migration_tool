<?php
namespace PortlandLabs\Concrete5\MigrationTool\Batch\Processor\Task;

use Concrete\Core\Foundation\Processor\ActionInterface;
use Concrete\Core\Foundation\Processor\TaskInterface;
use PortlandLabs\Concrete5\MigrationTool\Batch\ContentMapper\EmptyMapper;
use PortlandLabs\Concrete5\MigrationTool\Batch\ContentMapper\MapperManagerInterface;
use PortlandLabs\Concrete5\MigrationTool\Entity\ContentMapper\IgnoredTargetItem;
use PortlandLabs\Concrete5\MigrationTool\Entity\ContentMapper\UnmappedTargetItem;
use PortlandLabs\Concrete5\MigrationTool\Batch\ContentMapper\TargetItemList;

defined('C5_EXECUTE') or die("Access Denied.");

class TransformContentTypesTask implements TaskInterface
{

    protected $mappers;

    public function __construct(MapperManagerInterface $mappers)
    {
        $this->mappers = $mappers;
    }

    public function execute(ActionInterface $action)
    {
        return;
    }

    public function finish(ActionInterface $action)
    {
        $target = $action->getTarget();
        $batch = $target->getBatch();
        $transformers = \Core::make('migration/manager/transforms');
        foreach ($transformers->getDrivers() as $transformer) {
            try {
                $mapper = $this->mappers->driver($transformer->getDriver());
            } catch (\Exception $e) {
                // No mapper for this type.}
                $mapper = new EmptyMapper();
            }

            $targetItemList = $this->mappers->createTargetItemList($batch, $mapper);
            $items = $transformer->getUntransformedEntityObjects($mapper, $batch);
            foreach ($items as $entity) {
                $item = $transformer->getItem($entity);
                if (is_object($item)) {
                    $targetItem = $targetItemList->getSelectedTargetItem($item);
                    if (is_object($targetItem)) {
                        if (!($targetItem instanceof UnmappedTargetItem || $target instanceof IgnoredTargetItem)) {
                            $transformer->transform($entity, $mapper, $item, $targetItem, $batch);
                        }
                    }
                }
            }
        }
    }
}
