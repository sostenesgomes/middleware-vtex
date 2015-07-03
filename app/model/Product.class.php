<?php

/**
 * Classe de integração de dados de produtos vtex
 * @author Sóstenes Gomes
 * Class Product
 */

class Product {

    public function __construct() {

        $action = URI::get_segment(2, false);
        $data = count($_POST) ? $_POST : URI::get_params();

        /*verificar se é um json valido e atribuir o json decodificado */
        if (isset($data['json']))
            $data = VtexSoap::JsonToData($data['json']);

        switch($action){
            case 'adicionar':
                $this->insert($data);
                break;

            case 'atualizar':
                $this->update($data);
                break;

            default:
                Handler::error('Falha na integração: Ação não encontrada na integração de ' . json_encode(URI::get_segments()));
                break;
        }
	}

    private function modifier($data) {
        $this->populate($data);
    }

    private function populate($data){
        foreach($data AS $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }

    private function clear(){
        foreach ($this as $key => $value) {
            unset($this->$key);
        }

        return $this;
    }

    private function ProductGet(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->ProductGet(array('idProduct' => $this->Id));

            if (property_exists($result, 'ProductGetResult')){
                return ( property_exists($result->ProductGetResult, 'Id') ? $result->ProductGetResult : NULL);
            }

            return NULL;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }

    }

    private function ProductGetByRefId(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->ProductGetByRefId(array('refId' => $this->RefId));

            if ( is_object($result) and property_exists($result, 'ProductGetByRefIdResult')){
                return ( is_object($result->ProductGetByRefIdResult) and property_exists($result->ProductGetByRefIdResult, 'Id')) ? $result->ProductGetByRefIdResult : NULL;
            }

            return NULL;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }
    }

    private function ProductInsertUpdate(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->ProductInsertUpdate(array('productVO' => $this));

            if ( is_object($result) and property_exists($result, 'ProductInsertUpdateResult')){
                return ( is_object($result->ProductInsertUpdateResult) and property_exists($result->ProductInsertUpdateResult, 'Id')) ? $result->ProductInsertUpdateResult : NULL;
            }

            return NULL;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }

    }

    private function insert($data){

        $this->populate($data);
        $response = $this->ProductInsertUpdate();

        if ($response !== NULL){
            $message = 'Produto inserido com sucesso';
            Handler::success($message, json_encode($response));
        }else{
            $message = 'Falha na integração: Produto não inserido';
            Handler::error($message, json_encode($response));
        }

    }

    private function update($data){

        $this->populate($data);

        $productDTO = $this->ProductGetByRefId();

        if ($productDTO !== NULL){
            $this
                ->clear()
                ->populate($productDTO)
                ->modifier($data);

            $response = $this->ProductInsertUpdate();

            if ($response !== NULL){
                $message = 'Produto atualizado com sucesso';
                Handler::success($message, json_encode($response));
            }else{
                $message = 'Falha na integração: Produto não atualizado';
                Handler::error($message, json_encode($response));
            }

        }else{
            Handler::error($message = "Falha no processo de Integração. Produto não encontrado. Método: " . __METHOD__);
        }

    }

}