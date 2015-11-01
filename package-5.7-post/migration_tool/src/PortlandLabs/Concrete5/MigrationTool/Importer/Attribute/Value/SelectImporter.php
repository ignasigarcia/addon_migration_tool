<?php

namespace PortlandLabs\Concrete5\MigrationTool\Importer\Attribute\Value;

use PortlandLabs\Concrete5\MigrationTool\Entity\Import\AttributeValue\AttributeValue;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\AttributeValue\SelectAttributeValue;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\AttributeValue\StandardAttributeValue;
use PortlandLabs\Concrete5\MigrationTool\Importer\ImporterInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class SelectImporter implements ImporterInterface
{
    public function parse(\SimpleXMLElement $node)
    {
        $value = new SelectAttributeValue();
        $options = array();
        if ($node->value->option) {
            foreach($node->value->option as $option) {
                $options[] = (string) $option;
            }
        }
        $value->setValue($options);
        return $value;
    }

}
