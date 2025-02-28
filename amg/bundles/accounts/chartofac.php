<?php
Tools::getModel("Accounts");
Tools::getLib("Utils");
$ac = new Accounts();
$util = new Utils();
$tpl->renderBeforeContent();
?>



    <div class="body">
        <div class="row-fluid">
            <div class="social-box">
                <div class="header fonts">
                    <h4><?php echo $tpl->getPageTitle() ?></h4>
                </div>
                <div class="body">

                    <div class="row-fluid">
                        <div class="span6">

                            <input type="text" class="form-control input-sm" placeholder="Type to search..." id="searchText" name="searchText"/>

                            <div id="tree-container"></div>

                        </div>
                        <div class="span6">
                            <div id='right-container'></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="<?php echo Tools::getWebUrl() ?>/jstree/dist/themes/default/style.min.css" />
    <script src="<?php echo Tools::getWebUrl() ?>/js/jstree.min.js"></script>
    <script>
        $(function () {
            $('#tree-container').jstree({
                'core' : {
                    'data' : {
                        'url' : makeJsLink("ajax","accounts&operation=get_node"),
                        'data' : function (node) {
                            return { 'id' : node.id };
                        },
                        "dataType" : "json"
                    }
                    ,'check_callback' : true,
                    'themes' : {
                        'responsive' : true,
                        "icons": true,
                        "stripes": false,
                        "ellipsis": true,
                        "dots": true
                    }
                },




                'plugins' : ['contextmenu', 'search', 'unique']
            }).on('create_node.jstree', function (e, data) {

                $.get(makeJsLink("ajax","accounts&operation=create_node"), { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
                    .done(function (d) {
                        data.instance.set_id(data.node, d.id);
                    })
                    .fail(function () {
                        data.instance.refresh();
                    });
            }).on('rename_node.jstree', function (e, data) {
                $.get(makeJsLink("ajax","accounts&operation=rename_node"), { 'id' : data.node.id, 'text' : data.text })
                    .fail(function () {
                        data.instance.refresh();
                    });
            }).on('delete_node.jstree', function (e, data) {
                $.get(makeJsLink("ajax","accounts&operation=delete_node"), { 'id' : data.node.id })
                    .fail(function () {
                        data.instance.refresh();
                    });
            })
                .bind("select_node.jstree", function (e, data) {
                    var href = data.node.a_attr.href;
                    if(href == '#')
                        return '';

                    $('#right-container').html(href);
                })
            ;

            $( "#searchText" ).keyup(function() {
                var text = $(this).val();
                search(text)

            });

            function search(text){
                $('#tree-container').jstree(true).search(text);
            }


        });
    </script>
<?php
$tpl->footer();