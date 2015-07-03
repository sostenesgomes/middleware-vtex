<?php

/**
 * Classe para integração de imagens com vtex
 * @author Sóstenes Gomes
 * Class SkuImage
 */

class SkuImage {

    public function __construct(){
        $action = URI::get_segment(2, false);
        $data = count($_POST) ? $_POST : URI::get_params();

        /*verificar se é um json valido e atribuir o json decodificado */
        if (isset($data['json']))
            $data = VtexSoap::jsonToData($data['json']);

        switch($action){
            case 'adicionar':
                $this->insert($data);
                break;

            case 'remover':
                $this->delete($data);
                break;

            case 'remover-todas':
                $this->deleteAll($data);
                break;

            default:
                Handler::error('Falha na integração: Ação não encontrada na integração de ' . json_encode(URI::get_segments()));
                break;
        }

    }

    private function populate($data){
        foreach($data AS $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }

    private function insert($data){
        $this->populate($data);

        if ($this->ImageServiceInsertUpdate() === true){
            $message = 'Imagem de sku inserida com sucesso';
            Handler::success($message, json_encode(array('status' => 'true')));
        }else{
            $message = 'Falha na integração: Imagem de sku não inserida';
            Handler::error($message, json_encode(array('status' => 'false')));
        }

    }

    private function delete($data){
        $this->populate($data);

        if ($this->StockKeepingUnitImageRemoveByName() === true){
            $message = 'Imagem de sku removida com sucesso';
            Handler::success($message, json_encode(array('status' => 'true')));
        }else{
            $message = 'Falha na integração: Imagem de sku não removida';
            Handler::error($message, json_encode(array('status' => 'false')));
        }

    }

    private function deleteAll($data){
        $this->populate($data);

        if ($this->StockKeepingUnitImageRemove() === true){
            $message = 'Imagens de sku removidas com sucesso';
            Handler::success($message, json_encode(array('status' => 'true')));
        }else{
            $message = 'Falha na integração: Imagens não removidas';
            Handler::error($message, json_encode(array('status' => 'false')));
        }

    }

    private function ImageServiceInsertUpdate(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->ImageServiceInsertUpdate($this);

            return (is_object($result)) ? true : false;

        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }
    }

    private function StockKeepingUnitImageRemoveByName(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->StockKeepingUnitImageRemoveByName($this);

            return (is_object($result)) ? true : false;
        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }
    }

    private function StockKeepingUnitImageRemove(){

        try{
            $soapClient = VtexSoap::createSoapclient();
            $result     = $soapClient->StockKeepingUnitImageRemove($this);

            return (is_object($result)) ? true : false;
        }catch (SoapFault $e){
            VtexSoap::setResponseHeader($soapClient);

            $message = "Falha no processo de Integração. Método: " . __METHOD__;
            $messageVtex = json_encode(array('message' => $e->getMessage()));

            Handler::error($message, $messageVtex);
        }
    }

}