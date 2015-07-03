<?php

class teste_Controller{
	
	protected $pasta = 'teste';
	
	public function __construct(){
        Template::registryCSS(array('teste/index.css'));

        Template::registryJSFooter(array('jquery/jquery-2.1.1.min.js'));
        Template::registryJSFooter(array('teste/index.js'));

        Template::defineObjeto('formActionBase', URI::build_url(array('webservice')));


        if (URI::get_segment(1, false) == 'produto'){

            $optionsTeste = array(
                'Adicionar Produto' => URI::build_url(array('webservice', 'produto', 'adicionar')),
                'Atualizar Produto' => URI::build_url(array('webservice', 'produto', 'atualizar')),
            );

            Template::defineObjeto('tituloTeste', 'Produto');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }elseif (URI::get_segment(1, false) == 'sku'){

            $optionsTeste = array(
                'Adicionar Sku'          => URI::build_url(array('webservice', 'sku', 'adicionar')),
                'Atualizar Sku'          => URI::build_url(array('webservice', 'sku', 'atualizar')),
                'Ativar Sku se Possível' => URI::build_url(array('webservice', 'sku', 'ativar-se-possivel')),
            );

            Template::defineObjeto('tituloTeste', 'SKU');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }elseif (URI::get_segment(1, false) == 'sku-imagem'){

            $optionsTeste = array(
                'Adicionar imagem de Sku'           => URI::build_url(array('webservice', 'sku-imagem', 'adicionar')),
                'Remover imagem de Sku'             => URI::build_url(array('webservice', 'sku-imagem', 'remover')),
                'Remover todas as imagens do sku'   => URI::build_url(array('webservice', 'sku-imagem', 'remover-todas')),
            );

            Template::defineObjeto('tituloTeste', 'Imagem de sku');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }elseif (URI::get_segment(1, false) == 'sku-estoque'){

            $optionsTeste = array(
                'Consultar Estoque de sku' => URI::build_url(array('webservice', 'sku-estoque', 'consultar')),
                'Atualizar Estoque de sku' => URI::build_url(array('webservice', 'sku-estoque', 'atualizar')),
                'Consultar Estoque reservado de sku' => URI::build_url(array('webservice', 'sku-estoque', 'reservados')),
            );

            Template::defineObjeto('tituloTeste', 'Estoque de sku');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }elseif (URI::get_segment(1, false) == 'sku-preco'){

            $optionsTeste = array(
                'Atualizar Preço de sku' => URI::build_url(array('webservice', 'sku-preco', 'atualizar')),
            );

            Template::defineObjeto('tituloTeste', 'Preço de sku');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }elseif (URI::get_segment(1, false) == 'pedido'){

            $optionsTeste = array(
                'Consultar Por Status'                  => URI::build_url(array('webservice', 'pedido', 'consultar-por-status')),
                'Consultar Por Id'                      => URI::build_url(array('webservice', 'pedido', 'consultar-por-id')),
                'Iniciar Manuseio'                      => URI::build_url(array('webservice', 'pedido', 'iniciar-manuseio')),
                'Cancelar'                              => URI::build_url(array('webservice', 'pedido', 'cancelar')),
                'Confirmação de Pagamento do Boleto'    => URI::build_url(array('webservice', 'pedido', 'confirmar-pagamento-boleto')),
                'Notificação de Envio'                  => URI::build_url(array('webservice', 'pedido', 'notificacao-de-envio')),
                'Mapeamento de Email'                   => URI::build_url(array('webservice', 'pedido', 'mapeamento-de-email')),
                'Iterações de Transações Pedido'        => URI::build_url(array('webservice', 'pedido', 'transacao-iteracao'))
            );

            Template::defineObjeto('tituloTeste', 'Pedido');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }elseif (URI::get_segment(1, false) == 'ferramenta'){

            $optionsTeste = array(
                'Gerar Slug' => URI::build_url(array('webservice', 'ferramenta', 'gerar-slug')),
            );

            Template::defineObjeto('tituloTeste', 'Ferramenta');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }elseif (URI::get_segment(1, false) == 'servico'){

            $optionsTeste = array(
                'Adicionar/Atualizar Preço' => URI::build_url(array('webservice', 'servico', 'atualizar-preco')),
                'Vincular Sku' => URI::build_url(array('webservice', 'servico', 'vincular-sku')),
            );

            Template::defineObjeto('tituloTeste', 'Serviço');
            Template::defineObjeto('optionsTeste', $optionsTeste);

        }else{

            $links = array(
                'produto'       => array('titulo' => 'Produto',     'url' => URI::build_url(array('teste', 'produto'))),
                'sku'           => array('titulo' => 'Sku',         'url' => URI::build_url(array('teste', 'sku'))),
                'imagem'        => array('titulo' => 'Imagem',      'url' => URI::build_url(array('teste', 'sku-imagem'))),
                'skuEstoque'    => array('titulo' => 'Sku Estoque', 'url' => URI::build_url(array('teste', 'sku-estoque'))),
                'skuPreco'      => array('titulo' => 'Sku Preço',   'url' => URI::build_url(array('teste', 'sku-preco'))),
                'pedido'        => array('titulo' => 'Pedido',      'url' => URI::build_url(array('teste', 'pedido'))),
                'servico'       => array('titulo' => 'Serviço',     'url' => URI::build_url(array('teste', 'servico'))),
                'Ferramenta'    => array('titulo' => 'Ferramenta',  'url' => URI::build_url(array('teste', 'ferramenta')))
            );

            Template::defineObjeto('links', $links);

            Template::defineTemplate($this->pasta, 'index');
        }

        Template::defineTemplate($this->pasta, 'form');
    }
}