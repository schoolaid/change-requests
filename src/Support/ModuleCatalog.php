<?php

namespace Said\ChangeRequests\Support;

class ModuleCatalog
{
    /** @var array<int, string>|null */
    private ?array $cached = null;

    /**
     * Slugs válidos de `affectedModule`: los subdirectorios del path configurado,
     * excluyendo los que empiezan con `_` (convenciones internas de la doc).
     *
     * @return array<int, string>
     */
    public function slugs(): array
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        $path = (string) config('change-requests.modules_path');

        if (! is_dir($path)) {
            return $this->cached = [];
        }

        $slugs = [];

        foreach ((array) scandir($path) as $entry) {
            if (! is_string($entry) || str_starts_with($entry, '.') || str_starts_with($entry, '_')) {
                continue;
            }

            if (is_dir($path . DIRECTORY_SEPARATOR . $entry)) {
                $slugs[] = $entry;
            }
        }

        sort($slugs);

        return $this->cached = $slugs;
    }
}
