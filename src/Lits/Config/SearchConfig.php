<?php

declare(strict_types=1);

namespace Lits\Config;

use Lits\Config;
use Lits\Exception\InvalidConfigException;

use function Safe\preg_match;
use function Safe\sprintf;

final class SearchConfig extends Config
{
    /** @var array<string, string> */
    public array $catalogs = [];

    public function testCatalogs(): void
    {
        if (\count($this->catalogs) === 0) {
            throw new InvalidConfigException(
                'At least one catalog must be specified'
            );
        }

        foreach ($this->catalogs as $slug => $url) {
            if (\filter_var($url, \FILTER_VALIDATE_URL) === false) {
                throw new InvalidConfigException(sprintf(
                    'A valid URL is required for catalog %s.',
                    $slug
                ));
            }

            if (preg_match('/^https?:/i', $url) === 0) {
                throw new InvalidConfigException(sprintf(
                    'A HTTP(S) URL is required for catalog %s.',
                    $slug
                ));
            }
        }
    }
}
