<?php

/**
 * Classe para integração de dados de clientes com Vtex
 *
 * @author Sóstenes Gomes
 * Class Client
 */
class Client {

    public function __construct(){

        $action = URI::get_segment(2, false);
        $data   = count($_POST) ? $_POST : URI::get_params();

        if (!isset($data['json']))
            Handler::error('Falha no processo de Integração: É preciso enviar o json no formato válido : ' . json_encode(URI::get_segments()));

        $json = $data['json'];

        switch($action){
            case 'adicionar':
                $this->add($json);
                break;

            case 'adicionar-endereco':
                $this->addAddress($json, $data["profileId"]);
                break;

            default:
                Handler::error('Falha na integração: Ação não encontrada na integração de ' . json_encode(URI::get_segments()));
                break;
        }

    }

    private function add($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $service = $restParams['url_client_add'];

        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'profileId')){
                Handler::success('Perfil criado com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Perfil de cliente não criado. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Perfil de cliente não criado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Perfil de cliente não criado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    private function addAddress($data, $profileId){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $service = $service = preg_replace('/@PROFILEID@/', $profileId, $restParams['url_client_add_address']);

        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'profileId')){
                Handler::success('Endereço criado criado com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Endereço não criado. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Endereço não criado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Endereço não criado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

}