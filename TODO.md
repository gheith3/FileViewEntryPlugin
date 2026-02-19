# TODO - Before & After Release

This file tracks tasks to complete before and after moving the plugin to a separate repository.

---

## 🔴 Critical - Must Do Before First Release

### Branding & Identity
- [x] Replace `YourVendor` with actual vendor name in ALL files
  - [x] `composer.json` (lines 2, 14, 28, 39, 46)
  - [x] `src/FileViewEntryPlugin.php` (line 3)
  - [x] `src/FileViewEntryPluginServiceProvider.php` (line 3)
  - [x] `src/Infolists/Components/FileViewEntry.php` (line 3)
  - [x] `AGENTS.md` (all namespace references)
  - [x] `README.md` (all namespace references)
- [x] Update author info in `composer.json`
  - [x] Name
  - [x] Email
- [x] Update package name in `composer.json` (line 2)
  - Format: `vendor/filament-file-view-entry`

### Repository Setup
- [x] Create GitHub repository
- [x] Add repository URL to `composer.json`
  - [x] `homepage`
  - [x] `support.issues`
  - [x] `support.source`
- [x] Update README.md links (GitHub URLs)

### Documentation
- [x] Add screenshots to README.md
- [x] Review and update AGENTS.md for accuracy

---

## 🟡 High Priority - Before Release

### Testing
- [x] Create `tests/` directory structure
- [x] Add Pest tests for FileViewEntry component
- [x] Add test for file type detection
- [x] Configure GitHub Actions for CI
  - [x] PHP 8.1, 8.2, 8.3
  - [x] Laravel 10, 11, 12
  - [x] Filament 4

### Code Quality
- [x] Add `pint.json` (Laravel Pint configuration)
- [x] Add `.github/workflows/pint.yml` (code style CI)
- [x] Add `.github/workflows/tests.yml` (test CI)
- [x] Run Pint and fix any style issues
- [x] Add `.gitignore`
  - [x] vendor/
  - [x] .phpunit.result.cache
  - [x] composer.lock
  - [x] .idea/
  - [x] .vscode/

### Project Files
- [x] Add `CHANGELOG.md` (follow Keep a Changelog format)
- [x] Add `CONTRIBUTING.md` (contribution guidelines)
- [x] Add `SECURITY.md` (security policy)
- [x] Add `.editorconfig`

---

## 🟢 Medium Priority - After Release

### Features
- [ ] Add support for custom icon mapping
- [ ] Add option for list view (alternative to grid)
- [ ] Add file size display option
- [ ] Add bulk download option
- [ ] Add drag-and-drop upload integration
- [ ] Add search/filter functionality for multiple files

### Enhancements
- [ ] Add lazy loading for images in modal
- [ ] Add keyboard navigation (arrow keys, escape)
- [ ] Add loading states
- [ ] Add empty state customization
- [ ] Add support for custom blade components per file type

### Documentation
- [ ] Create video tutorial/GIF demo
- [ ] Add more usage examples to README
- [ ] Add FAQ section
- [ ] Create documentation website (GitHub Pages)

---

## 🔵 Low Priority - Nice to Have

### Distribution
- [x] Submit to Packagist
- [ ] Add to Filament Plugin Directory (https://filamentphp.com/plugins)
  - [ ] Create plugin screenshot (2560x1440px, 16:9, JPEG)
  - [ ] Fork filamentphp.com repository
  - [ ] Create content/plugins/gheith3-file-view-entry.md
  - [ ] Add image to public/images/content/plugins/
  - [ ] Submit Pull Request
- [ ] Create demo project repository

### Community
- [ ] Add issue templates (bug report, feature request)
- [ ] Add pull request template
- [ ] Set up Discord channel (optional)
- [ ] Create social media announcement

### Maintenance
- [x] Set up GitHub Hook for Packagist auto-update
- [ ] Set up Dependabot for dependency updates
- [ ] Schedule regular compatibility tests with new Filament versions

---

## 📋 Quick Commands for Setup

```bash
# After moving to separate repo, run these:

# 1. Install dependencies
composer install

# 2. Run tests
composer test

# 3. Run code style check
composer format:check

# 4. Fix code style
composer format

# 5. Tag release
git tag v1.0.0
git push origin v1.0.0
```

---

## 🚀 Release Checklist

- [x] All 🔴 Critical tasks complete (except screenshot)
- [x] At least 80% of 🟡 High Priority tasks complete
- [x] Tests passing
- [x] Code style clean
- [x] GitHub repository created
- [x] Package submitted to Packagist
- [x] Set up GitHub Hook for Packagist
- [ ] Tag v1.0.0 pushed
- [ ] Installation tested in fresh Laravel project

---

**Last Updated:** 2026-02-19
**Plugin Version:** 1.0.0-dev
