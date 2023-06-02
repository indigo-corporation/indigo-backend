<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(!app()->isProduction());

        JsonResource::withoutWrapping();
        ResourceCollection::withoutWrapping();

        Response::macro('success', function ($data = null, $status = 200) {
            return Response::json([
                'state' => true,
                'data' => $data,
            ], $status);
        });

        Response::macro('success_paginated', function ($data = null, $status = 200) {
            return Response::json($data, $status);
        });

        Response::macro('error', function ($error = ['code' => 666, 'message' => 'some very bad error..'], $status = 500) {
            return Response::json([
                'state' => false,
                'data' => [
                    'code' => $error['code'],
                    'message' => $error['message'],
                ],
            ], $status);
        });
    }
}
