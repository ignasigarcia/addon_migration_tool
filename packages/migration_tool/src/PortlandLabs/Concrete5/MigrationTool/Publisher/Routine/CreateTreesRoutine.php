<?php
namespace PortlandLabs\Concrete5\MigrationTool\Publisher\Routine;

use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Tree\Type\Topic;
use PortlandLabs\Concrete5\MigrationTool\Batch\BatchInterface;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\Batch;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\TreeNode;

defined('C5_EXECUTE') or die("Access Denied.");

class CreateTreesRoutine implements RoutineInterface
{
    protected function add(TreeNode $node, Node $parent)
    {
        $type = NodeType::getByHandle($node->getType());
        $class = $type->getTreeNodeTypeClass();
        $new = call_user_func_array(array($class, 'add'), array($node->getTitle(), $parent));
        foreach ($node->getChildren() as $child) {
            $this->add($child, $new);
        }
    }

    public function execute(BatchInterface $batch)
    {
        $values = $batch->getObjectCollection('tree');

        if (!$values) {
            return;
        }

        foreach ($values->getTrees() as $t) {
            $name = (string) $t->getName();
            $tree = Topic::getByName($name);
            if (is_object($tree)) {
                // We already have a tree. But we know we're going to have sub-nodes
                // of this tree in the import, so let's keep the same tree so that pointers
                // to attributes work, but let's clear it out.
                $root = $tree->getRootTreeNodeObject();
                $root->populateChildren();
                $children = $root->getChildNodes();
                foreach ($children as $child) {
                    $child->delete();
                }
            } else {
                $tree = Topic::add($name);
            }
            $parent = $tree->getRootTreeNodeObject();
            foreach ($t->getRootNodes() as $node) {
                $this->add($node, $parent);
            }
        }
    }
}
