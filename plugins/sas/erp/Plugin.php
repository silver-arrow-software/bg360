<?php namespace Sas\Erp;

use Yaml;
use File;
use Event;
use Backend;
use App;
use System\Classes\PluginBase;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UsersController;
use Sas\Blog\Controllers\Posts as PostsController;
use Sas\Blog\Models\Post as PostModel;
use Sas\Erp\Models\Tag;
use Illuminate\Foundation\AliasLoader;
use Config;

class Plugin extends PluginBase
{
    public $require = ['RainLab.User', 'RainLab.Location', 'Sas.Blog', 'Sas.Forum'];

    /**
     * @var array   Container for tags to be attached
     */
    private $tags = [];

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot() {
        //$this->bootPackages();

        Event::listen('backend.menu.extendItems', function($manager) {
           $manager->addSideMenuItems('RainLab.User', 'user', [
                'sas-erp-side-menu-profiles' => [
                    'label' => 'sas.erp::lang.plugin.profiles',
                    'icon' => 'icon-file-powerpoint-o',
                    'code' => 'profiles',
                    'owner' => 'RainLab.User',
                    'url' => Backend::url('sas/erp/profiles')
                ],
                'rainlab-side-menu-users' => [
                    'label' => 'rainlab.user::lang.users.menu_label',
                    'icon' => 'icon-users',
                    'code' => 'users',
                    'owner' => 'RainLab.User',
                    'url' => Backend::url('rainlab/user/users')
                ],
            ]);
        });

        UserModel::extend(function($model){
            $model->belongsTo['profile'] = ['Sas\Erp\Models\Profile'];
            $model->belongsTo['company'] = ['Sas\Erp\Models\Place'];
            $model->morphMany['accounts'] =  [
                'Sas\Erp\Models\Account',
                'name' => 'accountable'
            ];
            $model->addFillable([
                'code',
                'mobile',
                'street_addr',
                'district',
                'state',
                'country'
            ]);

            $model->implement[] = 'RainLab.Location.Behaviors.LocationModel';

            $model->addDynamicMethod('getCodeAttribute', function($value) use ($model) {
                return $value ? $value : uniqid();
            });
        });

        UsersController::extendListColumns(function ($list) {
            $list->addColumns([
                'profile' => [
                    'label' => 'sas.erp::lang.plugin.profile',
                    'relation' => 'profile',
                    'select' => 'name'
                ]
            ]);
        });

        UsersController::extendFormFields(function ($widget) {
            $configFile = __DIR__ . '/config/profile_fields.yaml';
            $config = Yaml::parse(File::get($configFile));
            $widget->addTabFields($config);
        });

        // Extend the Blog controller
        PostsController::extendFormFields(function($form, $model, $context) {
            if (!$model instanceof PostModel) return;
            $form->addSecondaryTabFields([
                'tagbox' => [
                    'label'     => 'sas.erp::lang.common.tags',
                    'tab'       => 'rainlab.blog::lang.post.tab_categories',
                    'type'      => 'owl-tagbox',
                    'slugify'   => false
                ]
            ]);
        });

        // Extend the Blog model
        PostModel::extend(function($model) {
            // Relationship
            $model->morphToMany['tags'] = [
                'Sas\Erp\Models\Tag',
                'table' => 'sas_erp_taggables',
                'name' => 'taggable'
            ];

            // getTagboxAttribute()
            $model->addDynamicMethod('getTagboxAttribute', function() use ($model) {
                return $model->tags()->lists('name');
            });

            // setTagboxAttribute()
            $model->addDynamicMethod('setTagboxAttribute', function($tags) use ($model) {
                $this->tags = $tags;
            });
        });

        // Attach tags to Blog model
        PostModel::saved(function($model) {
            if ($this->tags) {
                $ids = [];
                foreach ($this->tags as $name) {
                    $create = Tag::firstOrCreate(['name' => $name]);
                    $ids[] = $create->id;
                }

                $model->tags()->sync($ids);
            }
        });
    }

