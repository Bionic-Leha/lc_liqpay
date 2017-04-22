<?php
/**
 * Created by PhpStorm.
 * User: Bionic
 * Date: 22.04.2017
 * Time: 9:30
 */

class apitest extends apiBaseClass {

    //https://itstest.ml/wp-content/plugins/wpliqpay/api/?apitest.helloAPI={}
    function helloAPI() {
        $retJSON = $this->createDefaultJson();
        $retJSON->withoutParams = 'It\'s method called without parameters';
        return $retJSON;
    }

    //https://itstest.ml/wp-content/plugins/wpliqpay/api/?apitest.helloAPIWithParams={"TestParamOne":"Text of first parameter"}
    function helloAPIWithParams($apiMethodParams) {
        $retJSON = $this->createDefaultJson();
        if (isset($apiMethodParams->TestParamOne)){
            //Все ок параметры верные, их и вернем
            $retJSON->retParameter=$apiMethodParams->TestParamOne;
        }else{
            $retJSON->errorno=  APIConstants::$ERROR_PARAMS;
        }
        return $retJSON;
    }

    //https://itstest.ml/wp-content/plugins/wpliqpay/api/?apitest.saveData={"data":"Some text"}
    function saveData($apiMethodParams) {
        $retJSON = $this->createDefaultJson();
        $file = '/var/www/liqpay/wp-content/plugins/wpliqpay/purchase_data.txt';
        // Открываем файл для получения существующего содержимого
        $current = file_get_contents($file);
        // Добавляем нового человека в файл
        $current .= $apiMethodParams . "\n";
        // Пишем содержимое обратно в файл
        file_put_contents($file, $current);
        return '1';
    }

    //https://itstest.ml/wp-content/plugins/wpliqpay/api/?apitest.saveDataOld={"data":"Some text"}
    function saveDataOld($apiMethodParams) {
        $retJSON = $this->createDefaultJson();
        if (isset($apiMethodParams->data)){
            //Все ок параметры верные, их и вернем
            $retJSON->retParameter=$apiMethodParams->data;
            $file = '/var/www/liqpay/wp-content/plugins/wpliqpay/purchase_data.txt';
            // Открываем файл для получения существующего содержимого
            $current = file_get_contents($file);
            // Добавляем нового человека в файл
            $current .= $apiMethodParams->data . "\n";
            // Пишем содержимое обратно в файл
            file_put_contents($file, $current);
        }else{
            $retJSON->errorno=  APIConstants::$ERROR_PARAMS;
        }
        return $retJSON;
    }

    //https://itstest.ml/wp-content/plugins/wpliqpay/api/?apitest.helloAPIResponseBinary={"responseBinary":1}
    function helloAPIResponseBinary($apiMethodParams){
        header('Content-type: image/png');
        echo file_get_contents("http://habrahabr.ru/i/error-404-monster.jpg");
    }

}
