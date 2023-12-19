<?php

declare(strict_types=1);

namespace Lits\Config;

use Lits\Config;
use Lits\Exception\InvalidConfigException;
use Safe\Exceptions\PcreException;

use function Safe\preg_match;

final class SearchConfig extends Config
{
    /** @var array<string, string> */
    public array $catalogs = [];

    /** @throws InvalidConfigException */
    public function testCatalogs(): void
    {
        if (\count($this->catalogs) === 0) {
            throw new InvalidConfigException(
                'At least one catalog must be specified',
            );
        }

        foreach ($this->catalogs as $slug => $url) {
            $this->testCatalogsUrl($slug, $url);
        }
    }

    /** @throws InvalidConfigException */
    private function testCatalogsUrl(string $slug, string $url): void
    {
        if (\filter_var($url, \FILTER_VALIDATE_URL) === false) {
            throw new InvalidConfigException(\sprintf(
                'A valid URL is required for catalog %s.',
                $slug,
            ));
        }

        try {
            if (preg_match('/^https?:/i', $url) === 0) {
                throw new InvalidConfigException(\sprintf(
                    'A HTTP(S) URL is required for catalog %s.',
                    $slug,
                ));
            }
        } catch (PcreException $exception) {
            throw new InvalidConfigException(
                \sprintf('Could not validate URL for catalog %s.', $slug),
                0,
                $exception,
            );
        }
    }
}
