<?php

/*
Plugin Name: Woocommerce - Display Ebit Banner
Description: Mostrar o banner do Ebit com dados da venda realizada para avaliação
Version: 0.1
Author: Iran Alves
Author URI: makingpie.com.br
License: GPL2
Copyright (C) 2018 Iran
*/

require_once('inc/class-wc-qsti-admin.php');

/**
 * Classe com as funções para implementar banner Ebit
 * @since 0.1
 */
class WoocommerceDisplayEbitBanner
{
    
    var $min_woocommerce_version;
    static $option_name; 

    /**
     * Função de construção da classe
     * @since 0.1
    */
    function __construct()
    {   
        //Menor versão Woocommerce suportada
        $this->min_woocommerce_version = (int) 335;
        self::$option_name = 'wc_qsti_pagseguro_parameter';
    }     

    /**
     * Função de inicialização
     * @since 0.1
    */
    public function init(){

        /** Instanciar classe de admin */
        $adminClass = new WoocommerceQSTIAdmin();

        /* Add a custom meta_data to query via Woocommerce Query */
        add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array($this, 'wc_qsti_custom_query_var'), 10, 2 );

        /* Add shortcode to display banners */
        add_filter('add_shortcode', array($this, '')); 
        
        /* Show notice if woocommerce not installed or disabled */
        add_action( 'admin_notices', array($adminClass, 'wc_qsti_require_woocommerce_plugin') );

        /* Add a new section in Woocommerce Admin */
        add_filter( 'woocommerce_get_sections_products', array($adminClass, 'wc_qsti_admin_config'), 10, 2);

        /* Add settings in Woocommerce Admin */
        add_filter( 'woocommerce_get_settings_products', array($adminClass, 'wc_qsti_admin_config_settings'), 1, 2);

        /* Add settings in Woocommerce Admin */
        add_shortcode( 'wc_qsti_ebit_banner', array($this, 'wc_qsti_show_ebit_banner'), 1, 2);

        /** Função para salvar configurações no admin */
        $saveResult = $adminClass->wc_qsti_save_config($_POST);
        
    }
    
    /**
     * Função para habilitar Woocommerce a retornar dados de pedidos baseado no parametro '_transaction_id'
     * @since 0.1
    */
    function wc_qsti_custom_query_var( $query, $query_vars ) {

        /** Valor armazenado nas configurações */
        $transaction_id = $this->wc_qsti_load_var();
        
        /** Registra parametro para executar querys */
        if ( ! empty( $query_vars[$transaction_id] ) ) {
            $query['meta_query'][] = array(
                'key'   => $transaction_id,
                'value' => esc_attr( $query_vars[$transaction_id] )
            );
        }

        return $query;
    }
    
    /**
     * Função para verificar variável
     * @since 0.1
    */
    public function wc_qsti_load_var(){
    
        $getDefined = get_option(self::$option_name);
        
        //Pega parametro via url
        $transaction_id = ( !$this->wc_qsti_empty($getDefined) )? $getDefined : '_transaction_id';
        
        return $transaction_id;
    }

    /**
     * Função para retornar dados da transação do Banco de Dados
     * @since 0.1
    */
    function wc_qsti_load_order_query(){

        //Retornar parametro do banco
        $parameterDefined = $this->wc_qsti_load_var();

        /** DEFINIR VALIDAÇÃO DE PÁGINA, SE SHORTCODE ESTIVER NA PÁGINA DE COMPRA FINALIZADA FAZ QUERY BASEADO NELA. SE NÃO, PEGA VALOR VIA PARAMETRO EM URL. */
        
        if (!isset($_GET) || !array_key_exists($parameterDefined, $_GET)) {
            return false;
        }    
        
        //Definindo transacao direto da url
        $transaction_id = filter_var($_GET[$parameterDefined], FILTER_SANITIZE_SPECIAL_CHARS);
        
        if (!function_exists('wc_get_orders')) {
            $this->wc_qsti_register_error(__('Função "wc_get_orders" não existe. Provavelmente WC plugin desabilitado.', 'wc_qsti'));
            stop(E_ERROR);
        }
        
        $orderData = wc_get_orders(array('_transaction_id' => $parameterDefined));
        
        return $orderData;
    }

    /**
     * Função de shortcode para, finalmente, exibir o banner na página de redirecionamento
     * @since 0.1
    */    
    function wc_qsti_show_ebit_banner($atts){

        echo "Mostrar o resultado da query: ";
        var_dump($this->wc_qsti_load_order_query());
    }

    /**
     * Função para registrar erros
     * @since 0.1
     */   
    private function wc_qsti_register_error($stringError){

    }

    /**
     * Função para verificação de variaveis vazias
     * @since 0.1
     */   
    private function wc_qsti_empty($string){

        /** Se versão for maior que PHP 5.4 */
        if (function_exists('empty')) {
            return empty($string);
        }
        
        /** Se string for vazia */
        if (is_string($string)) {
            return $string == '';
        }

        /** Se numero for menor ou igual a zero */
        if($String <= 0){
            return true;
        } 
        
        /** Se array for menor ou igual a zero */
        if(count($String) <= 0){
            return true;
        } 

    }
    
}

/**
 * Verifica se função foi definida
 * @since 0.1
 */
if( !isset($wdeb) || !class_exists($wdeb)) {
    $wdeb = new WoocommerceDisplayEbitBanner();
}

/**
 * Inicializar plugin
 * @since 0.1
 */
$wdeb->init();
