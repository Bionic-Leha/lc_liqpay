<?php
/**
 * Created by PhpStorm.
 * User: Bionic
 * Date: 22.04.2017
 * Time: 9:36
 */

header('Content-type: text/html; charset=UTF-8');
if (count($_REQUEST)>0){
    require_once 'apiEngine.php';
    foreach ($_REQUEST as $apiFunctionName => $apiFunctionParams) {
        $APIEngine=new APIEngine($apiFunctionName,$apiFunctionParams);
        echo $APIEngine->callApiFunction();
        break;
    }
}else{
    $jsonError->error='No function called';
    echo json_encode($jsonError);
}