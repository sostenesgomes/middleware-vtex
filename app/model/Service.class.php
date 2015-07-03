<?php

/**
 * Classe de integração de serviços com vtex
 * @author Sóstenes Gomes
 * Class Service
 */

class Service {

    public function __construct() {

        $action = URI::get_segment(2, false);
        $data = count($_POST) ? $_POST : URI::get_params();

        /*verificar se é um json valido e atribuir o json decodificado */
        if (isset($data['json']))
            $data = VtexSoap::JsonToData($data['json']);

        switch($action){
            case 'atualizar-preco':
                $this->updatePrice($data);
                break;

            case 'vincular-sku':
                $this->bindSku($data);
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

    private function ServicePriceInsertUpdate(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->ServicePriceInsertUpdate($this);

            if ( is_object($result) and property_exists($result, 'ServicePriceInsertUpdateResult')){
                return ( is_object($result->ServicePriceInsertUpdateResult) and property_exists($result->ServicePriceInsertUpdateResult, 'Id')) ? $result->ServicePriceInsertUpdateResult : NULL;
            }

            return NULL;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }

    }

    private function updatePrice($data){

        $this->populate($data);
        $response = $this->ServicePriceInsertUpdate();

        if ($response !== NULL){
            $message = 'Preço de Serviço atualizado com sucesso';
            Handler::success($message, json_encode($response));
        }else{
            $message = 'Falha na integração: Preço de serviço não atualizado';
            Handler::error($message, json_encode($response));
        }

    }

    private function StockKeepingUnitServiceInsertUpdate(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->StockKeepingUnitServiceInsertUpdate($this);

            if ( is_object($result) and property_exists($result, 'StockKeepingUnitServiceInsertUpdateResult')){
                return ( is_object($result->StockKeepingUnitServiceInsertUpdateResult) and property_exists($result->StockKeepingUnitServiceInsertUpdateResult, 'Id')) ? $result->StockKeepingUnitServiceInsertUpdateResult : NULL;
            }

            return NULL;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }

    }

    private function bindSku($data){

        $this->populate($data);
        $response = $this->StockKeepingUnitServiceInsertUpdate();

        if ($response !== NULL){
            $message = 'Vínculo de Serviço a Sku realizado com sucesso';
            Handler::success($message, json_encode($response));
        }else{
            $message = 'Falha na integração: Vínculo de Serviço a Sku não realizado';
            Handler::error($message, json_encode($response));
        }

    }

}