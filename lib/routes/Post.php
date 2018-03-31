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
        "simulacion" => "Defaults@Default.getSimulacion",
        "table" => "Defaults@Default.tableProblem",
        "reportProblem" => "Defaults@Default.getReportProblem",
        "reportProduccion" => "Defaults@Default.getReportProduccion"
    );
}
