<?php

/**
 * Classe de integração de dados de sku vtex
 * @author Sóstenes Gomes
 * Class Sku
 */

class Sku {

    public function __construct() {

        $action = URI::get_segment(2, false);
        $data = count($_POST) ? $_POST : URI::get_params();

        /*verificar se é um json valido e atribuir o json decodificado */
        if (isset($data['json']))
            $data = VtexSoap::jsonToData($data['json']);

        switch($action){
            case 'adicionar':
                $this->insert($data);
                break;

            case 'atualizar':
                $this->update($data);
                break;

            case 'ativar-se-possivel':
                $this->active($data);
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

    private function StockKeepingUnitGet(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->StockKeepingUnitGet(array('id' => $this->ProductId));

            if ( is_object($result) and property_exists($result, 'StockKeepingUnitGetResult')){
                return ( is_object($result->StockKeepingUnitGetResult) and property_exists($result->StockKeepingUnitGetResult, 'Id')) ? $result->StockKeepingUnitGetResult : NULL;
            }

            return NULL;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }
    }

    private function StockKeepingUnitGetByRefId(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->StockKeepingUnitGetByRefId(array('refId' => $this->RefId));

            if ( is_object($result) and property_exists($result, 'StockKeepingUnitGetByRefIdResult')){
                return ( is_object($result->StockKeepingUnitGetByRefIdResult) and property_exists($result->StockKeepingUnitGetByRefIdResult, 'Id')) ? $result->StockKeepingUnitGetByRefIdResult : NULL;
            }

            return NULL;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }
    }

    private function StockKeepingUnitInsertUpdate(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->StockKeepingUnitInsertUpdate(array('stockKeepingUnitVO' => $this));

            if ( is_object($result) and property_exists($result, 'StockKeepingUnitInsertUpdateResult')){
                return ( is_object($result->StockKeepingUnitInsertUpdateResult) and property_exists($result->StockKeepingUnitInsertUpdateResult, 'Id')) ? $result->StockKeepingUnitInsertUpdateResult : NULL;
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

        $response = $this->StockKeepingUnitInsertUpdate();

        if ($response !== NULL){
            $message = 'Sku inserido com sucesso';
            Handler::success($message, json_encode($response));
        }else{
            $message = 'Falha na integração: Sku não inserido';
            Handler::error($message, json_encode($response));
        }

    }

    private function update($data){

        $this->populate($data);
        $skuDTO = $this->StockKeepingUnitGetByRefId();

        if ($skuDTO !== NULL){

            $this
                ->clear()
                ->populate($skuDTO)
                ->modifier($data);

            $response = $this->StockKeepingUnitInsertUpdate();

            if ($response !== NULL){
                $message = 'Sku atualizado com sucesso';
                Handler::success($message, json_encode($response));
            }else{
                $message = 'Falha na integração: Sku não atualizado';
                Handler::error($message, json_encode($response));
            }
        }else{
            Handler::error($message = "Falha no processo de Integração. Sku não encontrado. Método: " . __METHOD__);
        }

    }

    private function active($data){

        $this->populate($data);

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->StockKeepingUnitActive(array('idStockKeepingUnit' => $this->idStockKeepingUnit));

            if (is_object($result)){
                $message = 'Flag Ativar Sku se possível ativada com sucesso';
                Handler::success($message, json_encode(array("result" => "true")));
            }else{
                $message = 'Flag Ativar Sku se possível não foi ativada';
                Handler::error($message, json_encode(array("result" => "false")));
            }

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }

    }

}