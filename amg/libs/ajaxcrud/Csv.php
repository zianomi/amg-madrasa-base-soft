<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/12/18
 * Time: 5:06 PM
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . "FieldsType.php";
class Csv extends FieldsType{

    protected $showCSVExport = false;	// indicates whether to show the "Export Table to CSV" button
    protected $amgCsv = false;

    public function showCSVExportOption() {
        $this->showCSVExport = true;
    }

    /**
     * @return boolean
     */
    public function isAmgCsv()
    {
        return $this->amgCsv;
    }

    /**
     * @param boolean $amgCsv
     */
    public function setAmgCsv($amgCsv)
    {
        $this->amgCsv = $amgCsv;
    }


}