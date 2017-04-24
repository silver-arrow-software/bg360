<?php namespace Sas\Erp\Components;

use Lang;
use Flash;
use Request;
use Redirect;
use Exception;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;


class FroalaEditor extends ComponentBase
{

    public $editorName;

    public function componentDetails()
    {
        return [
            'name'        => 'Froala Editor',
            'description' => 'Rich text editor'
        ];
    }

    public function defineProperties()
    {
        return [
            'editorName' => [
                'title' => 'Input name',
                'description' => 'Name of editor control',
                'type' => 'string',
                //'depends' => ['displayColumn'],
                'validation' => [
                    'required' => [
                        'message' => 'Please set input name'
                    ]
                ],
                'showExternalParam' => false
            ],
        ];
    }


    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $this->addCss('assets/css/froala_editor.pkgd.css');
        $this->addCss('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css');
        $this->addCss('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css');
        $this->addJs('assets/js/froala_editor.pkgd.min.js');

        $this->editorName = $this->page['editorName'] = $this->property('editorName');
    }

}
