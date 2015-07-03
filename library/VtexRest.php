<?php

/**
 * Classe para iteração via Rest com Vtex
 *
 * @author Sóstenes Gomes
 * Class VtexRest
 */

class VtexRest{

    public static function execute($data, $service, $key, $token, $enabledPost=true){

        $resource = curl_init($service);

        $vtex_params_proxy = Config::get('vtex_params_proxy');
        $vtex_params_proxy = (Config::get('development') === true) ? $vtex_params_proxy['development'] : $vtex_params_proxy['production'];

        if($enabledPost === true){
            curl_setopt($resource, CURLOPT_POST, true);
            curl_setopt($resource, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($resource, CURLOPT_HEADER, true);
        curl_setopt($resource, CURLOPT_PROXY, $vtex_params_proxy['proxy_host']);
        curl_setopt($resource, CURLOPT_PROXYPORT , $vtex_params_proxy['proxy_port']);
        curl_setopt($resource, CURLOPT_FAILONERROR, false);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resource, CURLOPT_HTTPHEADER, array(
                "X-VTEX-API-AppKey: " . $key,
                "X-VTEX-API-AppToken: " . $token,
                "Accept: application/json",
                "Content-Type: application/json"
            )
        );

        $response = curl_exec($resource);
        $info     = curl_getinfo($resource);
        $body     = substr($response, -$info['download_content_length']);

        Handler::$responseHeader = substr($response, 0, $info['header_size']);

        curl_close($resource);

        return $body;
    }

    public static function validateJson($string){
        $isJson = Uteis::isJson($string);

        if ($isJson === false)
            Handler::error('Falha na integração: Formato json inválido ' . json_encode(URI::get_segments()), ((is_string($string)) ? $string : '' ) );

        return true;
    }

    public static function status204NoContent(){
        if (strstr(Handler::$responseHeader, 'HTTP/1.1 204 No Content'))
            return true;

        return false;
    }
}