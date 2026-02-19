<?php

namespace gheith3\FileViewEntryPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FileViewEntryPlugin implements Plugin
{
    /**
     * @var array<string, mixed>
     */
    protected array $config = [];

    public function getId(): string
    {
        return 'file-view-entry';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function register(Panel $panel): void
    {
        // Plugin registration
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot
    }

    /**
     * Set the default disk for file URLs.
     */
    public function defaultDisk(string $disk): static
    {
        $this->config['disk'] = $disk;

        return $this;
    }

    public function getDefaultDisk(): ?string
    {
        return $this->config['disk'] ?? null;
    }

    /**
     * Set the default grid columns.
     */
    public function defaultGridColumns(int $columns): static
    {
        $this->config['grid_columns'] = $columns;

        return $this;
    }

    public function getDefaultGridColumns(): ?int
    {
        return $this->config['grid_columns'] ?? null;
    }
}
