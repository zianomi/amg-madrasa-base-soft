function fnExcelReport()
{
    var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++)
    {
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text=tab_text+"</table>";
    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        expIframe.document.open("txt/html","replace");
        expIframe.document.write(tab_text);
        expIframe.document.close();
        expIframe.focus();
        sa=expIframe.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
    }
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
}
function printSpecial() {
    window.print();
}

var gAutoPrint = true;

/*<![CDATA[*/
$(document).ready(function () {

    App.init();

    SideBar.init({
      shortenOnClickOutside: false
    });

    FormElements.init();

    jQueryUI.init();
    $(".amg_crud_required").prop('required',true);

    $('.datepicker').datepicker({
      format: "dd-mm-yyyy",
      viewformat: "dd-mm-yyyy",
      todayBtn: "linked",
      forceParse: false,

      autoclose: true,
      todayHighlight: true
    });

    $('#datepicker').datepicker({
     format: "dd-mm-yyyy",
     viewformat: "dd-mm-yyyy",
     todayBtn: "linked",
     forceParse: false,
     autoclose: true,
     todayHighlight: true
   });


    $('#date').datepicker({
        format: "dd-mm-yyyy",
        viewformat: "dd-mm-yyyy",
        todayBtn: "linked",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
      });

    $('.date').datepicker({
        format: "dd-mm-yyyy",
        viewformat: "dd-mm-yyyy",
        todayBtn: "linked",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
      });


    $('.start_date').datepicker({
        format: "dd-mm-yyyy",
        viewformat: "dd-mm-yyyy",
        todayBtn: "linked",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
      });

    $('.end_date').datepicker({
        format: "dd-mm-yyyy",
        viewformat: "dd-mm-yyyy",
        todayBtn: "linked",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
      });


    $('#start_date').datepicker({
        format: "dd-mm-yyyy",
        viewformat: "dd-mm-yyyy",
        todayBtn: "linked",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
      });

    $('#end_date').datepicker({
        format: "dd-mm-yyyy",
        viewformat: "dd-mm-yyyy",
        todayBtn: "linked",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
      });


    $('#ajaxcruddatepicker').datepicker({
         format: "yyyy-mm-dd",
         viewformat: "yyyy-mm-dd",
         todayBtn: "linked",
         forceParse: false,
         autoclose: true,
         todayHighlight: true
       });


    $('.ajaxcruddatepicker').datepicker({
     format: "yyyy-mm-dd",
     viewformat: "yyyy-mm-dd",
     todayBtn: "linked",
     forceParse: false,
     autoclose: true,
     todayHighlight: true
   });
     //$('.footable').footable();
        //$(".chosen-select").chosen();

    $(".chosen-select").chosen();

    $branchDropDown = $('#branch');
    $classDropDown = $('#class');
    $studentId = $('#student_id');


    if ($('#student_id').size) {
          var getStudent = makeJsLink("ajax","settings");
        $('#student_id').change(function () {
              var data = 'ajax_request=student_id&student_id=' + $("#student_id").val();
              $.ajax({
                  type: "POST",
                  url: getStudent,
                  data: data,
                  success: function (data) {
                      $("#student_res").html(data);
                  }
              })
          });

      }


    if ($('#gr').size) {
              var GetStudentByGr = makeJsLink("ajax","settings");
        $('#gr').change(function () {
                  var data = 'ajax_request=student_by_gr&gr=' + $(this).val();
                  $.ajax({
                      type: "POST",
                      url: GetStudentByGr,
                      data: data,
                      success: function (data) {
                          $("#gr_res").html(data);
                      }
                  })
              });

          }




    if ($classDropDown.length) {
       var getSectionUrl = makeJsLink("ajax","settings");
        $classDropDown.change(function () {
           $('body').css('opacity', '0.5');
          $('#amgloader').show();
           var sessionString = "";
          if($('#session').length){
              sessionString = "&session="+$("#session").val();
          }
          else{
              sessionString = "";
          }


           var branchString = "";
            if($branchDropDown.length){
                branchString = "&branch="+$branchDropDown.val();
            }
            else{
                branchString = "";
            }

           var data = 'ajax_request=get_section&class=' + $classDropDown.val() + sessionString + branchString;
           $.ajax({
               type: "POST",
               url: getSectionUrl,
               data: data,
               success: function (data) {
                   $("#section").html(data);
                   $("#section").trigger("chosen:updated");
                   $('body').css('opacity', '1');
                  $('#amgloader').hide();
               }
           })
       });
    }


    $('#show_session_orign').click(function(){
        $("#reportsession").show();
        $("#reportdates").hide();
    });

    if ($('#session').length) {
        $("#session").change(function () {



            if($('#reportdates').length){
                var selVal = $("#session").chosen().val();
                if(selVal == "999999999999999-varss"){
                    $("#reportsession").hide();
                    $("#reportdates").show();
                }
            }

            //$("select#branch").attr('selectedIndex', 0);
            $('#branch').val('').trigger('chosen:updated');
            $('#class').val('').trigger('chosen:updated');
            $('#section').val('').trigger('chosen:updated');
            //$("#branch").html("<option value=''>Please Select</option>");
        });


    }



    if ($branchDropDown.length) {
       var getClassUrl = makeJsLink("ajax","settings");
        $branchDropDown.change(function () {
            $('body').css('opacity', '0.5');
            $('#amgloader').show();

               var sessionString = "";
               if($('#session').length){
                   sessionString += "&session="+$("#session").val();
                   if($('#session').val() == "999999999999999-varss"){
                      sessionString += "&start_date=" + $('.start_date').val() + "&end_date=" + $('.end_date').val();
                  }
               }

               var data = 'ajax_request=get_class&branch=' + $branchDropDown.val() + sessionString;
               $.ajax({
                   type: "POST",
                   url: getClassUrl,
                   data: data,
                   success: function (data) {
                       $("#class").html(data).trigger("chosen:updated");
                       $('body').css('opacity', '1');
                       $('#amgloader').hide();
                   }
               })
           });
       }


    if ($('#note_cat').length) {
           var getNoteSubCat = makeJsLink("ajax","settings");
           $("#note_cat").change(function () {

               $("#note_sub_cat").html(optionNulVal());
               var data = 'ajax_request=get_notesubcat&notecat=' + $("#note_cat").val();
               $.ajax({
                   type: "POST",
                   url: getNoteSubCat,
                   data: data,
                   success: function (data) {
                       $("#note_sub_cat").html(data);
                       $("#note_sub_cat").trigger("chosen:updated");
                   }
               })
           });
       }


if ($('.delete_exam_date_log').length) {
    $('.delete_exam_date_log').click(function(){
           var getClassUrl = makeJsLink("ajax","exam");
           var data = 'ajax_request=delete_exam_date_log&record_to_delete=' + $(this).data("id");
           var row = $(this).parent().parent();
          $.ajax({
              type: "POST",
              url: getClassUrl,
              data: data,
              success: function (data) {
                  if(data == "OK"){
                      row.remove();
                  }
                  else{
                      alert(data);
                  }

              }
          });

       });
}





    });








