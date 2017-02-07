<?php namespace Sas\Erp\Components;

use Yajra\Datatables\Datatables;
use RainLab\Builder\Components\RecordList as ComponentBase;
use Sas\Erp\Models\Task;

class RecordList extends ComponentBase
{

    public $dtRoute;

    public function onRun()
    {
        $this->addCss('//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css');
        $this->addJs('//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js');

        $this->dtRoute = $this->page['dtRoute'] = '';

        parent::onRun();
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function onGetData()
    {
        return Datatables::of(Task::query())->make(true);
    }

}