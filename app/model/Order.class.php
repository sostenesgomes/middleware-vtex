<?php

/**
 * Classe para integração de pedidos Vtex
 * @author Sóstenes Gomes
 * Class Order
 */

class Order {

    public function __construct(){

        $action = URI::get_segment(2, false);
        $data   = count($_POST) ? $_POST : URI::get_params();

        if (!isset($data['json']))
            Handler::error('Falha no processo de Integração: É preciso enviar o json no formato válido : ' . json_encode(URI::get_segments()));

        $json = $data['json'];

        switch($action){
            case 'consultar-por-status':
                $this->getByStatus($json);
                break;

            case 'consultar-por-id':
                $this->getById($json);
                break;

            case 'iniciar-manuseio':
                $this->startHandling($json);
                break;

            case 'cancelar':
                $this->cancel($json);
                break;

            case 'confirmar-pagamento-boleto':
                $this->paymentConfirmationBillet($json);
                break;

            case 'notificacao-de-envio':
                $this->shippingNotification($json);
                break;

            case 'mapeamento-de-email':
                $this->emailMapping($json);
                break;

            case 'transacao-iteracao':
                $this->orderTransactionIteration($json);
                break;

            default:
                Handler::error('Falha na integração: Ação não encontrada na integração de ' . json_encode(URI::get_segments()));
                break;
        }

    }

    private function getByStatus($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataStatus = json_decode($data);

        if ( (property_exists($dataStatus, 'status')) and (property_exists($dataStatus, 'page')) )
            $service = preg_replace( array('/@STATUS@/', '/@PAGE@/'), array($dataStatus->status, $dataStatus->page), $restParams['url_order_get_by_status']);
        else
            Handler::error('Falha na integração: É preciso enviar os parâmetros (status e page) para realizar a consulta: ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $data  = null;
        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token, false);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'list')){
                Handler::success('Pedidos consultados com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Pedidos não consultados. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Estoque não consultado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Pedidos não consultados :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    private function getById($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataId = json_decode($data);

        if (property_exists($dataId, 'orderId'))
            $service = preg_replace('/@ORDERID@/', $dataId->orderId, $restParams['url_order_get_by_id']);
        else
            Handler::error('Falha na integração: É preciso enviar o orderId a ser consultado: ' . __METHOD__,  ((is_string($data)) ? $data : '' ));

        $data  = null;
        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token, false);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'orderId')){
                Handler::success('Pedido consultado com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Pedido não consultado. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Pedido não consultado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Pedido não consultado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    private function startHandling($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataUpdate = json_decode($data);

        if ( property_exists($dataUpdate, 'orderId'))
            $service = preg_replace('/@ORDERID@/', $dataUpdate->orderId, $restParams['url_order_update_status']);
        else
            Handler::error('Falha na integração: É preciso enviar orderId para alteração de status do pedido: ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $data  = null;
        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if ( (is_string($response) and strlen($response) == 0) or (VtexRest::status204NoContent() === true) ){
            Handler::success('Pedido atualizado com sucesso', json_encode(array('status' => 'true')));

        }elseif (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Pedido não atualizado. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Pedido não atualizado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Pedido não atualizado: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    public function cancel($data){

        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataCancel = json_decode($data);

        if ( property_exists($dataCancel, 'orderId'))
            $service = preg_replace('/@ORDERID@/', $dataCancel->orderId, $restParams['url_order_cancel']);
        else
            Handler::error('Falha na integração: É preciso enviar orderId para cancelamento do pedido: ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $data  = null;
        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'orderId')){
                Handler::success('Solicitação de cancelamento realizada com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Solicitação de cancelamento não realizada. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Solicitação de cancelamento não realizada. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Solicitação de cancelamento de pedido não realizada :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    /* metodo para enviar confirmação de pagamento do boleto */
    public function paymentConfirmationBillet($data){
        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataPayment = json_decode($data);

        if ( property_exists($dataPayment, 'orderId') and property_exists($dataPayment, 'paymentId') ){
            $pattern = array('/@ORDERID@/', '/@PAYMENTID@/');
            $replace = array($dataPayment->orderId, $dataPayment->paymentId);
            $service = preg_replace($pattern, $replace, $restParams['url_order_pcb']);
        }else
            Handler::error('Falha na integração: É preciso enviar orderId e paymentId para confirmação de pagamento do boleto bancário: ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $data  = null;
        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if ( (is_string($response) and strlen($response) == 0) or (VtexRest::status204NoContent() === true) ){
            Handler::success('Solicitação de confirmação de pagamento do boleto bancário realizada com sucesso', json_encode(array('status' => 'true')));

        }elseif (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Solicitação de confirmação de pagamento do boleto bancário não realizada. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Solicitação de confirmação de pagamento do boleto bancário não realizada. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Solicitação de confirmação de pagamento do boleto bancário não realizada: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    public function shippingNotification($data){
        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataNotification = json_decode($data);

        if ( property_exists($dataNotification, 'orderId') ){
            $service = preg_replace('/@ORDERID@/', $dataNotification->orderId, $restParams['url_order_shipping_notification']);
        }else
            Handler::error('Falha na integração: É preciso enviar orderId para notificação de envio: ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'orderId')){
                Handler::success('Notificação de envio realizada com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Notificação de envio não realizada. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Notificação de envio não realizada. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Notificação de envio não realizada :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    public function emailMapping($data){
        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataEmailMapping = json_decode($data);

        if ( property_exists($dataEmailMapping, 'alias') ){
            $service = preg_replace('/@ALIAS@/', $dataEmailMapping->alias, $restParams['url_order_email_mapping']);
        }else
            Handler::error('Falha na integração: É preciso enviar alias para mapeamento de email : ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token, false);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( is_object($jsonDecode) and property_exists($jsonDecode, 'email')){
                Handler::success('Mapeamento de email realizado com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Mapeamento de email não realizado. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Mapeamento de email não realizado. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Mapeamento de email não realizado :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }

    public function orderTransactionIteration($data){
        VtexRest::validateJson($data);

        $restParams = Config::get('vtex_params_rest');

        $dataIteration = json_decode($data);

        if ( property_exists($dataIteration, 'transaction_id') ){
            $service = preg_replace('/@TRANSACTIONID@/', $dataIteration->transaction_id, $restParams['url_order_transaction_iteration']);
        }else
            Handler::error('Falha na integração: É preciso enviar transaction_id para pegar as iterações da transação : ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        $key   = $restParams['app_key'];
        $token = $restParams['app_token'];

        $response = VtexRest::execute($data, $service, $key, $token, false);

        if (Uteis::isJson($response)){
            $jsonDecode = json_decode($response);

            if ( isset($jsonDecode[0]) and is_object($jsonDecode[0]) and property_exists($jsonDecode[0], 'TransactionId')){
                Handler::success('Consulta de transação realizada com sucesso', $response);

            }elseif( is_object($jsonDecode) and property_exists($jsonDecode, 'error')){
                Handler::error('#A: Falha no processo de Integração: Consulta de transação não realizada. ' . __METHOD__, $response);

            }else{
                Handler::error('#B: Falha no processo de Integração: Consulta de transação não realizada. ' . __METHOD__, $response);
            }

        }else{
            Handler::error('Falha no processo de Integração: Consulta de transação não realizada :: Retorno fora do padrão json: ' . __METHOD__, ((is_string($response)) ? $response : '' ) );
        }
    }



}