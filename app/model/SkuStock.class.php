<?php

/**
 * Classe de Integração para dados de estoque vtex
 *
 * @author Sóstenes Gomes
 * Class SkuStock
 */
class SkuStock {

    public function __construct()
    {
        $action = URI::get_segment(2, false);
        $data = count($_POST) ? $_POST : URI::get_params();

        if (!isset($data['json']))
            Handler::error('Falha no processo de Integração: É preciso enviar o json : ' . json_encode(URI::get_segments()));

        $json = $data['json'];

        switch ($action) {
            case 'consultar':
                $this->get($json);
                break;

            case 'atualizar':
                $this->update($json);
                break;

            case 'reservados':
                $this->getReservations($json);
                break;

            default:
                Handler::error('Falha na integração: Ação não encontrada na integração de ' . json_encode(URI::get_segments()));
                break;
        }

    }

    private function get($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $service = $restParams['url_stock_get'];
        $key     = $restParams['app_key'];
        $token   = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Estoque não consultado . ' . __METHOD__, $response);

            }elseif( (is_array($jsonDecode)) and (is_object(current($jsonDecode))) and (property_exists(current($jsonDecode), 'itemId') )){
                Handler::success('Estoque de sku consultado com sucesso', $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Estoque não consultado . ' . __METHOD__);
            }

        }else{
            Handler::error('Falha no processo de Integração: Estoque não consultado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ));
        }

    }

    /**
     * Permite consultar o estoque na Vtex com retorno direto para o controller, retorna um array
     *
     * @author Silas dos S. Silva
     *
     * @param $data
     * @return Mixed (Array se sucesso, String se erro)
     */
    static function getStatic($data) {
        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $service = $restParams['url_stock_get'];
        $key     = $restParams['app_key'];
        $token   = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if (Uteis::isJson($response))
            return json_decode($response);
        else
            Handler::error('#B: Falha no processo de Consulta Interna: Estoque Static não consultado . ' . __METHOD__);
    }

