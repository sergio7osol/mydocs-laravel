<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Document;
use App\Models\User;

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
        // \Illuminate\Database\Eloquent\Model::preventLazyLoading(!app()->isProduction());
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.default');
        
        Gate::before(function (User $user, ?string $ability = null) {
          return $user->is_admin ? true : null;
        });
        
        Gate::define('edit-document', function (User $user, Document $document) {
          // Allow only if the document's category owner matches the authenticated user
          return $document->category && $document->category->user && $document->category->user->is($user);
        });

        Gate::define('delete-document', function (User $user, Document $document) {
          return $document->category && $document->category->user && $document->category->user->is($user);
        });
    }
}
