<?php namespace Sas\Erp\Components;

use Datatables;
use RainLab\Builder\Components\RecordList as ComponentBase;

class RecordList extends ComponentBase
{
    public $dtRoute;

    public function defineProperties() {
        $properties = parent::defineProperties();
        $properties['slug'] = [
                'title' => 'sas.erp::lang.account.slug',
                'description' => 'sas.erp::lang.account.slug_desc',
                'default' => '{{ :slug }}',
                'type' => 'string',
        ];
        return $properties;
    }

    public function onRun() {
        $this->addCss('//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css');
        $this->addJs('//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js');

        $this->dtRoute = $this->page['dtRoute'] = '';

        //parent::onRun();
        parent::prepareVars();

        $this->records = $this->page['records'] = $this->listRecords();
    }

    protected function listRecords() {
        $modelClassName = $this->property('modelClass');
        if (!strlen($modelClassName) || !class_exists($modelClassName)) {
            throw new SystemException('Invalid model class name');
        }

        $model = new $modelClassName();
        $scope = $this->getScopeName($model);

        if ($scope !== null) {
            if ($scope == "belongPlace") {
                $owner = \Sas\Erp\Models\Place::where('code_id', '=', $this->property('slug'))->first();
                $this->page['owner'] = $owner;
                $model = $model->$scope($owner->id);
            } else {
                $model = $model->$scope();
            }
        }

        $model = $this->sort($model);
        $records = $this->paginate($model);

        return $records;
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function onGetData() {
        $modelClassName = $this->property('modelClass');
        if (!strlen($modelClassName) || !class_exists($modelClassName)) {
            throw new SystemException('Invalid model class name');
        }

        $model = new $modelClassName();
        $scope = $this->getScopeName($model);

        if ($scope !== null) {
            if ($scope == "belongPlace") {
                $owner = \Sas\Erp\Models\Place::where('code_id', '=', $this->property('slug'))->first();
                $this->page['owner'] = $owner;
                return Datatables::of($model->$scope($owner->id))->make(true);
            } else {
                return Datatables::of($model->$scope())->make(true);
            }
        }

        return Datatables::of($model->query())->make(true);
    }

}
