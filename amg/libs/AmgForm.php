<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 1/27/2017
 * Time: 8:59 AM
 */
class AmgForm
{
    private $selectData = array();
    private $formMethod = "get";
    private $formAction = "";
    private $formClass = "formular";
    private $formId = "amg_form";

    /**
     * @return array
     */
    public function getSelectData()
    {
        return $this->selectData;
    }

    /**
     * @param array $selectData
     */
    public function setSelectData($selectData)
    {
        $this->selectData = $selectData;
    }


    /**
     * @return string
     */
    public function getFormMethod()
    {
        return $this->formMethod;
    }

    /**
     * @param string $formMethod
     */
    public function setFormMethod($formMethod)
    {
        $this->formMethod = $formMethod;
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->formAction;
    }

    /**
     * @param string $formAction
     */
    public function setFormAction($formAction)
    {
        $this->formAction = $formAction;
    }

    /**
     * @return string
     */
    public function getFormClass()
    {
        return $this->formClass;
    }

    /**
     * @param string $formClass
     */
    public function setFormClass($formClass)
    {
        $this->formClass = $formClass;
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * @param string $formId
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;
    }


    public function selectField($param = array()){
        $html = '';

        $html .= '<select name="' . $param['name'] . '" class="">';

        $dataArr = $this->getSelectData();


        foreach($dataArr as $data){

            $html .= '<option  value="">'.$data['title'].'</option>';
        }


        $html .= '</select>';

        return $html;
    }

    function formClose()
    {
        return '</form>';
    }

    public function formTag(){
        $formMethod = $this->getFormMethod();
        $formAction = $this->getFormAction();
        $formClass = $this->getFormClass();
        $formId = $this->getFormId();

        $html = '';
        $html .= '<form id="'.$formId.'" name="amg_form" method="'.$formMethod.'" class="'.$formClass.'" action="'.$formAction.'">';

        return $html;
    }

}