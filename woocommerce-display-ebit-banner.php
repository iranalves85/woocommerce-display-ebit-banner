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
    /**
     * Função de construção da classe
     * @since 0.1
    */
    function __construct()
    {   
        
    }     

    /**
     * Função de inicialização
     * @since 0.1
    */
    public function init(){
        
        $adminClass = new WoocommerceQSTIAdmin();
        $adminClass::wc_qsti_require_woocommerce_plugin();

        /* Add a custom meta_data to query via Woocommerce Query */
        add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array($this, 'wc_qsti_custom_query_var'), 10, 2 );

        /* Add shortcode to display banners */
        add_filter('add_shortcode', array($this, ''));        

        /* Add a new section in Woocommerce Admin */
        add_filter( 'woocommerce_get_sections_advanced', array($adminClass, 'wc_qsti_admin_config'),10, 2);

        /* Add settings in Woocommerce Admin */
        add_filter( 'woocommerce_get_settings_advanced', array($adminClass, 'wc_qsti_admin_config_settings'), 1, 2);
    }
    
    /**
     * Função para habilitar Woocommerce a retornar dados de pedidos baseado no parametro '_transaction_id'
     * @since 0.1
    */
    function wc_qsti_custom_query_var( $query, $query_vars, $userParameter = '' ) {
        
        /* Nota: _transaction_id pode ser alterado pelo usuário */

        if ( ! empty( $query_vars['_transaction_id'] ) ) {
            $query['meta_query'][] = array(
                'key' => '_transaction_id',
                'value' => esc_attr( $query_vars['_transaction_id'] ),
            );
        }
        return $query;
    }
    
    /**
     * Função para verificar variável
     * @since 0.1
    */
    function wc_qsti_load_var(){
    
        //Pega parametro via url
        $transaction_id = (isset($_GET['transaction_id']))? (string) $_GET['transaction_id'] : NULL;
        
        return $transaction_id;
    }

    /**
     * Função para registrar erros
     * @since 0.1
     */   
    private function wc_qsti_register_error($stringError){

    }
    
    /**
     * Função para retornar data da transação do Banco de Dados
     * @since 0.1
    */
    function wc_qsti_load_order_query(){
    
        $transaction_id = $this::wc_qsti_load_var();
    
        //Verifica se variavel possui algum valor
        if(!is_null($transaction_id)){
            
            if (!function_exists('wc_get_orders')) {
                $this::wc_qsti_register_error('Erro');
                return;
            }
            
            $orderData = wc_get_orders(array('_transaction_id' => $transaction_id));
            
            return $orderData;
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
