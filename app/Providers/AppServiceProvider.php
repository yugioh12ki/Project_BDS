<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;

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
        // Configure Paginator to use Bootstrap
        Paginator::useBootstrap();

        // Create documents directory
        Storage::makeDirectory('documents');
        // Thêm blade directive để hiển thị tên bất động sản
        Blade::directive('propertyTitle', function ($expression) {
            return "<?php
                \$propertyTitle = 'Không xác định';
                if ($expression) {
                    \$property = \\App\\Models\\Property::find($expression);
                    if (\$property && \$property->Title) {
                        \$propertyTitle = \$property->Title;
                    }
                }
                echo \$propertyTitle;
            ?>";
        });
    }
}
