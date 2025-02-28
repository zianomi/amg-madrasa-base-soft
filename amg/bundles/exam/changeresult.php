<?php
$tpl->renderBeforeContent();

?>
<form method="get">
<input type="hidden" name="menu" value="exam">
<input type="hidden" name="page" value="idhistory">

    <div class="social-box">
        <div class="body">

<div class="row-fluid">
        <div class="span3"><label><?php $tool->trans("id")?></label><input type="text" name="student_id" id="student_id" value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id'] ?>"></div>
        <div class="span3"><label>&nbsp;</label>
                <input type="submit" class="btn">
            </div>

            <div class="span3">&nbsp</div>
            <div class="span3">&nbsp</div>
    </div>
</form>
</div>
</div>