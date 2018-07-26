<?php 
/**
 * Classe com as funções para adminis
 * @since 0.1
 */
class WoocommerceQSTIAdmin extends WoocommerceDisplayEbitBanner
{
    /**
     * Função requerimentos para inicializar o plugin
     * @since 0.1
    */
    function wc_qsti_require_woocommerce_plugin(){
        //var_dump($this::wc_qsti_load_order_query());
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
                'id'       => 'wc_qsti_pagseguro_parameter',
                'type'     => 'text',
                'css'      => 'max-width: 200px;',
                'desc'     => __( 'Insira o parametro definido em sua conta.', 'wc_qsti' )
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
}