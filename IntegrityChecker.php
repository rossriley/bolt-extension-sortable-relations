<?php 

namespace Bolt\Extensions\Ross\SortableRelations;

use Bolt\Database\IntegrityChecker as BoltIntegrityChecker;
use Doctrine\DBAL\Schema\Schema;

class IntegrityChecker extends BoltIntegrityChecker
{
    protected function getBoltTablesSchema(Schema $schema)
    {
        $tables = parent::getBoltTablesSchema($schema);

        foreach ($tables as $table) {
            if ($table->getName() == 'bolt_relations') {
                $table->addColumn('sort', 'integer', array('default' => 0));
            }
        }
        
        return $tables;
    }
}