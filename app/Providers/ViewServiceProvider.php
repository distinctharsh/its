<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Breadcrumb;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Use view composer for the master layout
        View::composer('layouts.breadcrumb', function ($view) {
            $routeName = request()->route()->getName();
            $currentBreadcrumb = Breadcrumb::where('route_name', $routeName)->first();
            $breadcrumbPath = $currentBreadcrumb ? $currentBreadcrumb->getBreadcrumbPath() : [];
            $view->with('breadcrumbPath', $breadcrumbPath);
        });
    }

    public function register()
    {
        //
    }
}

