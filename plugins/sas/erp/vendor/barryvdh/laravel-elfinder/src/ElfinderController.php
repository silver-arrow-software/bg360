<?php namespace Barryvdh\Elfinder;

use Auth;
use Barryvdh\Elfinder\Session\LaravelSession;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Filesystem;

class ElfinderController extends Controller
{
    protected $package = 'elfinder';

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function showIndex()
    {
        return $this->app['view']
            ->make($this->package . '::elfinder')
            ->with($this->getViewVars());
    }

    public function showTinyMCE()
    {
        return $this->app['view']
            ->make($this->package . '::tinymce')
            ->with($this->getViewVars());
    }

    public function showTinyMCE4()
    {
        return $this->app['view']
            ->make($this->package . '::tinymce4')
            ->with($this->getViewVars());
    }

    public function showCKeditor4()
    {
        return $this->app['view']
            ->make($this->package . '::ckeditor4')
            ->with($this->getViewVars());
    }

    public function showPopup($input_id)
    {
        return $this->showFroala($input_id);
//        return $this->app['view']
//            ->make($this->package . '::standalonepopup')
//            ->with($this->getViewVars())
//            ->with(compact('input_id'));
    }

    public function showFroala($input_id)
    {
        return $this->app['view']
            ->make($this->package . '::froala')
            ->with($this->getViewVars())
            ->with(compact('input_id'));
    }

    public function showFilePicker($input_id)
    {
        $type = Request::input('type');
        return $this->app['view']
            ->make($this->package . '::filepicker')
            ->with($this->getViewVars())
            ->with(compact('input_id','type'));
    }

    public function showConnector()
    {
        $roots = $this->app->config->get('elfinder.roots', []);
        if (empty($roots)) {
            $user = Auth::getUser();
            $dirs = (array) $this->app['config']->get('elfinder.dir', []);
            foreach ($dirs as $dir) {
                $dir .= '/' . ($user ? $user->id . '_' .explode('@', $user->email)[0] : 0) . '/uploads';
                if (!$this->app->files->isDirectory($dir)) {
                    if ($this->app->files->makeDirectory($dir, 0777, true)) {
                        $this->app->files->put($dir. '.gitignore', "*\n!.gitignore");
                    } else {
                        throw new \Exception("Cannot create directory '$dir'..");
                    }
                }

                $roots[] = [
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => public_path($dir), // path to files (REQUIRED)
                    'URL' => url($dir), // URL to files (REQUIRED)
                    'uploadDeny'    => ['all'],                // All Mimetypes not allowed to upload
                    'uploadAllow'   => ['image', 'text/plain', 'application/zip'],// Mimetype `image` and `text/plain` allowed to upload
                    'uploadOrder'   => ['deny', 'allow'],      // allowed Mimetype `image` and `text/plain` only
                    'uploadMaxSize' => '2M',
                    'accessControl' => $this->app->config->get('elfinder.access') // filter callback (OPTIONAL)
                ];
            }

            $disks = (array) $this->app['config']->get('elfinder.disks', []);
            foreach ($disks as $key => $root) {
                if (is_string($root)) {
                    $key = $root;
                    $root = [];
                }
                $disk = app('filesystem')->disk($key);
                if ($disk instanceof FilesystemAdapter) {
                    $defaults = [
                        'driver' => 'Flysystem',
                        'filesystem' => $disk->getDriver(),
                        'alias' => $key,
                    ];
                    $roots[] = array_merge($defaults, $root);
                }
            }
        }

        if (app()->bound('session.store')) {
            $sessionStore = app('session.store');
            $session = new LaravelSession($sessionStore);
        } else {
            $session = null;
        }

        $rootOptions = $this->app->config->get('elfinder.root_options', array());
        foreach ($roots as $key => $root) {
            $roots[$key] = array_merge($rootOptions, $root);
        }

        $opts = $this->app->config->get('elfinder.options', array());
        $opts = array_merge($opts, ['roots' => $roots, 'session' => $session]);

        // run elFinder
        $connector = new Connector(new \elFinder($opts));
        $connector->run();
        /** @var \Symfony\Component\HttpFoundation\JsonResponse $jsonResponse */
        $jsonResponse = $connector->getResponse();
        if (Request::input('froala')) {
            $data = json_decode($jsonResponse->getContent(), true);
            if ($added = array_get($data, 'added')) {
                $data['link'] = array_get($added, '0.url');
            }
            $jsonResponse->setData($data);
        }
        return $jsonResponse;
    }

    protected function getViewVars()
    {
        $dir = 'plugins/sas/erp/assets/barryvdh/'.$this->package;
        $locale = str_replace("-",  "_", $this->app->config->get('app.locale'));
        if (!file_exists($this->app['path.public'] . "/$dir/js/i18n/elfinder.$locale.js")) {
            $locale = false;
        }
        $csrf = true;
        return compact('dir', 'locale', 'csrf');
    }
}
