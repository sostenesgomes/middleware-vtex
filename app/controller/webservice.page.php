<?php

class Webservice_Controller {

    public function __construct() {

        header('Content-Type: text/xml; charset=UTF-8');

        switch(URI::get_segment(1, false)){

            case 'produto':
                new Product();
                break;

            case 'sku':
                new Sku();
                break;

            case 'sku-imagem':
                new SkuImage();
                break;

            case 'sku-estoque':
                new SkuStock();
                break;

            case 'sku-preco':
                new SkuPrice();
                break;

            case 'pedido':
                new Order();
                break;

            case 'servico':
                new Service();
                break;

            case 'ferramenta':
                new Tool();
                break;

            case 'cliente':
                new Client();
                break;

            case 'vtrine-vtex':
                new VtrineVtex();
                break;

            default:
                Handler::error('Escolha um método para integracao');
                break;
        }
    }
}
?>