<?php

/**
 * Classe para iteração via SOAP com Vtex
 *
 * @author Sóstenes Gomes
 * Class VtexRest
 */


class VtexSoap{

    private static $client;

    static public function createSoapclient(){
        ini_set("soap.wsdl_cache_enabled", "0");

        $vtex_params_soap  = Config::get('vtex_params_soap');
        $vtex_params_proxy = Config::get('vtex_params_proxy');
        $vtex_params_proxy = (Config::get('development') === true) ? $vtex_params_proxy['development'] : $vtex_params_proxy['production'];

        self::$client = new SoapClient($vtex_params_soap['url_wsdl'],
            array(
                'login'      => $vtex_params_soap['login'],
                'password'   => $vtex_params_soap['password'],
                'proxy_host' => $vtex_params_proxy['proxy_host'],
                'proxy_port' => $vtex_params_proxy['proxy_port'],
                'trace'      => 1
            )
        );

        self::$client->__setLocation($vtex_params_soap['url_location']);

        return self::$client;

    }

    public static function jsonToData($object){
        $isJson = Uteis::isJson($object);

        if ($isJson === false)
            Handler::error('Falha na integração: Formato json inválido ' . json_encode(URI::get_segments()));

        return json_decode($object);
    }

    public static function setResponseHeader($resource){

        Handler::$responseHeader = 'Não definido';

        if ($resource instanceof SoapClient){
            Handler::$responseHeader = $resource->__getLastResponseHeaders();
        }

        return true;
    }

}