    public function register() {
        $alias = AliasLoader::getInstance();
        $alias->alias('SasCart', 'Sas\Erp\Facades\Cart');

        App::singleton('cart', function() {
            return new \Sas\Erp\Classes\Cart;
        });

        \Validator::extend("emails", function($attribute, $value, $parameters) {
            $rules = [
                'email' => 'required|email',
            ];

            if (!is_array($value)) {
                $emails = explode(',', $value);
            }
            else {
                $emails = [$value];
            }

            foreach ($emails as $email) {
                $data = [
                    'email' => trim($email)
                ];
                $validator = \Validator::make($data, $rules);
                if ($validator->fails()) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents() {
        return [
            'Sas\Erp\Components\Cart' => 'cart',
            'Sas\Erp\Components\Orders' => 'orders',
            'Sas\Erp\Components\Checkout' => 'checkout',
            'Sas\Erp\Components\OrderDisplay' => 'orderDisplay',
            'Sas\Erp\Components\Products' => 'products',
            'Sas\Erp\Components\ProductDisplay' => 'productDisplay',
            'Sas\Erp\Components\Places' => 'places',
            'Sas\Erp\Components\Place' => 'place',
            'Sas\Erp\Components\MoneyAccounts' => 'moneyAccounts',
            'Sas\Erp\Components\MoneyAccount' => 'moneyAccount',
            'Sas\Erp\Components\Feedback' => 'feedback',
            'Sas\Erp\Components\RecordList' => 'sasErpList',
            'Sas\Erp\Components\RecordDetail' => 'sasErpDetail',
            'Sas\Erp\Components\ProjectDetail' => 'sasErpProject',
        ];
    }

    public function registerSettings() {
        return [
            'settings' => [
                'label' => 'sas.erp::lang.settings.menu_label',
                'description' => 'sas.erp::lang.settings.menu_description',
                'category' => 'SAS',
                'icon' => 'icon-cog',
                'class' => 'Sas\Erp\Models\Settings',
                'order' => 200
            ],
            'channels' => [
                'label' => 'sas.erp::lang.feedback.channel.many',
                'description' => 'sas.erp::lang.feedback.navigation.menu.settings.channels.description',
                'category' => 'SAS',
                'icon' => 'icon-arrows',
                'url' => \Backend::url('sas/erp/feedbackchannels'),
                'order' => 500,
                'keywords' => 'feedback channel',
                'permissions' => ['sas.erp.config_feedback']
            ]
        ];
    }

    /**
     * Registers any form widgets implemented in this plugin.
     */
    public function registerFormWidgets() {
        return [
            'Owl\FormWidgets\Tagbox\Widget' => [
                'label' => 'TagBox',
                'code'  => 'owl-tagbox'
            ],
            'Owl\FormWidgets\Knob\Widget' => [
                'label' => 'Knob',
                'code'  => 'owl-knob'
            ],
            'Owl\FormWidgets\Money\Widget' => [
                'label' => 'Money',
                'code' => 'owl-money'
            ],
        ];
    }

    public function registerListColumnTypes() {
        return [
            // A local method, i.e $this->evalUppercaseListColumn()
            'currency' => [$this, 'formatCurrency'],
            'qrcode' => [$this, 'viewQRCode'],

            // Using an inline closure
            'format_number' => function($value) { return number_format($value, 0, ',', '.'); }
        ];
    }

    public function formatCurrency($value, $column, $record) {
        return number_format($value, 0, ',', '.') . ' đ';
    }

    public function viewQRCode($value, $column, $record) {
        $return_value = "<img src='data:image/png;base64, {{ barcodePNG({data: '". $value . "', type: 'QRCODE', color: [0,0,0], width: '5', height: '5'}) }}' alt='barcode' />";
        return $return_value;
    }

    /**
     * Registers any mail templates implemented by this plugin.
     * The templates must be returned in the following format:
     * ['acme.blog::mail.welcome' => 'This is a description of the welcome template'],
     * ['acme.blog::mail.forgot_password' => 'This is a description of the forgot password template'],
     */
    public function registerMailTemplates() {
        return [
            'sas.erp::base-email' => \Lang::get('sas.erp::lang.mail_template.description')
        ];
    }

    /*
        ML: đăng ký sử dụng các package của Laravel
    */
    public function bootPackages() {
        // Get the namespace of the current plugin to use in accessing the Config of the plugin
        $pluginNamespace = str_replace('\\', '.', strtolower(__NAMESPACE__));

        // Instantiate the AliasLoader for any aliases that will be loaded
        $aliasLoader = AliasLoader::getInstance();

        // Get the packages to boot
        $packages = Config::get($pluginNamespace . '::packages');

        // Boot each package
        foreach ($packages as $name => $options) {
            // Setup the configuration for the package, pulling from this plugin's config
            if (!empty($options['config'] && !empty($options['config_namespace']))) {
                Config::set($options['config_namespace'], $options['config']);
            }

            // Register any Service Providers for the package
            if (!empty($options['providers'])) {
                foreach ($options['providers'] as $provider) {
                    App::register($provider);
                }
            }

            // Register any Aliases for the package
            if (!empty($options['aliases'])) {
                foreach ($options['aliases'] as $alias => $path) {
                    $aliasLoader->alias($alias, $path);
                }
            }
        }
    }

    /*public function registerNavigation() {
        return [
            'sas-erp-main-menu-item' => [
                'label' => 'sas.erp::lang.plugin.name',
                'url' => Backend::url('sas/erp/profiles'),
                'icon' => 'icon-location-arrow',
                'order' => 500,
                'sideMenu' => [
                    'sas-erp-side-menu-teams' => [
                        'label' => 'Teams',
                        'icon' => 'icon-users',
                        'url' => Backend::url('sas/project/teams'),
                        'permissions' => ['sas.project.manage_teams']
                    ],
                    'sas-erp-side-menu-projects' => [
                        'label' => 'Projects',
                        'icon' => 'icon-files',
                        'url' => Backend::url('sas/project/projects'),
                        'permissions' => ['sas.project.manage_projects']
                    ]
                ]
            ]
        ];
    }*/
}
