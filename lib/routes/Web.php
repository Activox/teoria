<?php

namespace lib\routes;

/**
 * @author Miguel Peralta
 */
class Web 
{    
    /**
     * if you want show any view, you must call 'display' method and send one parameter with the name view (Verify the key sensitive)
     * 
     * @var array All routes interfaces
     */
    public static $_rules    =   array 
    (
        //key (path request) => " Module's name '@' Class Name (without 'Controller' or 'Model') '.' Method to execute * View (the last parameter after '*' is optional, the '*' too)"
        "default" => "Defaults@Default.display*default",
        "reportProblem" => "Defaults@Default.display*reportProblem",
        "reportProduccion" => "Defaults@Default.display*reportProduccion",
        "reportEarning" => "Defaults@Default.display*reportEarn"
    );
    
}
