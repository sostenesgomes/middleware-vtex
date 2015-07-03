<?php

/**
 * Classe para integração de preço de sku vtex
 * @author Sóstenes Gomes
 * Class SkuPrice
 */
class SkuPrice {

    public function __construct(){
        $action = URI::get_segment(2, false);
        $data = count($_POST) ? $_POST : URI::get_params();

        if (!isset($data['json']))
            Handler::error('Falha no processo de Integração: É preciso enviar o json : ' . json_encode(URI::get_segments()));

        $json = $data['json'];

        switch($action){
            case 'atualizar':
                $this->update($json);
                break;

            default:
                Handler::error('Falha na integração: Ação não encontrada na integração de ' . json_encode(URI::get_segments()));
                break;
        }

    }

    private function get($data){

    }

    private function update($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $service = $restParams['url_price_update'];
        $key     = $restParams['app_key'];
        $token   = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_array($jsonDecode) and isset($jsonDecode[0]) and property_exists($jsonDecode[0], 'itemId')){
                Handler::success('Preço de sku atualizado com sucesso', $response);

            }elseif ( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Preço de sku não atualizado. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Preço de sku não atualizado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Preço não atualizado :: Retorno fora do padrão: ' . __METHOD__, ((is_string($response)) ? $response : '' ));
        }
    }

}