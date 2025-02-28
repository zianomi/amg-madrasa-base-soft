<?php
/* @var $tool Tools */
/* @var $tpl Template */

Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");

Tools::getModel("AcademicModel");

$tpl->setCanExport(false);


$qr = new QueryTemplate();
$tpf = new TemplateForm();

$acd = new AcademicModel();
$errors = array();
$sessionArr = array();
$headings = array();
$session = "";

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$duplicate = (isset($_GET['duplicate'])) ? $tool->GetInt($_GET['duplicate']) : '';






$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();





?>

<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("session") ?>
        </label>
        <?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("branch") ?>
        </label>
        <?php echo $tpl->userBranches() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("class") ?>
        </label>
        <?php echo $tpl->getClasses() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("section") ?>
        </label>
        <?php echo $tpl->getSecsions() ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("allow_duplicate") ?>
        </label>
        <select name="duplicate">
            <option value="">
                <?php $tool->trans("not_allowed") ?>
            </option>
            <option value="1" <?php if ($duplicate == 1)
                echo ' selected'; ?>>
                <?php $tool->trans("allowed") ?>
            </option>

        </select>
    </div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3"><label>&nbsp;</label></div>
    <div class="span3"><label>&nbsp;</label></div>
</div>



<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {



    if (empty($session) || empty($branch) || empty($class) || empty($section)) {
        echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
        $tpl->footer();
        exit;
    }






    $paramTeachers['branch'] = $branch;
    $paramTeachers['class'] = $class;








    ?>

    <div class="body">
        <div id="printReady">

            <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel">Modal header</h3>
                </div>
                <div id="ajaxForm"></div>
            </div>


            <div class="row-fluid">
                <div class="span12">
                    <?php echo $tpl->branchBreadCrumbs() ?>
                </div>
            </div>



            <div id="timeTable">&nbsp;</div>


        </div>
    </div>




    <script>

        const url = makeJsLink("ajax", "academic");

        let loadedModal = '';

        function saveData() {
            loaderShow();
            const formData = new FormData();
            const form = $("#formTimetable").serialize();

            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: url,
                data: form,
                success: function (data) {
                    $('#myModal').modal('hide');
                    fetchData()
                    loaderHide()


                },
                error: function () {
                    $('#myModal').modal('hide');
                    loaderHide()
                }
            });

        }

        function fetchData() {

            let paramFetch = 'ajax_request=fetch';
            paramFetch += "&session=<?php echo $session ?>"
            paramFetch += "&branch=<?php echo $branch ?>"
            paramFetch += "&class=<?php echo $class ?>"
            paramFetch += "&section=<?php echo $section ?>"

            loaderShow();
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: url,
                data: paramFetch,
                success: function (data) {
                    loaderHide()
                    $("#timeTable").html(data.data);
                },
                error: function () {
                    loaderHide()
                }
            });
        }

        function fetchModel(attrData) {
            attrData += "&branch=<?php echo $branch ?>";
            attrData += "&class=<?php echo $class ?>";
            attrData += "&session=<?php echo $session ?>";
            attrData += "&section=<?php echo $section ?>";
            loaderShow();
            let paramFetch = 'ajax_request=enter';
            paramFetch += '&params=' + attrData;
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: url,
                data: paramFetch,
                success: function (data) {
                    loaderHide()
                    $('#myModal').modal('show');
                    $("#myModalLabel").text(data.period_name)
                    $("#ajaxForm").html(data.data)

                },
                error: function () {
                    loaderHide()
                }
            });
        }

        function deleteTeacher(attrData) {
            attrData += "&branch=<?php echo $branch ?>";
            attrData += "&class=<?php echo $class ?>";
            attrData += "&session=<?php echo $session ?>";
            attrData += "&section=<?php echo $section ?>";
            loaderShow();
            let paramFetch = 'ajax_request=del_teacher';
            paramFetch += '&params=' + attrData;
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: url,
                data: paramFetch,
                success: function (data) {
                    fetchData()

                },
                error: function () {
                    loaderHide()
                }
            });
        }


        $(document).ready(function () {


            fetchData();


            $(document).on("click", ".enter", function () {
                let attrData = $(this).data("text");
                attrData += "&duplicate=<?php echo $duplicate ?>";
                fetchModel(attrData);
            });

            $(document).on("click", ".del_teacher", function () {
                let attrData = $(this).data("text");
                deleteTeacher(attrData);
            });






            $(document).on("click", "#save_timetable", function (e) {
                e.preventDefault();
                saveData();
            });





        });


    </script>



    <?php
}
$tpl->footer();

