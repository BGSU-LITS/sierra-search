<?php

declare(strict_types=1);

namespace Lits\Action;

use Lits\Action;
use Lits\Config\SearchConfig;
use Slim\Exception\HttpNotFoundException;

final class IndexAction extends Action
{
    public function action(): void
    {
        \assert($this->settings['search'] instanceof SearchConfig);

        $this->settings['search']->testCatalogs();
        $catalogs = $this->settings['search']->catalogs;

        if (!isset($this->data['catalog'])) {
            $this->render($this->template(), ['catalogs' => $catalogs]);

            return;
        }

        if (!isset($catalogs[$this->data['catalog']])) {
            throw new HttpNotFoundException($this->request);
        }

        $url = \rtrim($catalogs[$this->data['catalog']], '/');

        if (isset($this->data['function'])) {
            $url .= '/' . $this->data['function'];
        }

        $query = $this->request->getUri()->getQuery();

        if ($query !== '') {
            $url .= '?' . $query;
        }

        $this->redirect($url);
    }
}
