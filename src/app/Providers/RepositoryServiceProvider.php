<?php

namespace App\Providers;

use App\Interfaces\Repository\CategoryRepositoryInterface;
use App\Interfaces\Repository\NoteRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\NoteRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void{
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void{
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(NoteRepositoryInterface::class, NoteRepository::class);
    }
}
