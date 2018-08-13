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
            if ( $wc_version < parent::$min_woocommerce_version ) {
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
        $sections['wc_qsti'] = __('Display Ebit Banner', 'wc_qsti');
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
                'name'     => __( 'ID Buscapé', 'wc_qsti' ),
                'desc_tip' => __( 'ID de sua loja no Buscapé', 'wc_qsti' ),
                'id'       =>  parent::$buscape_id,
                'default'  => __('', 'wc_qsti'),
                'type'     => 'text',
                'css'      => 'max-width: 200px;',
                'desc'     => __( 'Esse ID é disponibilizado após a integração do banner ocorrer.', 'wc_qsti' )
            );

            // Add text field option
            $settings_plugin[] = array(
                'name'     => __( 'ID Ebit', 'wc_qsti' ),
                'desc_tip' => __( 'Seu Id fornecido pela Ebit', 'wc_qsti' ),
                'id'       =>  parent::$ebit_id,
                'default'  => __('', 'wc_qsti'),
                'type'     => 'number',
                'css'      => 'max-width: 200px;',
                'desc'     => __( 'Id defindo para a sua loja no ambiente Ebit.', 'wc_qsti' )
            );
            
            // Add text field option
            $settings_plugin[] = array(
                'name'     => __( 'Parametro de Transação', 'wc_qsti' ),
                'desc_tip' => __( 'Parametro de retorno definido em seu gateway', 'wc_qsti' ),
                'id'       =>  parent::$option_name,
                'default'  => __('', 'wc_qsti'),
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
    function wc_qsti_save_config() {

        /** Verifica se houve dados enviados via POST */
        if(!isset($_POST) || !array_key_exists( parent::$option_name, $_POST) || !array_key_exists(parent::$buscape_id, $_POST) || !array_key_exists(parent::$ebit_id, $_POST)){
            return true;
        }

        /** Filtra e adiciona o valor de config a variavel */
        $configData = filter_var_array($_POST, FILTER_SANITIZE_STRING);

        $update = [];

        /** Faz o update da configuração "BUSCAPE ID" no BD Wordpress */
        if( parent::wc_qsti_empty($configData[parent::$buscape_id])){
            $update['buscape_id'] = update_option( parent::$buscape_id, $configData[parent::$buscape_id]);
        }

        /** Faz o update da configuração "EBIT ID" no BD Wordpress */
        if( parent::wc_qsti_empty($configData[parent::$ebit_id])){
            $update['ebit_id'] = update_option( parent::$ebit_id, $configData[parent::$ebit_id]);
        }

        /** Faz o update da configuração "Parametro" no BD Wordpress */
        if( parent::wc_qsti_empty($configData[parent::$option_name])){
            $update['parameter'] = update_option( parent::$option_name, $configData[parent::$option_name]);
        }

        /* Verifica se houve algum update com sucesso e retorna sucesso*/
        foreach($update as $key => $value){
            if($value == TRUE){
               return TRUE;
               break; 
            }
        }
        
        /** Retorna o resultado */
        return FALSE;
        
    }

    /**
     * Mostrar erros ao salvar
     * @since 0.1
     */
    function wc_qsti_save_error(){
        echo '<div class="error"><p>'.  __('Error in Saving', 'wc_qsti') .'</p></div>';
    }
}