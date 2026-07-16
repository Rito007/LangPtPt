<?php

namespace Rito007\LangPtPt\Providers;

use Illuminate\Support\ServiceProvider;
use Rito007\LangPtPt\Console\SeedDemoPTPTCommand;

class LangPtPtServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->registerCommands();
    }
    public function boot(): void
    {
        $this->callAfterResolving('view', function ($view) {
            $view->prependNamespace('installer', __DIR__ . '/../Resources/views');
        });

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../Resources/lang/vendor' => lang_path('vendor'),
            ], 'bagisto-lang-pt-pt');
        }
    }
    /**
     * Register the commands for this package.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedDemoPTPTCommand::class,
            ]);
        }
    }
}
