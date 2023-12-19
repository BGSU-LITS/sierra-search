<?php

declare(strict_types=1);

namespace Lits\Action;

use Lits\Action;
use Lits\Config\SearchConfig;
use Lits\Exception\InvalidConfigException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

final class IndexAction extends Action
{
    /**
     * @throws HttpInternalServerErrorException
     * @throws HttpNotFoundException
     */
    public function action(): void
    {
        \assert($this->settings['search'] instanceof SearchConfig);

        try {
            $this->settings['search']->testCatalogs();
        } catch (InvalidConfigException $exception) {
            throw new HttpInternalServerErrorException(
                $this->request,
                null,
                $exception,
            );
        }

        $catalogs = $this->settings['search']->catalogs;

        if (isset($this->data['catalog'])) {
            if (!isset($catalogs[$this->data['catalog']])) {
                throw new HttpNotFoundException($this->request);
            }

            $this->catalog($catalogs[$this->data['catalog']]);

            return;
        }

        try {
            $this->render($this->template(), ['catalogs' => $catalogs]);
        } catch (\Throwable $exception) {
            throw new HttpInternalServerErrorException(
                $this->request,
                null,
                $exception,
            );
        }
    }

    public function catalog(string $catalog): void
    {
        $url = \rtrim($catalog, '/') . '/';

        if (isset($this->data['function'])) {
            $url .= $this->data['function'];
        }

        $query = $this->request->getUri()->getQuery();

        if ($query !== '') {
            $url .= '?' . $query;
        }

        $this->redirect($url);
    }
}
