<?php
/**
 * Created by PhpStorm.
 * User: paul9
 * Date: 17/12/2017
 * Time: 4:15 PM
 */
Route::getJs(array("problemScript"), "Defaults", array(), FALSE);
?>
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Reporte Problemas</h3>
        </div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"> <i class="fa fa-area-chart"></i> QTY</a></li>
                    <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"> <i class="fa fa-bar-chart"></i> PRODUCTO</a></li>
                    <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false"> <i class="fa fa-line-chart"></i> ESTILO</a></li>
                    <li class=""><a href="#tab_4" data-toggle="tab" aria-expanded="false"> <i class="fa fa-pie-chart"></i> MODULO</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="containerQty" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                                <div class="table-responsive" id="tableQty">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="tab_2">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="containerProduct" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                                <div class="table-responsive" id="tableProduct">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="tab_3">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="containerStyle" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                                <div class="table-responsive" id="tableStyle">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="tab_4">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="containerModule" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                                <div class="table-responsive" id="tableModule">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->
