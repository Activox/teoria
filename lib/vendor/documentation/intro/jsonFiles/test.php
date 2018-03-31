<?php

$json = array(

    0 => array(
        "id" => "test",
        "docs" => "<h2>test</h2><p>probando contenido HTML, continuando con el documento</p><a href='#'>www.google.com</a> ",
        "position" => "left"
    ),
    1 => array(
        "id" => "lista",
        "docs" => "lista",
        "position" => "left"
    ),
    2 => array(
        "id" => "steps1",
        "docs" => "Opcion de la lista",
        "position" => "left"
    ),
    3 => array(
        "id" => "steps2",
        "docs" => "Opcion de la lista",
        "position" => "left"
    ),
    4 => array(
        "id" => "steps3",
        "docs" => "Opcion de la lista",
        "position" => "left"
    ),
    5 => array(
        "id" => "steps4",
        "docs" => "Opcion de la lista",
        "position" => "left"
    ),
    6 => array(
        "id" => "steps5",
        "docs" => "Opcion de la lista",
        "position" => "left"
    )
);

echo json_encode($json);

?>