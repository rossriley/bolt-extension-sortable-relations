<?php

namespace Bolt\Extensions\Ross\SortableRelations;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
