<?php

namespace interfaces;

defined('_EXEC_APP') or die('Ups! access not allowed');

interface Moduling
{
    //Idatabase $clave
    public function __construct(Idatabase $idatabase = NULL );
    public function getLink();
}

?>

