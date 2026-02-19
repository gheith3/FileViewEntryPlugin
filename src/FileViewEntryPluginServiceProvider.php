<?php

namespace gheith3\FileViewEntryPlugin;

use Illuminate\Support\ServiceProvider;

class FileViewEntryPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FileViewEntryPlugin::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'file-view-entry-plugin');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/file-view-entry-plugin'),
        ], 'file-view-entry-plugin-views');
    }
}
