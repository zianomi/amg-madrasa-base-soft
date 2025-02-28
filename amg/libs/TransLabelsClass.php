<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 1/31/2017
 * Time: 7:14 PM
 */
class TransLabelsClass
{
    private $lang = "en";

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }


    public function transArray(){
        $transArr = array(
            "ar" => array(
                    "total_records" => "السجلات الكلية"
                   ,"search" => "بحث"
                   ,"delete" => "حذف"
                   ,"cancel" => "إلغاء"
                   ,"action" => "عمل"
                   ,"edit" => "تصحيح"
                   ,"del" => "حذف"
                   ,"no_data_in_this_table_click_add_button_below" => "لا توجد بيانات في هذا الجدول. انقر إضافة الزر أدناه."
                   ,"no_fields_in_this_table" => "لا حقول في هذا الجدول!"
                  ,"print" => "طباعة"
                  ,"export" => "تصدير"
                  ,"enable" => "تمكين"
                  ,"disable" => "تعطيل"
                  ,"no_record_found" => "لاتوجدالنتايج•"
                  ,"gender_one" => "S/O"
                  ,"gender_two" => "D/O"
            )
           ,"ur" => array(
                   "total_records" => "کل تعداد"
                   ,"search" => "تلاش"
                   ,"delete" => "حذف"
                   ,"del" => "حذف"
                   ,"action" => "عمل"
                   ,"edit" => "تبدیل"
                   ,"no_data_in_this_table_click_add_button_below" => "اس کے ٹیبل میں کوئی ڈیٹا نہیں. نیچے دیے گئے بٹن پر کلک کریں"
                   ,"no_fields_in_this_table" => "اس میں کوئی کالم نہیں ہے۔"
                   ,"print" => "پرنٹ"
                   ,"cancel" => "منسوخ"
                   ,"export" => "برآمد"
                   ,"enable" => "فعال"
                   ,"disable" => "غیر فعال"
                   ,"no_record_found" => "کوئی نتیجہ نہیں ملا۔"
                  ,"gender_one" => "بن"
                  ,"gender_two" => "بنت"
                )
           ,"en" => array(
                   "total_records" => "Total Records"
                  ,"search" => "Search"
                  ,"delete" => "Delete"
                  ,"del" => "Delete"
                  ,"action" => "Action"
                  ,"edit" => "Edit"
                  ,"no_data_in_this_table_click_add_button_below" => "No data in this table. Click add button below."
                  ,"no_fields_in_this_table" => "No fields in this table!"
                  ,"print" => "Print"
                  ,"cancel" => "Cancel"
                  ,"export" => "Export"
                  ,"enable" => "Enable"
                  ,"disable" => "Disable"
                  ,"no_record_found" => "No record found."
                  ,"gender_one" => "S/O"
                  ,"gender_two" => "D/O"
                )
        );

        return $transArr[$this->lang];
    }


}