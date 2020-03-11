<?php
namespace OTIFSolutions\LaravelSocial;
use Illuminate\Support\ServiceProvider;


class LaravelSocialServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-social');
        
        //$this->app['router']->aliasMiddleware('role', Http\Middleware\UserRole::class);
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\DBCleanUp::class
//                 Commands\InstaUpdatePosts::class,
//                 Commands\InstaGetPosts::class,
//                 Commands\InstaNewPosts::class,
            ]);
        }
    }

    public function register()
    {

    }

}
?>
