<?php

use gheith3\FileViewEntryPlugin\Infolists\Components\FileViewEntry;

it('can create file view entry', function () {
    $entry = FileViewEntry::make('file_path');

    expect($entry)->toBeInstanceOf(FileViewEntry::class);
});

it('can set and get grid columns', function () {
    $entry = FileViewEntry::make('files')
        ->grid(4);

    expect($entry->getGridColumns())->toBe(4);
});

it('can set and get custom keys', function () {
    $entry = FileViewEntry::make('documents')
        ->titleKey('title')
        ->pathKey('document_path')
        ->dateKey('uploaded_at');

    expect($entry->getTitleKey())->toBe('title')
        ->and($entry->getPathKey())->toBe('document_path')
        ->and($entry->getDateKey())->toBe('uploaded_at');
});

it('has default keys', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getTitleKey())->toBe('name')
        ->and($entry->getPathKey())->toBe('file_path')
        ->and($entry->getDateKey())->toBeNull();
});

it('can set and get preview options', function () {
    $entry = FileViewEntry::make('files')
        ->downloadable()
        ->previewHeight('500px');

    expect($entry->isDownloadable())->toBeTrue()
        ->and($entry->getPreviewHeight())->toBe('500px');
});

it('can set and get disk', function () {
    $entry = FileViewEntry::make('files')
        ->disk('s3');

    expect($entry->getDiskName())->toBe('s3');
});

it('detects image files', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getFileType('photo.jpg'))->toBe('image')
        ->and($entry->getFileType('photo.jpeg'))->toBe('image')
        ->and($entry->getFileType('photo.png'))->toBe('image')
        ->and($entry->getFileType('photo.gif'))->toBe('image')
        ->and($entry->getFileType('photo.webp'))->toBe('image');
});

it('detects video files', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getFileType('video.mp4'))->toBe('video')
        ->and($entry->getFileType('video.mov'))->toBe('video')
        ->and($entry->getFileType('video.avi'))->toBe('video');
});

it('detects audio files', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getFileType('audio.mp3'))->toBe('audio')
        ->and($entry->getFileType('audio.wav'))->toBe('audio')
        ->and($entry->getFileType('audio.ogg'))->toBe('audio');
});

it('detects pdf files', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getFileType('document.pdf'))->toBe('pdf');
});

it('detects text files', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getFileType('readme.txt'))->toBe('text')
        ->and($entry->getFileType('readme.md'))->toBe('text');
});

it('returns other for unknown extensions', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getFileType('file.xyz'))->toBe('other')
        ->and($entry->getFileType('file.unknown'))->toBe('other');
});

it('determines if file can be previewed', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->canPreviewInBrowser('image.jpg'))->toBeTrue()
        ->and($entry->canPreviewInBrowser('video.mp4'))->toBeTrue()
        ->and($entry->canPreviewInBrowser('audio.mp3'))->toBeTrue()
        ->and($entry->canPreviewInBrowser('doc.pdf'))->toBeTrue()
        ->and($entry->canPreviewInBrowser('readme.txt'))->toBeTrue()
        ->and($entry->canPreviewInBrowser('file.xyz'))->toBeFalse();
});

it('returns correct icons for file types', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getFileIcon('image'))->toBe('heroicon-o-photo')
        ->and($entry->getFileIcon('video'))->toBe('heroicon-o-video-camera')
        ->and($entry->getFileIcon('audio'))->toBe('heroicon-o-musical-note')
        ->and($entry->getFileIcon('pdf'))->toBe('heroicon-o-document-text')
        ->and($entry->getFileIcon('other'))->toBe('heroicon-o-document');
});

it('has default preview height', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->getPreviewHeight())->toBe('300px');
});

it('can set preview height as integer', function () {
    $entry = FileViewEntry::make('files')
        ->previewHeight(400);

    expect($entry->getPreviewHeight())->toBe('400px');
});

it('has default asModal set to true', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->shouldShowAsModal())->toBeTrue();
});

it('can disable asModal', function () {
    $entry = FileViewEntry::make('files')
        ->asModal(false);

    expect($entry->shouldShowAsModal())->toBeFalse();
});

it('can enable asModal explicitly', function () {
    $entry = FileViewEntry::make('files')
        ->asModal(true);

    expect($entry->shouldShowAsModal())->toBeTrue();
});

it('has default withModalEye set to false', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->shouldShowWithModalEye())->toBeFalse();
});

it('can enable withModalEye', function () {
    $entry = FileViewEntry::make('files')
        ->asModal(false)
        ->withModalEye(true);

    expect($entry->shouldShowAsModal())->toBeFalse()
        ->and($entry->shouldShowWithModalEye())->toBeTrue();
});

it('can disable withModalEye explicitly', function () {
    $entry = FileViewEntry::make('files')
        ->withModalEye(false);

    expect($entry->shouldShowWithModalEye())->toBeFalse();
});

it('has default contained set to true', function () {
    $entry = FileViewEntry::make('files');

    expect($entry->isContained())->toBeTrue();
});

it('can disable contained', function () {
    $entry = FileViewEntry::make('files')
        ->contained(false);

    expect($entry->isContained())->toBeFalse();
});

it('can enable contained explicitly', function () {
    $entry = FileViewEntry::make('files')
        ->contained(true);

    expect($entry->isContained())->toBeTrue();
});
