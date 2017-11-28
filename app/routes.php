<?php
/**
 * Application Routes
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2017 Bowling Green State University Libraries
 * @license MIT
 */

namespace App\Action;

use Slim\Container;
use TheIconic\Tracking\GoogleAnalytics\Analytics;

// Index action.
$container[IndexAction::class] = function (Container $container) {
    return new IndexAction(
        $container[Analytics::class],
        $container['settings']['catalogs']
    );
};

$app->get('/', IndexAction::class);
$app->get('/{catalog}[/{function}]', IndexAction::class);
