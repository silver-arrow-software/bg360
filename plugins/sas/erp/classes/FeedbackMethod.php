<?php
namespace Sas\Erp\Classes;


interface FeedbackMethod
{

    /**
     * Used to register new form fields to Channel.
     * Modify and prepare Channel model.
     *
     * @return void
     */
    public function boot();

    /**
     * @param array $methodData
     * @param array $data
     * @return mixed
     */
    public function send($methodData, $data);

}