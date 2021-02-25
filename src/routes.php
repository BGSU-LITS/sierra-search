<?php

declare(strict_types=1);

use Lits\Action\IndexAction;
use Lits\Framework;

return function (Framework $framework): void {
    $framework->app()->get('/', IndexAction::class)
        ->setName('index');

    $framework->app()->get('/{catalog}[/{function}]', IndexAction::class)
        ->setName('catalog');
};
