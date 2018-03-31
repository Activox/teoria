<?php
/**
 * Created by PhpStorm.
 * User: paul9
 * Date: 17/12/2017
 * Time: 4:15 PM
 */
Route::getJs(array("scriptReport"), "Defaults", array(), FALSE);
?>
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Reporte de Produccion por Modulo</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    <div class="table-responsive" id="table">

                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->
