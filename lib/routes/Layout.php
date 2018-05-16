<?php

namespace lib\routes;

/**
 * @author Miguel Peralta
 */
class Layout {
    
    /**
     * @var array you can change the main layouts for any page, just specify the route or rule
     * in this variable and the layout's name.
     *
     * if you do not specify header or layout, the framework'll select two files per default 'layout/header.php' and 'layout/footer.php'
     */
    public static $_layouts =   array(
        //web route key => "header" => layout header, "footer" => layout footer
        "default" => [ "header" => "", "footer" => "" ],
        "test" => [ "header" => "headerRaw", "footer" => "footerRaw" ]
    );
    
}
