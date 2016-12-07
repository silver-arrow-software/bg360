<?php namespace Sas\Api;

use System\Classes\PluginBase;
use App;
use Illuminate\Foundation\AliasLoader;

/**
 * Api Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Plugin dependency
     * @var array
     */
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'sas.api::lang.plugin.name',
            'description' => 'sas.api::lang.plugin.description',
            'author'      => 'SanBT',
            'icon'        => 'icon-user-secret'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

        // Register ServiceProviders
        App::register(\Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class);

        // Register aliases
        $alias = AliasLoader::getInstance();
        $alias->alias('JWTAuth', \Tymon\JWTAuth\Facades\JWTAuth::class);
        $alias->alias('JWTFactory', \Tymon\JWTAuth\Facades\JWTFactory::class);

        App::singleton('auth', function ($app) {
            return new \Illuminate\Auth\AuthManager($app);
        });

        // Register router middlewares
        $this->app['router']->middleware('jwt.auth', '\Tymon\JWTAuth\Middleware\GetUserFromToken');
        $this->app['router']->middleware('jwt.refresh', '\Tymon\JWTAuth\Middleware\RefreshToken');

    }

}
