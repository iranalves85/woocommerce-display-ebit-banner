<?php

/*
 * Plugin Name: Woocommerce Display Ebit Banner
 * Description: Mostrar o banner do Ebit com dados da venda realizada para avaliação
 * Version: 0.1
 * Author: Iran Alves
 * Author URI: makingpie.com.br
 * License: GPLv3
 * Copyright (C) 2018 Iran
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Verifica se classe foi iniciada
 * @since 0.1
 */
if( !isset($wc_qsti) || !class_exists('WoocommerceDisplayEbitBanner')) {
    require_once('inc/class-wc-qsti-banner.php');
    require_once('inc/class-wc-qsti-admin.php');    
    $wc_qsti = new WoocommerceDisplayEbitBanner();
}

/**
 * Inicializar plugin
 * @since 0.1
 */
$wc_qsti->init();