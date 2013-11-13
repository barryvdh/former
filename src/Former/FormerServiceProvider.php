<?php
namespace Former;

use Former\Former;
use Former\Populator;
use Illuminate\Config\FileLoader as ConfigLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Register the Former package with the Laravel framework
 */
class FormerServiceProvider extends ServiceProvider
{
  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = true;

  /**
   * Register Former's package with Laravel
   *
   * @return void
   */
  public function register()
  {
    $this->app = static::make($this->app);
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array('former');
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CLASS BINDINGS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Create a Former container
   *
   * @param  Container $app
   *
   * @return Container
   */
  public static function make($app = null)
  {
    if (!$app) {
      $app = new Container;
    }

    // Bind classes to container
    $provider = new static($app);
    $app      = $provider->bindFormer($app);

    return $app;
  }


  /**
   * Bind Former classes to the container
   *
   * @param  Container $app
   *
   * @return Container
   */
  public function bindFormer(Container $app)
  {
    // Add config namespace
    $app['config']->package('anahkiasen/former', __DIR__.'/../config');
    
    // Get framework to use
    $framework = $app['config']->get('former::framework');

    $frameworkClass = '\Former\Framework\\'.$framework;
    $app->bind('former.framework', function ($app) use ($frameworkClass) {
      return new $frameworkClass($app);
    });

    $app->singleton('former.populator', function ($app) {
      return new Populator;
    });

    $app->singleton('former', function ($app) {
      return new Former($app, new MethodDispatcher($app));
    });

    Helpers::setApp($app);

    return $app;
  }
}
