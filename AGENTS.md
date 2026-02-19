# FileViewEntryPlugin - AI Agent Guide

This document provides essential information for AI coding agents working on the FileViewEntryPlugin Filament package.

---

## Project Overview

FileViewEntryPlugin is a **Filament plugin** that provides a custom Infolist entry component for displaying file attachments with:

- Type-specific icons (image, video, audio, PDF, text, etc.)
- Grid-based responsive layout
- Modal preview for supported file types
- Direct download option
- Dark mode support
- Customizable data keys for flexible array/object structures

**Package Name**: `gheith3/filament-file-view-entry`
**Main Class**: `FileViewEntry`

---

## Architecture

### Directory Structure

```
FileViewEntryPlugin/
├── src/                                     # Source code
│   ├── FileViewEntryPlugin.php              # Plugin class
│   ├── FileViewEntryPluginServiceProvider.php # Service provider
│   └── Infolists/
│       └── Components/
│           └── FileViewEntry.php            # Main entry component
├── resources/
│   └── views/
│       └── infolists/
│           └── components/
│               └── file-view-entry.blade.php # Blade view
├── tests/                                   # Test suite
│   ├── TestCase.php                         # Base test class
│   └── Unit/
│       └── FileViewEntryTest.php            # Component tests
├── .github/
│   └── workflows/
│       ├── tests.yml                        # CI test workflow
│       └── pint.yml                         # Code style workflow
├── composer.json
├── phpunit.xml                              # PHPUnit configuration
├── pint.json                                # Laravel Pint configuration
├── CHANGELOG.md                             # Version history
├── CONTRIBUTING.md                          # Contribution guidelines
├── SECURITY.md                              # Security policy
├── README.md
└── AGENTS.md (this file)
```

### Key Components

1. **FileViewEntryPlugin** - Implements `Filament\Contracts\Plugin`
   - `getId()`: Returns 'file-view-entry'
   - `make()`: Fluent factory method
   - `get()`: Accessor for plugin instance

2. **FileViewEntry** - Extends `Filament\Infolists\Components\Entry`
   - Handles single files, arrays, and Collections
   - Configurable data keys (title, path, date)
   - Grid layout with auto or fixed columns
   - Modal preview with Alpine.js

3. **FileViewEntryPluginServiceProvider**
   - Registers views with namespace `file-view-entry-plugin`
   - Publishes views to `resources/views/vendor/file-view-entry-plugin`

---

## Usage Patterns

### Basic Usage

```php
use gheith3\FileViewEntryPlugin\Infolists\Components\FileViewEntry;

// Single file path
FileViewEntry::make('file_path')
    ->showPreview()
    ->downloadable();

// Array of files with grid
FileViewEntry::make('uploadedFiles')
    ->grid(3)
    ->showPreview()
    ->downloadable();
```

### With Custom Keys

```php
FileViewEntry::make('documents')
    ->grid(4)
    ->titleKey('title')        // Default: 'name'
    ->pathKey('path')          // Default: 'file_path'
    ->dateKey('uploaded_at')   // Default: null (hidden)
    ->showPreview()
    ->downloadable();
```

### In Filament Panel

```php
use gheith3\FileViewEntryPlugin\FileViewEntryPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(FileViewEntryPlugin::make());
}
```

---

## Configuration Options

| Method            | Type   | Default     | Description                        |
| ----------------- | ------ | ----------- | ---------------------------------- | -------------------------------- | -------------------- |
| `grid()`          | int    | null        | null                               | Grid columns (1-6, auto if null) |
| `titleKey()`      | string | 'name'      | Key for file title in array/object |
| `pathKey()`       | string | 'file_path' | Key for file path in array/object  |
| `dateKey()`       | string | null        | null                               | Key for date (null = hidden)     |
| `disk()`          | string | null        | config default                     | Storage disk for URLs            |
| `showPreview()`   | bool   | true        | Enable modal preview               |
| `downloadable()`  | bool   | false       | Show download button               |
| `showAsLink()`    | bool   | false       | Show as link instead of cards      |
| `previewHeight()` | int    | string      | null                               | '300px'                          | Modal preview height |

---

## File Type Support

| Type  | Extensions                          | Preview          |
| ----- | ----------------------------------- | ---------------- |
| image | jpg, jpeg, png, gif, bmp, svg, webp | ✓ Modal          |
| video | mp4, mov, avi, mkv, webm, flv, wmv  | ✓ Modal          |
| audio | mp3, wav, ogg, flac, aac, m4a       | ✓ Modal          |
| pdf   | pdf                                 | ✓ Modal (iframe) |
| text  | txt, md                             | ✓ Modal          |
| other | \*                                  | ✗ Opens new tab  |

---

## Customization

### Publishing Views

```bash
php artisan vendor:publish --tag=file-view-entry-plugin-views
```

Views will be copied to `resources/views/vendor/file-view-entry-plugin/`

### CSS Classes

Card styling:

- Container: `aspect-square p-4 rounded-2xl bg-gray-50 dark:bg-gray-800 border`
- Icon: `w-12 h-12 text-gray-400 group-hover:text-primary-500`
- Title: `text-sm font-medium text-gray-900 dark:text-white truncate`
- Date: `text-xs text-gray-500 dark:text-gray-400` (optional)

---

## Testing

Run tests with:

```bash
vendor/bin/pest
```

Run tests with coverage:

```bash
vendor/bin/pest --coverage
```

Run code style checks with:

```bash
vendor/bin/pint --test
```

Fix code style issues with:

```bash
vendor/bin/pint
```

---

## Building for Distribution

1. Update `composer.json` with your vendor name
2. Update namespace in all PHP files
3. Update author information
4. Create GitHub repository
5. Submit to Packagist

---

## Important Notes

- **Always use `$dateKey` explicitly** if you want dates shown (default is null)
- **Grid is responsive**: Mobile shows 2 cols, tablet 3, desktop 4-5
- **URL generation**: Uses `Storage::disk()->temporaryUrl()` with 5min expiry, falls back to `url()`
- **Collections supported**: Eloquent relationships work out of the box
- **Dark mode**: Fully supported via Tailwind dark: classes

---

## Dependencies

- PHP ^8.1
- Filament ^4.0
- Laravel ^10.0|^11.0|^12.0
- Alpine.js (included with Filament)