/*]]>*/


function loaderShow(){
    $('body').css('opacity', '0.5');
    $('#amgloader').show();
}
function loaderHide(){
    $('body').css('opacity', '1');
    $('#amgloader').hide();
}
function optionNulVal(){
    return '<option value="">Please Select</option>';
}

function checkAll(bx) {
       var cbs = document.getElementsByTagName('input');
       for (var i = 0; i < cbs.length; i++) {
           if (cbs[i].type == 'checkbox') {
               cbs[i].checked = bx.checked;
           }
       }
}

function makeJsLink(bundle,file){
    return siteUrl + "/?menu=" + bundle + "&page=" + file;
}

function CheckValue(input, max) {
    var value = Number(input.value);
    if (NaN == value || input.value>max) { //it is not a number or is too big - revert
        input.value = LastGood;
    } else { //it is ok, save it as the last good value
        LastGood = value;
    }
}


function getAmgCurrentPage() {
    var e = this_page, t = "?";
    return -1 != e.indexOf("?") && (t = "&"), e += t
}


function ajaxcrudLoadData(pageUrl,passedData){
    var responsData = "";
    $.ajax({
        type: "get",
        url: pageUrl,
        data: passedData,
        async: false,
        success: function (htm) {
            responsData = htm;
        }
    });

    return responsData;
}


