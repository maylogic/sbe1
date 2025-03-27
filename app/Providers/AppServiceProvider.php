<?php

namespace App\Providers;

use App\Livewire\BaseTextAreasEditor;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('base-text-areas-editor', BaseTextAreasEditor::class);
    }
}
