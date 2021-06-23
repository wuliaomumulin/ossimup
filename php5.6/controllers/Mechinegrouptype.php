<?php

use App\Models\Mechinegrouptype;


class MechinegrouptypeController extends Base
{
    protected $model;
    /**
     * 
     */
    public function init()
    {
        parent::init();
        $this->model = new Mechinegrouptype();
    }

    public function queryListAction()
    {

        jsonResult($this->model->getAll());

    }
}
?>