    private function update($data){

        VtexRest::validateJson($data);

        /** consultar estoque reservado */
        $consultReserved = array();
        $decodeData = json_decode($data);
        $decodeData = isset($decodeData[0]) ? $decodeData[0] : new stdClass();

        if ( (property_exists($decodeData, 'itemId')) and (property_exists($decodeData, 'wareHouseId'))){
            $consultReserved['itemId']      = $decodeData->itemId;
            $consultReserved['wareHouseId'] = $decodeData->wareHouseId;
        }

        /* processo de consulta do saldo reservado na vtex */
        if(isset($decodeData->quantity)){
            $quantidadeIterada     = $this->iteratorBalanceReserved(json_encode($consultReserved));
            $decodeData->quantity += $quantidadeIterada === -1 ? 0 : $quantidadeIterada;

            $decodeData->quantity = (string)$decodeData->quantity;
            $data = '[' . json_encode($decodeData) . ']';
            VtexRest::validateJson($data);
        }

        $restParams = Config::get('vtex_params_rest');

        $service = $restParams['url_stock_update'];
        $key     = $restParams['app_key'];
        $token   = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if ($response === 'true'){
            Handler::success('Estoque de sku atualizado com sucesso', json_encode(array('status' => 'true')));

        }elseif (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Estoque não atualizado. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Estoque não atualizado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Estoque não atualizado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ));
        }
    }

    private function iteratorBalanceReserved($data){

        $page = 1;
        $quantity = 0;

        $iterator = $this->getBalanceReserved($data, $page);

        $page     = $iterator['page'];
        $quantity += $iterator['quantity'];
        $pages    = $iterator['pages'];

        if($pages > 0){

            while($pages != $page){
                $page++;
                $iterator = $this->getBalanceReserved($data, $page);
                $page     = $iterator['page'];
                $quantity += $iterator['quantity'];
            }
        }

        return $quantity;
    }

    private function getBalanceReserved($data, $page){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $service = $restParams['url_stock_reservations'];

        $dataStock = json_decode($data);

        if ((property_exists($dataStock, 'itemId')) and (property_exists($dataStock, 'wareHouseId')))
            $service = preg_replace(array('/@WAREHOUSEID@/', '/@ITEMID@/'), array($dataStock->wareHouseId, $dataStock->itemId), $restParams['url_stock_reservations']);
        else
            Handler::error('Falha na integração: É preciso enviar os parâmetros (itemId e wareHouseId) para realizar a consulta: ' . __METHOD__, ((is_string($data)) ? $data : ''));

        $data = null;
        $key = $restParams['app_key'];
        $token = $restParams['app_token'];

        $service .= '?page='. $page .'&perPage=100';

        $response = VtexRest::execute($data, $service, $key, $token, false);

        if (Uteis::isJson($response)) {
            $jsonDecode = json_decode($response);

            /* sucesso */
            if ( (is_object($jsonDecode) and (property_exists($jsonDecode, 'items')) and property_exists($jsonDecode, 'paging')) ){
                $items    = $jsonDecode->items;
                $quantity = 0;

                foreach ($items as $item) {

                    if ( (!isset($item->Quantity)) or (!isset($item->DateUtcAcknowledgedOnBalanceSystem)) or (!isset($item->Status)) )
                        Handler::error('#C: Falha no processo de Integração: Item (Quantity | Status | DateUtcAcknowledgedOnBalanceSystem) não presente no array $items . ' . __METHOD__, $response);

                    /* se tem reserva pra cair, então desconsidera */
                    if ( ($item->Status == 'Authorized') or (($item->Status == 'Confirmed') and ($item->DateUtcAcknowledgedOnBalanceSystem == '0001-01-01T00:00:00')) )
                        $quantity += $item->Quantity;
                }

                return array(
                    'page'     => $jsonDecode->paging->page,
                    'pages'    => $jsonDecode->paging->pages,
                    'quantity' => $quantity
                );

            }elseif (is_object($jsonDecode) and property_exists($jsonDecode, 'error')) {
                if (property_exists($jsonDecode->error, 'exception')) {
                    if (property_exists($jsonDecode->error->exception, 'InnerException')) {
                        if (property_exists($jsonDecode->error->exception->InnerException, 'ClassName')) {
                            if ($jsonDecode->error->exception->InnerException->ClassName == 'System.ArgumentNullException' ||
                            $jsonDecode->error->exception->InnerException->ClassName == 'Vtex.Practices.ServiceModel.Client.Exceptions.InternalServerErrorException') {
                                return array(
                                    'page'     => 1,
                                    'pages'    => 0,
                                    'quantity' => -1
                                );
                            }
                        }
                    }
                }
                Handler::error('#A: Falha no processo de Integração: Estoque Reservado não consultado . ' . __METHOD__, $response);

            } else {
                Handler::error('#B: Falha no processo de Integração: Estoque reservado não consultado . ' . __METHOD__);
            }

        } else {
            Handler::error('Falha no processo de Integração: Estoque reservado não consultado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : ''));
        }
    }

    private function getReservations($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $service = $restParams['url_stock_reservations'];

        $dataStock = json_decode($data);

        if ( (property_exists($dataStock, 'itemId')) and (property_exists($dataStock, 'wareHouseId')) )
            $service = preg_replace( array('/@WAREHOUSEID@/', '/@ITEMID@/'), array($dataStock->wareHouseId, $dataStock->itemId), $restParams['url_stock_reservations']);
        else
            Handler::error('Falha na integração: É preciso enviar os parâmetros (itemId e wareHouseId) para realizar a consulta: ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $data  = null;
        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token, false);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Estoque Reservado não consultado . ' . __METHOD__, $response);

            }elseif( (is_array($jsonDecode)) or true /*and (is_object(current($jsonDecode))) and (property_exists(current($jsonDecode), 'items') )*/){
                Handler::success('Estoque reservado de sku consultado com sucesso', $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Estoque reservado não consultado . ' . __METHOD__);
            }

        }else{
            Handler::error('Falha no processo de Integração: Estoque reservado não consultado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ));
        }

    }
}