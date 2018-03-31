<?php

//$lang = ( isset( $_SESSION["language"] ) ) ? $_SESSION["language"] : "en";
//
//$options = [
//    "en_0" => "are you search some product? here! you could request any product just follow the steps of our form",
//    "es_0" => "¿buscas algún producto? ¡aquí! Puede solicitar cualquier producto simplemente siga los pasos de nuestro formulario.",
//
//    "en_1" => "are you search some product? here! you could request any product just follow the steps of our form",
//    "es_1" => "¿buscas algún producto? ¡aquí! Puede solicitar cualquier producto simplemente siga los pasos de nuestro formulario.",
//
//    "en_2" => "are you search some product? here! you could request any product just follow the steps of our form",
//    "es_2" => "¿buscas algún producto? ¡aquí! Puede solicitar cualquier producto simplemente siga los pasos de nuestro formulario."
//
//];

$json = array(

    0 => array(
        "id" => "header-top",
        "docs" => "header options",
        "position" => "right"
    )
);

echo json_encode($json);

?>