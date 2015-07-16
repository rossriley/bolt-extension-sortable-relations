<?php

namespace Bolt\Extensions\Ross\SortableRelations;

use Bolt\Application;
use Bolt\BaseExtension;

class Extension extends BaseExtension
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
        if ($this->app['config']->getWhichEnd() == 'backend') {
            $this->app['twig.loader.filesystem']->prependPath(__DIR__.'/twig');
        }
    }

    public function initialize()
    {
        $this->app['integritychecker'] = $this->app->share(
            function ($app) {
                return new IntegrityChecker($app);
            }
        );
        
        $check = $this->app['integritychecker']->checkTablesIntegrity();
        $this->addCss('assets/select2.sortable.css', 1);
        $this->addJavascript('assets/select2.sortable.min.js', 1);
        
        $this->app['dispatcher']->addListener(\Bolt\Events\StorageEvents::POST_SAVE, array($this, 'saveRelationOrder'));


    }
    
    
    public function saveRelationOrder($event)
    {
        $content = $event->getContent();
        $contenttype = $event->getContentType();
        $relations = $content->relation;
        foreach ($relations as $type => $related) {
            // First we delete the current values
            $tablename = $this->app['storage']->getTablename('relations');
            $this->app['db']->delete($tablename, [
                'from_contenttype' => $contenttype,
                'to_contenttype' => $type,
                'from_id' => $content->id
            ]);
            
            // Now we insert all the ones we have, along with the order
            foreach ($related as $sortOrder => $relId) {
                $row = [
                    'from_contenttype' => $contenttype,
                    'from_id'          => $content->id,
                    'to_contenttype'   => $type,
                    'to_id'            => $relId,
                    'sort'             => $sortOrder
                ];
                $this->app['db']->insert($tablename, $row);
            }
            
        }
    }
    
    

    public function getName()
    {
        return 'sortable-relations';
    }
}
