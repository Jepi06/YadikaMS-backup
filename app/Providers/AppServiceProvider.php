<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Jurusan;

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
        Model::preventLazyLoading(!app()->isProduction());
        Paginator::useBootstrapFive();

        // Sidebar per-jurusan di panel admin SPMB selalu tersedia,
        // tanpa perlu di-pass manual dari setiap controller.
        View::composer('spmb.layouts.admin', function ($view) {
            $view->with('sidebarJurusan', Jurusan::orderBy('nama')->withCount('pendaftar')->get());
        });

        // $lmsUser dipakai di layout LMS DAN di section content-nya (guru/siswa
        // dashboard). Dengan @extends, section content dieksekusi SEBELUM
        // layout-nya, jadi @php lokal di layout saja tidak cukup — harus
        // di-share lewat composer supaya tersedia di semua view 'lms.*'.
        View::composer('lms.*', function ($view) {
            $view->with('lmsUser', Auth::guard('lms')->user());
        });
         if (config('app.env') !== 'local') {
        \URL::forceScheme('https');
    }
    }
}
