<?php

namespace lib\routes;

/**
 * @author Miguel Peralta
 */
class Post
{
    /**
     * Define all routes to server request, you must specify the request variable 'content' this variable can be (json,html,text : these are return type variables )
     * @var array All routes interfaces
     */
    public static $_rules = array
    (
        //key (path request) => " Module's name '@' Class Name (without 'Controller' or 'Model') '.' Method to execute"
        "doProcess" => "Defaults@Default.doProcess",
        "getCustomer" => "Defaults@Default.getCustomer",
        "getProductStyle" => "Defaults@Default.getProductStyle",
        "reportEarn" => "Defaults@Default.getGananciaByProduct",
        "getStyle" => "Defaults@Default.getStyle",
        "table" => "Defaults@Default.tableProblem",
        "reportProblem" => "Defaults@Default.getReportProblem",
        "reportProduccion" => "Defaults@Default.getReportProduccion"
    );
}
