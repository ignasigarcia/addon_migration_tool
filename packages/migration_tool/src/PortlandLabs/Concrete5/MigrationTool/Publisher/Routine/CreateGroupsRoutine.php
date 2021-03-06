<?php
namespace PortlandLabs\Concrete5\MigrationTool\Publisher\Routine;

use Concrete\Core\Job\Job;
use PortlandLabs\Concrete5\MigrationTool\Batch\BatchInterface;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\Batch;

defined('C5_EXECUTE') or die("Access Denied.");

class CreateGroupsRoutine implements RoutineInterface
{
    public function execute(BatchInterface $batch)
    {
        $groups = $batch->getObjectCollection('group');

        if (!$groups) {
            return;
        }

        $groups = $groups->getGroups()->toArray();
        usort($groups, function ($a, $b) {
            $pathA = $a->getPath();
            $pathB = $b->getPath();
            $numA = count(explode('/', $pathA));
            $numB = count(explode('/', $pathB));
            if ($numA == $numB) {
                return 0;
            } else {
                return ($numA < $numB) ? -1 : 1;
            }
        });

        foreach($groups as $group) {
            $parent = null;
            if ($group->getPath() != '') {
                $lastSlash = strrpos($group->getPath(), '/');
                $parentPath = substr($group->getPath(), 0, $lastSlash);
                if ($parentPath) {
                    $parent = \Concrete\Core\User\Group\Group::getByPath($parentPath);
                }
            }

            $pkg = null;
            if ($group->getPackage()) {
                $pkg = \Package::getByHandle($group->getPackage());
            }

            \Concrete\Core\User\Group\Group::add($group->getName(), $group->getDescription(), $parent, $pkg);
        }
    }
}
