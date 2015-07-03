<?php

/**
 * Classe que fornece ferramentas utéis
 *
 * @author Sóstenes Gomes
 * Class Tool
 */
class Tool {

    public function __construct(){

        $action = URI::get_segment(2, false);
        $data   = count($_POST) ? $_POST : URI::get_params();

        if (!isset($data['json']))
            Handler::error('Falha no processo de Integração: É preciso enviar o json no formato válido : ' . json_encode(URI::get_segments()));

        $json = $data['json'];

        switch($action){
            case 'gerar-slug':
                $this->slugGenerator($json);
                break;

            default:
                Handler::error('Falha na integração: Ação não encontrada na integração de ' . json_encode(URI::get_segments()));
                break;
        }

    }

    private function slugGenerator($data){

        VtexRest::validateJson($data);

        $dataSlug = json_decode($data);

        if (property_exists($dataSlug, 'name'))
            $slug = URI::slug_generator($dataSlug->name);
        else
            Handler::error('Falha na integração: O name não foi enviado para geração do slug: ' . __METHOD__, ((is_string($data)) ? $data : '' ));

        Handler::success('Slug gerado com sucesso', json_encode(array('slug' => $slug)));

    }

}