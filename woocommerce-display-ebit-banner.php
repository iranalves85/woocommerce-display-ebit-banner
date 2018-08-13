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
    
    static $min_woocommerce_version;
    
    //Parametros definidos pelo usuário
    static $option_name;
    static $buscape_id;
    static $ebit_id;
    
    static $savedData;

    /**
     * Função de construção da classe
     * @since 0.1
    */
    function __construct()
    {   
        //Menor versão Woocommerce suportada
        self::$min_woocommerce_version = (int) 335;
        self::$option_name  = 'wc_qsti_display_banner_ebit_config';
        self::$buscape_id   = 'wc_qsti_display_banner_buscape_id';
        self::$ebit_id      = 'wc_qsti_display_banner_ebit_id';
        self::$savedData    = $this->wc_qsti_load_var();

    }     

    /**
     * Função de inicialização
     * @since 0.1
    */
    public function init(){

        /** Instanciar classe de admin */
        $adminClass = new WoocommerceQSTIAdmin();

        /* Add a custom meta_data to query via Woocommerce Query */
        add_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'wc_qsti_custom_query_var'), 10, 2 );

        /* Add shortcode to display banners */
        add_filter('add_shortcode', array($this, '')); 
        
        /* Show notice if woocommerce not installed or disabled */
        add_action( 'admin_notices', array($adminClass, 'wc_qsti_require_woocommerce_plugin') );

        /* Add a new section in Woocommerce Admin */
        add_filter('woocommerce_get_sections_products', array($adminClass, 'wc_qsti_admin_config'), 10, 2);

        /* Add settings in Woocommerce Admin */
        add_filter('woocommerce_get_settings_products', array($adminClass, 'wc_qsti_admin_config_settings'), 1, 2);

        /* Salvamento de configurações */
        add_action('woocommerce_update_options_products', array($adminClass, 'wc_qsti_save_config'));

        /* Add settings in Woocommerce Admin */
        add_shortcode('wc_qsti_ebit_banner', array($this, 'wc_qsti_show_ebit_banner'), 1, 2);
        
    }
    
    /**
     * Função para habilitar Woocommerce a retornar dados de pedidos baseado no parametro '_transaction_id'
     * @since 0.1
    */
    function wc_qsti_custom_query_var( $query, $query_vars ) {

        /** Valor armazenado nas configurações */
        $transaction_id = self::$savedData;
        
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

        /** DEFINIR VALIDAÇÃO DE PÁGINA, SE SHORTCODE ESTIVER NA PÁGINA DE COMPRA FINALIZADA FAZ QUERY BASEADO NELA. SE NÃO, PEGA VALOR VIA PARAMETRO EM URL. */
        
        //Retornar parametro do banco
        $parameterDefined = self::$savedData;

        //Pega parametro e valor da URL
        if (!isset($_GET) || !array_key_exists($parameterDefined, $_GET)) {
            return false;
        }    

        //Definindo transacao direto da url
        $transaction_id = filter_var($_GET[$parameterDefined], FILTER_SANITIZE_SPECIAL_CHARS);

        //Verifica se função WC que retorna dados dos pedidos
        if (!function_exists('wc_get_orders')) {
            $this->wc_qsti_register_error(__('Função "wc_get_orders" não existe. Provavelmente WC plugin desabilitado.', 'wc_qsti'));
            stop(E_ERROR);
        }

        //Retorna os dados do pedido pelo código da transação
        $orderData = wc_get_orders(array('_transaction_id' => $transaction_id));

        return $orderData;
    }

    /**
     * Função para retornar dados do cliente referente ao pedido
     * @since 0.1
    */
    function wc_qsti_load_costumer_data(){

        //Verifica se função WC que retorna dados dos pedidos
        if (!function_exists('wc_get_orders')) {
            $this->wc_qsti_register_error(__('Função "wc_get_orders" não existe. Provavelmente WC plugin desabilitado.', 'wc_qsti'));
            stop(E_ERROR);
        }

        //Retorna os dados do pedido pelo código da transação
        $orderData = wc_get_orders(array('_transaction_id' => $transaction_id));

        return $orderData;
    }

    /**
     * Função de shortcode para, finalmente, exibir o banner na página de redirecionamento
     * @since 0.1
    */    
    function wc_qsti_show_ebit_banner($atts){

        //Retorna dados da transação WC_Order
        $queryTransaction = $this->wc_qsti_load_order_query();

        //Retorna dados do pedido em forma de array
        $order = $queryTransaction[0]->get_data();
        $shippingMetadata = $order['meta_data'];
        $shipping = $order['shipping_lines'];
        $products = $order['line_items'];

        foreach ($order as $key => $value) {
            echo $key . ' => ';
            print_r($value);
            echo "<br /><br />";
        }

        //Verifica se usuário é visitante ou registrado para retornar dados
        if(array_key_exists('customer_id', $order) && !self::wc_qsti_empty($order['customer_id'])){
            /* CHAMAR FUNÇÂO DE RETORNO DE DADOS DE USUÁRIO */
        }

        $html = '<param id="ebitParam" value="email=email&gender=gender&birthDay=birthDay&zipCode=zipCode&parcels=parcels&deliveryTax=deliveryTax&deliveryTime=deliveryTime&totalSpent=totalSpent&value=value&quantity=quantity&productName=productName&transactionId=transactionId&ean=ean&sku=sku&buscapeId=BuscapeId&storeId=93710"/>';

        //Campos obrigatórios para submissão Ebit
        $require_keys = array('email', 'deliveryTax', 'deliveryTime', 'totalSpent', 'value', 'quantity', 'productName', 'transactionId', 'sku');

        $meta_data_keys = array(
            'parcelas'          => array('Parcelas'),
            'prazoEntrega'      => array(''),
            'tipoPagamento'     => array('Tipo de pagamento'),
            'metodoPagamento'   => array('Método de pagamento')
        );

        foreach ($shipping as $key => $value) {
            print_r($value->get_data());
            echo "<br />";
        }

        echo "<br />";

        foreach ($shippingMetadata as $key => $value) {
            print_r($value->get_data());
            echo "<br />";
        }

        $args = array(
            'email' => $order['billing']['email'], //(obrigatório)
            'gender' => '', //F | M
            'birthDay' => '', //DD-MM-AAAA
            'zipCode' => str_replace('-', '', $order['billing']['postcode']), //
            'parcels' => '', //Int
            'deliveryTax' => $order['shipping_total'], //Decimal - Valor Frete (obrigatório)
            'deliveryTime' => 0, //Int - Tempo do Frete (obrigatório)
            'totalSpent' => $order['total'],//Decimal - Valor Total (obrigatório)
            'value' => 0.00, //Decimal - Valor de cada produto (obrigatório)
            'quantity' => 0, //Int - qtd de cada produto (obrigatório)
            'productName' => '', //Nome de cada produto (obrigatório)
            'transactionId' => $order['transaction_id'],//ID da transação (obrigatório)
            'ean' => '', //Código EAN
            'sku' => '', //Código SKU de cada produto (obrigatório)
            'BuscapeId' => self::$buscape_id,//
            'storeId' => self::$ebit_id
        );

        //$args = wp_parse_args( array(), $defaults );

        /*$html .= '<a id="bannerEbit"></a>';

        $html .= '<script type="text/javascript" id="getSelo" src="https://imgs.ebit.com.br/ebitBR/selo-ebit/js/getSelo.js'. self::$ebit_id .'&lightbox=true"></script>';*/

        //esc_html_e($html);
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
    public function wc_qsti_empty($var){

        /** Se versão for maior que PHP 5.4 */
        if (function_exists('empty')) {
            return empty($var);
        }
        
        /** Se string for vazia */
        if (is_string($var)) {
            return $var == '';
        }

        /** Se numero for menor ou igual a zero */
        if($var <= 0){
            return true;
        } 
        
        /** Se array for menor ou igual a zero */
        if(count($var) <= 0){
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