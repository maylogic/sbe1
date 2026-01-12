<?php

namespace App\Providers;

use App\Livewire\BaseTextAreasEditor;
use App\Services\CartService;
use App\Services\SalesRuleService;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(SalesRuleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        DB::listen(function ($query) {
//            Log::info(
//                $query->sql,
//                [
//                    'bindings' => $query->bindings,
//                    'time' => $query->time
//                ]
//            );
//        });
        Livewire::component('base-text-areas-editor', BaseTextAreasEditor::class);
    }
}
