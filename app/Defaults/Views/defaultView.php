<?php
Route::getJs(array("scriptSim"), "Defaults", array(), FALSE);
?>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">D Clase Shoes. Teoria de una Orden en Modulos</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <form id="frmSimulacion">
                        <div class="col-md-3">
                            <label>Temporada</label>
                            <select class="form-control select2" style="width: 100%;" name="temporadacmb" id="temporadacmb">
                                <option selected="selected" value="0">Select Option</option>
                                <option value="1">Invierno</option>
                                <option value="2">Primavera</option>
                                <option value="3">Verano</option>
                                <option value="4">Otoño</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Cliente</label>
                            <select class="form-control select2" style="width: 100%;" name="clientecmb" id="clientecmb">
                                <option selected="selected" value="0">Select Option</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Producto</label>
                            <select class="form-control select2" style="width: 100%;" name="productocmb" id="productocmb">
                                <option selected="selected" value="0">Select Option</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Estilo</label>
                            <select class="form-control select2" style="width: 100%;" name="stockcmb" id="stockcmb">
                                <option selected="selected" value="0">Select Option</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Cant. Modulos</label>
                            <input type="number" class="form-control" id="modulotxt" name="modulotxt" value="1" max="9" min="1">
                        </div>
                        <div class="col-md-3">
                            <label>Cant. Ordenes</label>
                            <input type="number" class="form-control" id="ordentxt" name="ordentxt" value="1" max="9" min="1">
                        </div>
                        <div class="col-md-3">
                            <label>Cant. Pares</label>
                            <input type="number" class="form-control" id="parestxt" name="parestxt" value="1" max="10000" min="1">
                        </div>
                    </form>
                    <div class="col-md-3">
                        <br>
                        <button class="btn btn-primary" id="btnEject"><i class="fa fa-gear"></i> Ejecutar</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="images/dclase.png" style="    width: 28% !important;">
                </div>
            </div>
            <hr>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"> <i
                                    class="fa fa-clock-o"></i> Tiempo</a></li>
                    <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"> <i class="fa fa-warning"></i>
                            Problema</a></li>
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-success">
                                    <div class="box-header with-border" style="background-color: #f5f1f1;">
                                        <i class="fa fa-bullhorn"></i>
                                        <h3 class="box-title">Leyenda</h3>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <button class="btn btn-warning"><i class="fa fa-warning"></i> Orden atrasada
                                        </button>
                                        <button class="btn btn-danger"><i class="fa fa-ban"></i> Orden cencelada
                                        </button>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" id="tableDetails">
                                    <div class="callout callout-success text-align-center">
                                        <h3 style="text-align: center;"><i class="fa fa-info"></i> Ningun dato
                                            encontrado</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="container"
                                     style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                                <div class="table-responsive" id="tableProblem">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->
