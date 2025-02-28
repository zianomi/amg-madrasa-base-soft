<?php
$tpl->renderBeforeContent();




$dataDump = array("{trans}dashboard{/trans}" => "ڈیش پورڈ","{trans}error_500{/trans}" => "Error 500");


Tools::setTransData(array());
$tool->trans("dashboard");
$tool->trans('test2');
$tool->trans('4534534534');
$tool->trans('underscore_asdasd');
$tool->trans('hyphen-sdasdasd');
$tool->trans('alpha_num-adasdas3423432');
?>
<div class="social-box">
     <div class="container-fluid body">
         <div class="row-fluid">
                 <div class="error-500">
                   <i class="icon-remove-sign icon-4x error-icon"></i>
                   <h1>Dashboard</h1>
                   <span class="text-error"><small><strong>Error 500</strong></small></span>
                   <p>Oops! An error has occured, sorry.</p>
                 </div>

             </div>
   </div>
   </div>

<?php
$tpl->footer();