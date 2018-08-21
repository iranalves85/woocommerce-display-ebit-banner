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

/**
 * Verifica se classe foi iniciada
 * @since 0.1
 */
if( !isset($wdeb) || !class_exists($wdeb)) {
    require_once('inc/class-wc-qsti-banner.php');
    require_once('inc/class-wc-qsti-admin.php');    
    $wdeb = new WoocommerceDisplayEbitBanner();
}

/**
 * Inicializar plugin
 * @since 0.1
 */
$wdeb->init();