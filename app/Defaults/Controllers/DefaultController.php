<?php

namespace Defaults\Controllers;

defined('_EXEC_APP') or die('Ups! access not allowed');

use abstracts\Controller;
use Factory;
use stdClass;
use Exception;

class DefaultController extends Controller
{
    /**
     * execute parent contruct..
     */
    public function __construct()
    {
        parent::__construct($this);
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function display($view = '', array $params = array())
    {

        /**
         * set params to view
         */
        Factory::setParametersView($params);

        /**
         * write all content HTML
         */
        $define_view_here = '';

        /**
         * render view
         */
        $render = $view;

        if (empty($define_view_here)) {
            $render .= ".php";
        } else {
            $render = $define_view_here;
        }

        return $render;

    }

    /**
     * get Model object
     * @param string $model 'Module/Reference'
     * @param stdClass $properties object { server : ??, user : ??, pass : ??, db : ??, port : ??}
     * @return object model
     * @throws Exception
     */
    public function getModel($model = '', $properties = null)
    {
        return parent::getModel($model, $properties);
    }

    public function doProcess()
    {
        /* get values */
        $data = \Factory::getInput('data');
        $params = new \stdClass();
        $params->temporada = $data['temporadacmb'];
        $params->modulo = $data['modulotxt'];
        $params->order = $data['ordentxt'];
        $params->cliente = $data['clientecmb'];
        /* get the current execution value */
        $result = $this->getModel()->getEjecucion();
        $params->max_ejecion = $result[0]->max_ejecion + 1;
        /* get the operations */
        $operations = $this->getModel()->getOperations();
        $html = new stdClass();
        $table = '';
        /* do the magic */
        for ($m = 0; $m < $params->order; $m++) {
            for ($p = 1; $p <= $params->modulo; $p++) {
                /* insertar la orden */
                $orden = new \stdClass();
                $orden->name = "" . $params->cliente == 1 ? "SPR" . $this->randomKey(7) : ($params->cliente == 2 ? "SEB" . $this->randomKey(7) : ($params->cliente == 3 ? "TMB" . $this->randomKey(7) : "QV" . $this->randomKey(7)));
                $orden->pares = $data['parestxt'];
                $orden->stock_id = $data['stockcmb'];
                $id_orden = $this->getModel()->setOrden($orden);
                $params->cancel = 0;
                foreach ($operations as $key => $value) {
                    /* ver si la orden no se cancelo en la operacion pasada */
                    if ($params->cancel == 0) {
                        $operation_act = new \stdClass();
                        $operation_act->id_orden = $id_orden[0]->max_id;
                        $operation_act->id_modulo = $p;
                        $operation_act->ejecucion = $params->max_ejecion;
                        $operation_act->id_operation = $value->id_record;
                        $operation_act->id_operation = $value->id_record;
                        $operation_act->tiempo = (rand(5, 35));
                        /* obtener todos los problemas que podria tener esa operacion */
                        $problems = $this->getModel()->getOperationsProblems($value->id_record);
                        $rand_problem = rand(0, count($problems));
                        /* verificar si la orden se cancela */
                        $rand = rand(1, 100);
                        if ($rand > 90) {
                            $operation_act->id_problem = 24;
                            $params->cancel = 1;
                        } else {
                            /*buscar si en esta operacion hay un problema.*/
                            $count_problems = 1;
                            foreach ($problems as $key) {
                                if ($rand_problem == $count_problems) {
                                    $operation_act->id_problem = $key->id_record;
                                    break;
                                } else {
                                    $operation_act->id_problem = 0;
                                }
                                $count_problems++;
                            }
                            /* si la temporada es invierno o verano aumentar el tiempo por la alta demanda. */
                            if (($params->temporada == 1 || $params->temporada == 3) || $operation_act->id_problem != 0) {
                                if (rand(1, 100) > 85) {
                                    $poisson = round(abs(1 / (rand(1, 10000) / 10000) * log((rand(1, 10000) / 10000))), 2);
                                    $operation_act->tiempo = $operation_act->tiempo + ($poisson * 10);
                                }
                            }
                        }
                        $this->getModel()->setActividad($operation_act);
                    }
                }
            }
        }

        /* construir las tablas para cada modulo */
        for ($p = 1; $p <= $params->modulo; $p++) {
            $params->id_modulo = $p;
            $result = $this->getModel()->getActividad($params);
            $table .= "  <h4>Modulo $p</h4>
                    <table class=\"table table-bordered table-striped table-condensed\" >
                        <thead class=\"bg-green-active\">
                            <tr>
                                <th>#</th>
                                <th>Numero de Orden</th>
                                <th>Pares</th>
                                <th>Planificacion</th>
                                <th>Cutting</th>                                
                                <th>Hand Sewing</th>
                                <th>Horno</th>
                                <th>Bottoming</th>
                                <th>Packing</th>
                                <th>Tiempo total</th>
                                <th>Costo de Produccion</th>
                                <th>Costo de Venta</th>
                                <th>Ganancia Neta</th>
                                <th>%</th>
                            </tr>
                            </thead><tbody>";
            $tmp = 0;
            $count_row = 1;
            $number_rounds = 6;
            $total_time = 0;
            $total_pares = 0;
            $total_production_cost = 0;
            $total_production_sell = 0;
            foreach ($result as $key => $value) {

                /* como hay multiples filas setiamos primeros los valores que van una sola vez */
                if ($tmp != $value->id_record) {
                    $count_ronds = 0;
                    $table .= " <tr> ";
                    $count_order_number = 0;
                    $table .= "<td>" . $count_row++ . "</td>";
                    $table .= "<td>" . $value->orden . "</td>";
                    $table .= "<td>" . $value->pares . "</td>";
                    $tmp = $value->id_record;
                    $total_pares += $value->pares;
                    /* buscamos cuantas operaciones tiene esa orden */
                    foreach ($result as $key2 => $value2) {
                        if ($value->id_record == $value2->id_record) {
                            $count_order_number++;
                        }
                    }
                }
                $style_rows = '';
                if ($value->tiempo > 25) {
                    $style_rows = 'style="background-color:#f6e36b;font-weight: bold;"';
                }
                $table .= "<td $style_rows >" . $value->tiempo . " Min</td>";
                $count_ronds++;
//                echo " counter_row:" . $count_ronds . " counter oder: " . $count_order_number . " missing rows:" . ($number_rounds - $count_order_number) . " \n\n";
                /* revisamos si termino con esa orden*/
                if ($count_ronds == $count_order_number) {
                    $missing_rows = $number_rounds - $count_order_number;
                    if ($missing_rows > 0) {
                        for ($i = 0; $i < $missing_rows; $i++) {
                            $table .= "<td style=\"background-color:#F25E5E;font-weight: bold;\" >0</td>";
                        }
                    }
                    $table .= "<td>" . round(($value->total_time > 60 ? ($value->total_time / 60) : $value->total_time), 2) . "" . ($value->total_time > 60 ? " Horas" : " Min") . "</td>";
                    $table .= "<td>US$ " . number_format(($value->production_cost + (5 * ($value->count_time_extra > 0 ? $value->count_time_extra : 0)))) . "</td>";
                    $table .= "<td>US$ " . number_format($value->production_sell) . "</td>";
                    $table .= "<td>US$ " . number_format(($value->production_sell - ($value->production_cost + (5 * ($value->count_time_extra > 0 ? $value->count_time_extra : 0))))) . "</td>";
                    $table .= "<td> " . round((($value->production_sell - ($value->production_cost + (5 * ($value->count_time_extra > 0 ? $value->count_time_extra : 0)))) / ($value->production_cost + (5 * ($value->count_time_extra > 0 ? $value->count_time_extra : 0)))) * 100,2) . "%</td>";
                    $total_time += $value->total_time;
                    $total_production_cost += $value->production_cost - (5 * ($value->count_time_extra > 0 ? $value->count_time_extra : 0));
                    $total_production_sell += $value->production_sell;
                    $table .= " </tr> ";
                }
            }
            /* buscar los totales del modulo */
            $footer = $this->getModel()->getTotalByOperation($params);
            $table .= '</tbody>
                    <tfoot>';
            foreach ($footer as $key3 => $value3) {
                $table .= "<tr style='background-color: #dadada; font-weight: bold;' >
                        <td colspan='2' ></td>
                        <td>" . round($total_pares, 2) . "</td>
                        <td>" . round($value3->planificacion, 2) . " Min</td>
                        <td>" . round($value3->cutting, 2) . " Min</td>
                        <td>" . round($value3->handsewing, 2) . " Min</td>
                        <td>" . round($value3->horno, 2) . " Min</td>
                        <td>" . round($value3->bottoming, 2) . " Min</td>
                        <td>" . round($value3->packing, 2) . " Min</td>
                        <td>" . round(($total_time / 60), 2) . " Horas</td>
                        <td >US$ " . number_format($total_production_cost) . "</td>
                        <td style='color:" . ($total_production_cost < $total_production_sell ? '#008d4c' : '#F25E5E') . "' >US$ " . number_format($total_production_sell) . "</td>
                        <td >US$ " . number_format(($total_production_sell - $total_production_cost)) . "</td>
                        <td >" . round((($total_production_sell - $total_production_cost) / $total_production_cost) * 100,2) . "%</td>
                        </tr>
                    </tfoot>
                </table>";
            }
            $table .= "<hr>";
        }
        $html->table = $table;
        $html->eject = $params->max_ejecion;
        return $html;

    }

    private function randomKey($length)
    {
        $pool = array_merge(range(0, 9));
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $key;
    }

    public function tableProblem()
    {
        $html = new stdClass();
        $data = \Factory::getInput('data');
        $params = $data;
        /* hacer tablas y estadistica de los problemas. */
        $result2 = $this->getModel()->getTotalProblemsByEjection($params);
//        echo "{'data':" . json_encode($result2) . "}";
        if (!empty($result2)) {
            $tableProblem = '';
            $count_row = 1;
            $tableProblem .= '<div class="row"> <div class="col-md-4"> <label> Problemas </label> <table class="table table-bordered table-striped table-condensed" >
                        <thead class="bg-green-active">
                            <tr>
                                <th>#</th>
                                <th>Problema</th>
                                <th>Qty</th> </tr></thead> <tbody>';
            foreach ($result2 as $key) {
                /* llenar el body de la tabla */
                $tableProblem .= '<tr>
                <td>' . $count_row++ . '</td>
                <td>' . $key->problema . '</td>
                <td>' . $key->cant_problema . '</td>
                </tr>';
            }
            $tableProblem .= '</tbody><tfoot><tr style="background-color: #dadada">
            <th colspan="2" ></th>
            <th>' . $result2[0]->sum_problem . '</th>
            </tr></tfoot></table></div>';
            /* crear tabla los problemas de cada modulo */
            $modulo = $this->getModel()->getProblemsModule($params);
            $tmp = 0;
            $row = 1;
            foreach ($modulo as $key) {
                if ($tmp != $key->id_modulo) {
                    $total_problem = 0;
                    if ($row == 4) {
                        $row = 1;
                        $tableProblem .= "</div><div class='row'>";
                    }
                    $tableProblem .= "
                        <div class=\"col-md-4\"><label> Modulo $key->id_modulo </label>
                        <table class=\"table table-bordered table-striped table-condensed\" >
                        <thead class=\"bg-green-active\">
                            <tr>
                                <th>#</th>
                                <th>Problema</th>
                                <th>Qty</th> </tr></thead> <tbody>";
                    foreach ($modulo as $key2) {
                        if ($key2->id_modulo == $key->id_modulo) {
                            /* llenar el body de la tabla */
                            $tableProblem .= '<tr>
                                <td>' . $count_row++ . '</td>
                                <td>' . $key2->problema . '</td>
                                <td>' . $key2->cant_problema . '</td>
                                </tr>';
                            $total_problem += $key2->cant_problema;
                        }
                    }
                    $tmp = $key->id_modulo;
                    $tableProblem .= '
                            </tbody><tfoot><tr style="background-color: #dadada">
                                <th colspan="2" ></th>
                                <th>' . $total_problem . '</th>
                            </tr></tfoot></table></div>';

                    $row++;
                }
            }
            $tableProblem .= "</div>";
            $html->tableProblem = $tableProblem;
            $html->code = 400;
            $html->chart = json_encode($result2);
            $html->result = $result2;
        } else {
            $html->code = 200;
        }

        return json_encode($html);
    }

    public function getReportProblem()
    {

        $html = '';
        $result = $this->getModel()->getTotalProblems();
        $html .= '  <table class="table table-bordered table-striped table-condensed" >
                        <thead class="bg-light-blue-active">
                        <tr>
                                <th>#</th>
                                <th>Problema</th>
                                <th>Qty</th>
                                <th>Solucion</th>
                            </tr>
                            </thead><tbody> ';
        $count_rows = 1;
        $total_problems = 0;
        foreach ($result as $key) {
            $html .= "<tr>
            <td>" . $count_rows++ . "</td>
            <td>" . $key->problema . "</td>
            <td>" . number_format($key->cant_problema) . "</td>
            <td>" . $key->solucion . "</td>
            </tr>";
            $total_problems += $key->cant_problema;
        }
        $html .= "<tbody>
                <tfoot>
                    <tr style='background-color: #dadada;' >
                        <th colspan='2' ></th>
                        <th > " . number_format($total_problems) . " </th>
                        <th ></th>
                    </tr>
                </tfoot>
                </table>";

        $html .= '  <table class="table table-bordered table-striped table-condensed" id="datatable" style="display: none" >
                        <thead class="bg-light-blue-active">
                            <tr><th></th>';
        foreach ($result as $key) {
            $html .= '<th>' . $key->problema . '</th>';
        }
        $html .= '</tr></thead><tbody><tr><td>QTY</td> ';
        foreach ($result as $key) {
            $html .= " <td>" . $key->cant_problema . "</td> ";
        }
        $html .= "</tr><tbody></table>";
        echo $html;
    }

    public function getReportProduccion()
    {

        $html = '';
        $result = $this->getModel()->getTotalProduction();
        $html .= '  <table class="table table-bordered table-striped table-condensed" >
                        <thead class="bg-light-blue-active">
                        <tr>
                                <th>#</th>
                                <th>Modulo</th>
                                <th>Qty Pares</th>
                            </tr>
                            </thead><tbody> ';
        $count_rows = 1;
        $total_problems = 0;
        foreach ($result as $key) {
            $html .= "<tr>
            <td>" . $count_rows++ . "</td>
            <td>" . $key->modulo . "</td>
            <td>" . number_format($key->pares) . "</td>
            </tr>";
            $total_problems += $key->pares;
        }
        $html .= "<tbody>
                <tfoot>
                    <tr style='background-color: #dadada;' >
                        <th colspan='2' ></th>
                        <th > " . number_format($total_problems) . " </th>
                    </tr>
                </tfoot>
                </table>";

        $html .= '  <table class="table table-bordered table-striped table-condensed" id="datatable" style="display: none" >
                        <thead class="bg-light-blue-active">
                            <tr><th></th>';
        foreach ($result as $key) {
            $html .= '<th>' . $key->modulo . '</th>';
        }
        $html .= '</tr></thead><tbody><tr><td>QTY Pares</td> ';
        foreach ($result as $key) {
            $html .= " <td>" . $key->pares . "</td> ";
        }
        $html .= "</tr><tbody></table>";
        echo $html;
    }

    public function getCustomer()
    {
        return $this->generateCombo($this->getModel()->getCustomer());
    }

    public function getProductStyle()
    {
        $data = \Factory::getInput('data');
        $params = new \stdClass();
        $params->product = $this->generateCombo($this->getModel()->getProductByCustomer($data));
        $params->stock = $this->generateCombo($this->getModel()->getStock($data, 0));
        return $params;
    }

    public function getStyle()
    {
        $data = \Factory::getInput('data');
        return $this->generateCombo($this->getModel()->getStock($data['clientecmb'], $data['productocmb']));
    }

    public function generateCombo($data)
    {
        $html = " <option selected=\"selected\" value=\"0\">Select Option</option> ";
        foreach ($data as $key) {
            $html .= " <option value='$key->id_record'>$key->description</option> ";
        }
        return $html;
    }

    public function getGananciaByProduct(){

        $result = $this->getModel()->getGananciaByProduct();



    }

}
