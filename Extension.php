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

        $this->addTwigFunction('relationSort', 'relationSort');
        $this->addTwigFunction('getSortedRelations', 'getSortedRelations');


    }
    
    public function relationSort($arr1, $arr2)
    {
        $index = [];
        foreach ($arr2 as $key => $obj) {
            $index[] = $key;
        }
        $compiled = [];        
        foreach ($arr1 as $val) {
            $compiled[$val] = $arr2[$val];
            unset($index[array_search($val, $index)]);
        }
        
        foreach ($index as $val) {
            $compiled[$val] = $arr2[$val];
        }
        
        return $compiled;
    }
    
    public function getSortedRelations($content, $relcontenttype)
    {
        $id = $content['id'];
        
        $query = "SELECT * from bolt_relations WHERE from_id=$id AND from_contenttype='$relcontenttype' ORDER BY sort;";
        $result = $this->app['db']->fetchAll($query);
        
        return $result;
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
