<?php

namespace gheith3\FileViewEntryPlugin\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\Storage;

class FileViewEntry extends Entry
{
    protected string $view = 'file-view-entry-plugin::infolists.components.file-view-entry';

    protected bool|Closure $showAsLink = false;

    protected bool|Closure $asModal = true;

    protected bool|Closure $withModalEye = false;

    protected bool|Closure $contained = true;

    protected bool|Closure $lazyLoad = false;

    protected bool|Closure $showFileSize = false;

    protected bool|Closure $showFileCount = false;

    protected bool|Closure $loadingSkeleton = false;

    protected bool|Closure $copyable = false;

    protected array|Closure $customIcons = [];

    protected bool|Closure $downloadable = false;

    protected int|string|Closure|null $previewHeight = null;

    protected string|Closure|null $diskName = null;

    protected int|Closure|null $gridColumns = null;

    protected string|Closure $titleKey = 'name';

    protected string|Closure $pathKey = 'file_path';

    protected string|Closure|null $dateKey = null;

    public function showAsLink(bool|Closure $condition = true): static
    {
        $this->showAsLink = $condition;

        return $this;
    }

    public function asModal(bool|Closure $condition = true): static
    {
        $this->asModal = $condition;

        return $this;
    }

    public function withModalEye(bool|Closure $condition = true): static
    {
        $this->withModalEye = $condition;

        return $this;
    }

    public function contained(bool|Closure $condition = true): static
    {
        $this->contained = $condition;

        return $this;
    }

    public function lazyLoad(bool|Closure $condition = true): static
    {
        $this->lazyLoad = $condition;

        return $this;
    }

    public function showFileSize(bool|Closure $condition = true): static
    {
        $this->showFileSize = $condition;

        return $this;
    }

    public function showFileCount(bool|Closure $condition = true): static
    {
        $this->showFileCount = $condition;

        return $this;
    }

    public function loadingSkeleton(bool|Closure $condition = true): static
    {
        $this->loadingSkeleton = $condition;

        return $this;
    }

    public function copyable(bool|Closure $condition = true): static
    {
        $this->copyable = $condition;

        return $this;
    }

    public function customIcons(array|Closure $icons): static
    {
        $this->customIcons = $icons;

        return $this;
    }

    public function downloadable(bool|Closure $condition = true): static
    {
        $this->downloadable = $condition;

        return $this;
    }

    public function previewHeight(int|string|Closure|null $height): static
    {
        $this->previewHeight = $height;

        return $this;
    }

    public function disk(string|Closure|null $disk): static
    {
        $this->diskName = $disk;

        return $this;
    }

    public function grid(int|Closure|null $columns): static
    {
        $this->gridColumns = $columns;

        return $this;
    }

    public function titleKey(string|Closure $key): static
    {
        $this->titleKey = $key;

        return $this;
    }

    public function pathKey(string|Closure $key): static
    {
        $this->pathKey = $key;

        return $this;
    }

    public function dateKey(string|Closure|null $key): static
    {
        $this->dateKey = $key;

        return $this;
    }

    public function shouldShowAsLink(): bool
    {
        return (bool) $this->evaluate($this->showAsLink);
    }

    public function shouldShowAsModal(): bool
    {
        return (bool) $this->evaluate($this->asModal);
    }

    public function shouldShowWithModalEye(): bool
    {
        return (bool) $this->evaluate($this->withModalEye);
    }

    public function isContained(): bool
    {
        return (bool) $this->evaluate($this->contained);
    }

    public function shouldLazyLoad(): bool
    {
        return (bool) $this->evaluate($this->lazyLoad);
    }

    public function shouldShowFileSize(): bool
    {
        return (bool) $this->evaluate($this->showFileSize);
    }

    public function shouldShowFileCount(): bool
    {
        return (bool) $this->evaluate($this->showFileCount);
    }

    public function shouldShowLoadingSkeleton(): bool
    {
        return (bool) $this->evaluate($this->loadingSkeleton);
    }

    public function isCopyable(): bool
    {
        return (bool) $this->evaluate($this->copyable);
    }

    public function getCustomIcons(): array
    {
        return (array) $this->evaluate($this->customIcons);
    }

    public function isDownloadable(): bool
    {
        return (bool) $this->evaluate($this->downloadable);
    }

    public function getPreviewHeight(): ?string
    {
        $height = $this->evaluate($this->previewHeight);

        if ($height == null) {
            return '300px';
        }

        if (is_int($height)) {
            return "{$height}px";
        }

        return $height;
    }

    public function getDiskName(): string
    {
        $name = $this->evaluate($this->diskName);

        if (filled($name)) {
            return $name;
        }

        return config('filament.default_filesystem_disk', 'public');
    }

    public function getGridColumns(): ?int
    {
        $columns = $this->evaluate($this->gridColumns);

        return $columns !== null ? (int) $columns : null;
    }

    public function getTitleKey(): string
    {
        return (string) $this->evaluate($this->titleKey);
    }

    public function getPathKey(): string
    {
        return (string) $this->evaluate($this->pathKey);
    }

    public function getDateKey(): ?string
    {
        $key = $this->evaluate($this->dateKey);

        return $key !== null ? (string) $key : null;
    }

    public function getFileUrl(string $path): ?string
    {
        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }

        try {
            return Storage::disk($this->getDiskName())->temporaryUrl($path, now()->addMinutes(5));
        } catch (\Exception $e) {
            try {
                return Storage::disk($this->getDiskName())->url($path);
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function getFileType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp' => 'image',
            'mp4', 'mov', 'avi', 'mkv', 'webm', 'flv', 'wmv' => 'video',
            'mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a' => 'audio',
            'pdf' => 'pdf',
            'txt', 'md' => 'text',
            default => 'other',
        };
    }

    public function canPreviewInBrowser(string $path): bool
    {
        $type = $this->getFileType($path);

        return in_array($type, ['image', 'video', 'audio', 'pdf', 'text']);
    }

    public function getFileIcon(string $fileType, ?string $extension = null): string
    {
        $customIcons = $this->getCustomIcons();
        
        // Check for custom icon by extension first
        if ($extension && isset($customIcons[$extension])) {
            return $customIcons[$extension];
        }
        
        // Check for custom icon by file type
        if (isset($customIcons[$fileType])) {
            return $customIcons[$fileType];
        }
        
        return match ($fileType) {
            'image' => 'heroicon-o-photo',
            'video' => 'heroicon-o-video-camera',
            'audio' => 'heroicon-o-musical-note',
            'pdf' => 'heroicon-o-document-text',
            'text' => 'heroicon-o-document-text',
            'document' => 'heroicon-o-document',
            'spreadsheet' => 'heroicon-o-table-cells',
            'presentation' => 'heroicon-o-presentation-chart-bar',
            'archive' => 'heroicon-o-archive-box',
            default => 'heroicon-o-document',
        };
    }

    public function getFileSize(string $path): ?string
    {
        try {
            $size = Storage::disk($this->getDiskName())->size($path);

            return $this->formatFileSize($size);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
