<?php 
/**
 * Classe com as funções para admin
 * @since 0.1
 */
class WoocommerceQSTIAdmin extends WoocommerceDisplayEbitBanner
{

    /**
     * Construtor da classe
     * @since 0.1
    */
    function __construct()
    {
        
    }
        
    /**
     * Função requerimentos para inicializar o plugin
     * @since 0.1
    */
    function wc_qsti_require_woocommerce_plugin(){

        // Verify that CF7 is active and updated to the required version (currently 3.9.0)
        if ( is_plugin_active('woocommerce/woocommerce.php') ) {
            
            $wc_path = plugin_dir_path( dirname(__DIR__) ) . 'woocommerce/woocommerce.php';
            $wc_plugin_data = get_plugin_data( $wc_path, false, false);
            $wc_current_version = $wc_plugin_data['Version'];
            $wc_version = (int)preg_replace('/[.]/', '', $wc_current_version);
            
            // CF7 drops the ending ".0" for new major releases (e.g. Version 4.0 instead of 4.0.0...which would make the above version "40")
            // We need to make sure this value has a digit in the 100s place.
            if ( $wc_version < 100 ) {
                $wc_version = $wc_version * 10;
            }

            // If Woocommerce version is < 3.3.5
            if ( $wc_version < $this->min_woocommerce_version ) {
                echo '<div class="error"><p><strong>'. __('Warning:', 'wc_qsti') . '</strong> '. __('Your Woocommerce version is: ','wc_qtsi') . $wc_current_version .  __('. Display Ebit Banner requires that you have the latest version of Woocommerce installed. Please upgrade now', 'wc_qsti') .'</p></div>';
            }

        }
        // If it's not installed and activated, throw an error
        else {
            echo '<div class="error"><p>' . __('Woocommerce is not activated. Woocommere Plugin must be installed and activated before you can use Display Ebit Banner', 'wc_qsti') .'</p></div>';
        }

        return $this->wc_qsti_load_order_query();
    }

    /**
     * Função que adiciona uma aba de administração no Woocommmerce
     * @since 0.1
     */
    function wc_qsti_admin_config($sections) {
        $sections['wc_qsti'] = __('Banner Ebit', 'wc_qsti');
        return $sections;
    }

    /**
     * Função que adiciona uma aba de administração no Woocommmerce
     * @since 0.1
     */
    function wc_qsti_admin_config_settings($settings, $current_section) {

        if ($current_section == 'wc_qsti') {

            $settings_plugin = array();
            
            // Add Title to the Settings
            $settings_plugin[] = array( 
                'name' => __( 'Banner Ebit', 'wc_qsti' ), 
                'type' => 'title', 
                'desc' => __( 'Aqui você define o parametro que cadastrou em sua conta Pagseguro. Assim conseguimos retornar os dados do pedido para renderizar corretamente o banner Ebit.', 'wc_qsti' ), 
                'id' => 'wc_qsti' );
            
            // Add text field option
            $settings_plugin[] = array(
                'name'     => __( 'Parametro de Transação', 'wc_qsti' ),
                'desc_tip' => __( 'This will add a title to your slider', 'wc_qsti' ),
                'id'       =>  parent::$option_name,
                'default'  => __('Teste de pagamento', 'wc_qsti'),
                'type'     => 'text',
                'css'      => 'max-width: 200px;',
                'desc'     => __( 'Parametro definido como retorno no seu gateway. Default: "_transaction_id".', 'wc_qsti' )
            );
            
            $settings_plugin[] = array( 
                'type' => 'sectionend', 
                'id' => 'wc_qsti' );
            
            return $settings_plugin;
        
        }
        else{
            return $settings;
        }
        
    }

    /**
     * Função que salvas as configurações do plugin
     * @since 0.1
     */
    function wc_qsti_save_config($postData) {
        
        /** Verifica se houve dados enviados via POST */
        if(!isset($postData) || !array_key_exists( parent::$option_name, $postData)){
            return true;
        }

        /** Adiciona o valor de config a variavel */
        $configData = filter_var($postData[parent::$option_name], FILTER_SANITIZE_STRING);

        /** Faz o update da configuração no BD Wordpress */
        $result = update_option(parent::$option_name, $configData);

        /** Retorna o resultado */
        return $result;
        
    }

    /**
     * Mostrar erros ao salvar
     * @since 0.1
     */
    function wc_qsti_save_error(){
        echo '<div class="error"><p>'.  __('Error in Saving', 'wc_qsti') .'</p></div>';
    }
}