<?php

namespace CodeDistortion\TagTools\Laravel;

use CodeDistortion\TagTools\Settings;use CodeDistortion\TagTools\TagCss;
use CodeDistortion\TagTools\TagDns;
use CodeDistortion\TagTools\TagFav;
use CodeDistortion\TagTools\TagJs;
use Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * TagTools ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Service-provider register method.
     *
     * @return void
     */
    public function register(): void
    {
        // register TagCss as a singleton
        $this->app->singleton(
            TagCss::getAlias(),
            function ($app) {

                $isOnVapor = (($_ENV['SERVER_SOFTWARE'] ?? null) === 'vapor');
                $cacheDir = $isOnVapor
                    ? '/tmp/storage/bootstrap/cache/'
                    : app()->bootstrapPath('cache/');

                return new TagCss(new Filesystem(), $cacheDir);
            }
        );

        // register TagDns as a singleton
        $this->app->singleton(
            TagDns::getAlias(),
            function ($app) {
                return new TagDns();
            }
        );

        // register TagFav as a singleton
        $this->app->singleton(
            TagFav::getAlias(),
            function ($app) {
                return new TagFav();
            }
        );

        // register TagJs as a singleton
        $this->app->singleton(
            TagJs::getAlias(),
            function ($app) {
                return new TagJs(new Filesystem());
            }
        );
    }

    /**
     * Service-provider boot method.
     *
     * @return void
     * @throws BindingResolutionException Thrown when a class cannot be bound.
     */
    public function boot(Router $router): void
    {
        $this->initialiseConfig();
        $this->registerTestViewPath();
        $this->registerMiddleware($router);
        $this->registerBladeDirectives();
    }

    /**
     * Initialise the config settings file.
     *
     * @return void
     */
    private function initialiseConfig(): void
    {
        // initialise the config
        $configPath = __DIR__.'/../../config/config.php';
        $this->mergeConfigFrom($configPath, Settings::LARAVEL_CONFIG_NAME);

        // allow the default config to be published
        if ((!$this->app->environment('testing'))
            && ($this->app->runningInConsole())) {

            $this->publishes(
                [$configPath => config_path(Settings::LARAVEL_CONFIG_NAME.'.php'),],
                'config'
            );
        }
    }

    /**
     * Specify where the views are, for testing only.
     *
     * @return void
     */
    private function registerTestViewPath(): void
    {
        if ($this->app->environment('testing')) {
            $this->loadViewsFrom(__dir__.'/../../tests/Laravel/Integration/views', 'test');
        }
    }

    /**
     * Register the middleware.
     *
     * @return void
     * @throws BindingResolutionException Thrown when the class cannot be bound.
     */
    private function registerMiddleware(Router $router): void
    {
        $router->middleware('tag-tools', Middleware::class);
        $router->pushMiddlewareToGroup('web', Middleware::class);
    }


    /**
     * Register the "@" directives used in blade templates.
     *
     * @return void
     */
    private function registerBladeDirectives(): void
    {
        Blade::directive(
            TagCss::getBladeDirective(),
            function ($expression) {
                return '<?php echo TagCss::generate(); ?>';
            }
        );
        Blade::directive(
            TagDns::getBladeDirective(),
            function ($expression) {
                return '<?php echo TagDns::generate(); ?>';
            }
        );
        Blade::directive(
            TagFav::getBladeDirective(),
            function ($expression) {
                return '<?php echo TagFav::generate(); ?>';
            }
        );
        Blade::directive(
            TagJs::getBladeDirective(),
            function ($expression) {
                return '<?php echo TagJs::generate(); ?>';
            }
        );
    }
}
