<?php
$tpl->renderBeforeContent();
//Tools::getModel("SettingModel");
$set = new SettingModel();


?>
<div class="social-box">
  <div class="container-fluid body">
      <div class="row-fluid">
                  <div class="social-box">
                    <div class="header">
                      <h4>FAQ with tabs</h4>
                    </div>
                    <div class="body">
                      <div class="row-fluid">

                        <!-- BEGIN RIGHT PANEL -->
                        <div class="span12">
                          <div class="tab-content">
                            <div class="tab-pane active" id="fa1">
                              <div class="accordion" id="accordion-fa1">
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-fa1" href="#collapseOne-fa1">
                                      Question 1: Collapsible Group Item #1?
                                    </a>
                                  </div>
                                  <div id="collapseOne-fa1" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-fa1" href="#collapseTwo-fa1">
                                      Question 2: Collapsible Group Item #2?
                                    </a>
                                  </div>
                                  <div id="collapseTwo-fa1" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-fa1" href="#collapseThree-fa1">
                                      Question 3: Collapsible Group Item #3?
                                    </a>
                                  </div>
                                  <div id="collapseThree-fa1" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="tab-pane" id="fa2">
                              <div class="accordion" id="accordion-fa2">
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-fa2" href="#collapseOne-fa2">
                                      Question 1: Collapsible Group Item #1?
                                    </a>
                                  </div>

                                  <div id="collapseOne-fa2" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-fa2" href="#collapseTwo-fa2">
                                      Question 2: Collapsible Group Item #2?
                                    </a>
                                  </div>
                                  <div id="collapseTwo-fa2" class="accordion-body collapse in">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane" id="fa3">
                              <div class="accordion" id="accordion-fa3">
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-fa3" href="#collapseOne-fa3">
                                      Question 1: Collapsible Group Item #1?
                                    </a>
                                  </div>
                                  <div id="collapseOne-fa3" class="accordion-body collapse" style="height: 0px;">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-fa3" href="#collapseTwo-fa3">
                                      Question 2: Collapsible Group Item #2?
                                    </a>
                                  </div>

                                  <div id="collapseTwo-fa3" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-fa3" href="#collapseThree-fa3">
                                      Question 3: Collapsible Group Item #3?
                                    </a>
                                  </div>
                                  <div id="collapseThree-fa3" class="accordion-body collapse in">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-fa3" href="#collapseFour-fa3">
                                      Question 4: Collapsible Group Item #4?
                                    </a>
                                  </div>
                                  <div id="collapseFour-fa3" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane" id="fa4">
                              <div class="accordion" id="accordion-fa4">
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-fa4" href="#collapseOne-fa4">
                                      Question 1: Collapsible Group Item #1?
                                    </a>
                                  </div>
                                  <div id="collapseOne-fa4" class="accordion-body collapse in" style="height: 0px;">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                                <div class="accordion-group">
                                  <div class="accordion-heading">
                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-fa4" href="#collapseTwo-fa4">
                                      Question 2: Collapsible Group Item #2?
                                    </a>
                                  </div>
                                  <div id="collapseTwo-fa4" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put.
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- END RIGHT PANEL -->
                      </div>
                    </div>
                  </div>
                </div>
</div><!-- container-fluid FOOTER-->
</div>

<?php
$tpl->footer();