<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/12/18
 * Time: 5:18 PM
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . "SqlCrud.php";
class FieldsType extends SqlCrud{

    //field datatypes
    protected $field_datatype = array(); //$field_datatype[field] = datatype
    protected $amgInputDataType;




    /**
     * @return array
     */
    public function getAmgInputDataType()
    {
        return $this->amgInputDataType;
    }


    public function setAmgInputDataType($inputField,$amgInputDataType)
    {
        $this->amgInputDataType[$inputField] = $amgInputDataType;
    }



    public function getFieldDataType($field_name){
        return isset($this->field_datatype[$field_name]);
    }

    public function fieldIsInt($datatype){
        if (stristr($datatype, "int") !== FALSE){
            return true;
        }
        return  false;
    }

    public function fieldIsDecimal($datatype){
        if (stristr($datatype, "decimal") !== FALSE || stristr($datatype, "double") !== FALSE){
            return true;
        }
        return  false;

    }

    protected function fieldIsEnum($datatype){
        if (stristr($datatype, "enum") !== FALSE){
            return true;
        }
        return  false;
    }

    protected function fieldIsDate($datatype){
        if (stristr($datatype, "date") !== FALSE){
            return true;
        }
        return  false;
    }

    protected function getEnumArray($datatype){
        $enum = substr($datatype, 5);
        $enum = substr($enum, 0, (strlen($enum) - 1));
        $enum = str_replace("'", "", $enum);
        $enum = str_replace('"', "", $enum);
        $enum_array = explode(",", $enum);

        return ($enum_array);
    }


    public function fieldInArray($field, $the_array){

        //try to find index for arrays with array[key] = field_name
        $found_index = array_search($field, $the_array);
        if ($found_index !== FALSE){
            return true;
        }

        //for arrays with array[0] = field_name and array[1] = value
        foreach ($the_array as $the_array_values){
            $field_name = $the_array_values[0];
            if ($field_name == $field){
                return true;
            }
        }

        return false;
    }


}