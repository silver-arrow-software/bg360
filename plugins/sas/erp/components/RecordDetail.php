<?php namespace Sas\Erp\Components;

use Lang;
use SystemException;
use RainLab\Builder\Components\RecordDetails as ComponentBase;
use RainLab\Builder\Classes\ComponentHelper;

class RecordDetail extends ComponentBase
{

    protected $defaultModelClass = 'Sas\Erp\Models\Project';
    protected $defaultDisplayColumn = 'name';

    public function getScopeOptions()
    {
        $modelClass = ComponentHelper::instance()->getModelClassDesignTime();

        $result = [
            '-' => Lang::get('rainlab.builder::lang.components.list_scope_default')
        ];
        try {
            $methods = get_class_methods($modelClass);

            foreach ($methods as $method) {
                if (preg_match('/scope[A-Z].*/', $method)) {
                    $result[$method] = $method;
                }
            }
        }
        catch (Exception $ex) {
            // Ignore invalid models
        }

        return $result;
    }

    //
    // Properties
    //

    public function defineProperties()
    {
        $properties = parent::defineProperties();
        $properties['modelClass']['default'] = $this->defaultModelClass;
        $properties['displayColumn']['default'] = $this->defaultDisplayColumn;
        $properties['scope'] = [
            'title'       => 'rainlab.builder::lang.components.list_scope',
            'description' => 'rainlab.builder::lang.components.list_scope_description',
            'type'        => 'dropdown',
            'depends'     => ['modelClass'],
            'showExternalParam' => false
        ];
        return $properties;
    }


    protected function loadRecord()
    {
        if (!strlen($this->identifierValue)) {
            return;
        }

        $modelClassName = $this->property('modelClass');
        if (!strlen($modelClassName) || !class_exists($modelClassName)) {
            throw new SystemException('Invalid model class name');
        }

        $model = new $modelClassName();

        $scope = $this->getScopeName($model);

        if ($scope !== null) {
            $model = $model->$scope();
        }

        return $model->where($this->modelKeyColumn, '=', $this->identifierValue)->first();
    }

    protected function getScopeName($model)
    {
        $scopeMethod = trim($this->property('scope'));
        if (!strlen($scopeMethod) || $scopeMethod == '-') {
            return null;
        }

        if (!preg_match('/scope[A-Z].+/', $scopeMethod)) {
            throw new SystemException('Invalid scope method name.');
        }

        if (!method_exists($model, $scopeMethod)) {
            throw new SystemException('Scope method not found.');
        }

        return lcfirst(substr($scopeMethod, 5));
    }

}