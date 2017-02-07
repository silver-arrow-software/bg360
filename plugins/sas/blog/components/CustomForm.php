<?php namespace Sas\Blog\Components;

use October\Rain\Support\Facades\Flash;
use Validator;
use ValidationException;
use Lang;
use Cms\Classes\ComponentBase;
use RainLab\Builder\Classes\ComponentHelper;;
use SystemException;
use Redirect;
use Carbon\Carbon;
use Cms\Classes\Page;
use Debugbar;

class CustomForm extends ComponentBase
{
    /**
     * A model instance to display
     * @var \October\Rain\Database\Model
     */
    public $record = null;

    /**
     * Message to display if the record is not found.
     * @var string
     */
    public $notFoundMessage;

    /**
     * Model column to display on the details page.
     * @var string
     */
    public $displayColumn;

    /**
     * Model column to use as a record identifier for fetching the record from the database.
     * @var string
     */
    public $modelKeyColumn;

    /**
     * Identifier value to load the record from the database.
     * @var string
     */
    public $identifierValue;

    public function componentDetails()
    {
        return [
            'name'        => 'Custom Form',
            'description' => 'Load a custom form to submit on frontend'
        ];
    }

    //
    // Properties
    //

    public function defineProperties()
    {
        return [
            'modelClass' => [
                'title'       => 'rainlab.builder::lang.components.details_model',
                'type'        => 'dropdown',
                'showExternalParam' => false
            ],
            'identifierValue' => [
                'title'       => 'rainlab.builder::lang.components.details_identifier_value',
                'description' => 'rainlab.builder::lang.components.details_identifier_value_description',
                'type'        => 'string',
                'default'     => '{{ :id }}',
                'validation'  => [
                    'required' => [
                        'message' => Lang::get('rainlab.builder::lang.components.details_identifier_value_required')
                    ]
                ]
            ],
            'modelKeyColumn' => [
                'title'       => 'rainlab.builder::lang.components.details_key_column',
                'description' => 'rainlab.builder::lang.components.details_key_column_description',
                'type'        => 'autocomplete',
                'default'     => 'id',
                'validation'  => [
                    'required' => [
                        'message' => Lang::get('rainlab.builder::lang.components.details_key_column_required')
                    ]
                ],
                'showExternalParam' => false
            ],
            'displayColumn' => [
                'title'       => 'rainlab.builder::lang.components.details_display_column',
                'description' => 'rainlab.builder::lang.components.details_display_column_description',
                'type'        => 'autocomplete',
                'depends'     => ['modelClass'],
                'validation'  => [
                    'required' => [
                        'message' => Lang::get('rainlab.builder::lang.components.details_display_column_required')
                    ]
                ],
                'showExternalParam' => false
            ],
            'sasEmbedCode' => [
                'title'       => 'sas.blog::lang.embedposts.embed_title',
                'description' => 'sas.blog::lang.embedposts.embed_desc',
                'default' => '{{:slug}}',
                'type'        => 'string',
            ],
            'slugType'       => [
                'title' => 'sas.erp::lang.account.slug_type',
                'description' => 'sas.erp::lang.account.slug_type_desc',
                'default' => 'PLACE',
                'type' => 'dropdown',
                'options' => ['PLACE', 'USER']
            ],
            'notFoundMessage' => [
                'title'       => 'rainlab.builder::lang.components.details_not_found_message',
                'description' => 'rainlab.builder::lang.components.details_not_found_message_description',
                'default'     => Lang::get('rainlab.builder::lang.components.details_not_found_message_default'),
                'type'        => 'string',
                'showExternalParam' => false
            ]
        ];
    }

    public function getModelClassOptions() {
        return ComponentHelper::instance()->listGlobalModels();
    }

    public function getModelKeyColumnOptions() {
        return ComponentHelper::instance()->listModelColumnNames();
    }

    public function getDisplayColumnOptions() {
        return ComponentHelper::instance()->listModelColumnNames();
    }

    //
    // Rendering and processing
    //
    public function onRun() {
        $this->prepareVars();
    }

    protected function prepareVars() {
        $this->notFoundMessage = $this->page['notFoundMessage'] = Lang::get($this->property('notFoundMessage'));
        $this->displayColumn = $this->page['displayColumn'] = $this->property('displayColumn');
        $this->modelKeyColumn = $this->page['modelKeyColumn'] = $this->property('modelKeyColumn');
        $this->identifierValue = $this->page['identifierValue'] = $this->property('identifierValue');

        if (!strlen($this->displayColumn)) {
            throw new SystemException('The display column name is not set.');
        }

        if (!strlen($this->modelKeyColumn)) {
            throw new SystemException('The model key column name is not set.');
        }
    }

    public function onCustomFormSubmit() {
        $data = post();

        $redirectLink = '';
        switch ($this->property('slugType')) {
            case '0':
                $redirectLink = '/place/';
                break;
            case '1':
                    $redirectLink = '/profile/';
                    break;
        }
        $redirectLink .= $this->property('sasEmbedCode') . "/blogs";

        $rules = [
            'postTitle' => 'required',
            'postContent' => 'required',
        ];

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $new_post = new \Sas\Blog\Models\Post([
            'title' => $data['postTitle'],
            'content' => $data['postContent'],
            'slug' => str_slug($data['postTitle']),
            'published' => 1,
            'sas_embed_code' => $this->property('sasEmbedCode'),
            'published_at' => Carbon::now(),
        ]);
        $new_post->save();

        return Redirect::to($redirectLink)->withInput();
        //return Redirect::to($redirectLink)->with('message', 'Đã lưu dữ liệu thành công!');
        //return Redirect::back()->with('message', 'Đã lưu dữ liệu thành công!');
        // return [
        //     '#myDiv' => $this->renderPartial('@result')
        // ];
    }
}
