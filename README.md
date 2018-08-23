=== Woocommerce Display Ebit Banner  ===
Contributors: iranalves85
Tags: ebit, plugin ebit, banner ebit, avaliação ebit, avaliação de pedido, selo ebit, selo de avaliação, pagseguro integração ebit, integração ebit, mostrar ebit, reputação ebit, pedido selo, pedido avaliação, woocommerce extensão, woocommerce extension, extensão, plugin extensão.
Donate link: https://goo.gl/dN6U3T
Requires at least: 3.9.23
Tested up to: 4.9.4
Requires PHP: 5.6 >=
Requires Woocommerce: 2.2 >=
Stable Tag: 0.1
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Plugin que exibe banner ou selo Ebit com a utilização de shortcodes. Ebit é a maior plataforma de avaliação de lojas virtuais do Brasil. Mais informações: https://www.ebit.com.br/

== Description ==

Este plugin pode ser usado de duas formas, a primeira é inserir shortcode do plugin na página de finalização de pedido, normalmente na mesma página que contém o shortcode [woocommerce_checkout], quando da confirmação do pedido será exibido banner da Ebit. 
A segunda forma é através de uma página de redirecionamento, na qual também deve ser inserido shortcode do plugin, e informar nas configurações do plugin qual o parametro definido no gateway de pagamento (ex.: Pagseguro). 

Importante: Testado primariamente com a versão 7.2 do PHP

*   Shortcode para exibir banner Ebit para avaliação de lojas.
*   Shortcode especifico para exibir selo obrigatório Ebit no rodapé ou em outro da loja.
*   Opção de habilitar ou não, Lightbox Ebit (Para efeito de maior conversão).


== Installation ==

* Instale e ative o plugin "Woocommerce Display Ebit Banner".
* Na aba "Produtos" nas configurações do Woocommerce, insira os dados obtidos ao se cadastrar na plataforma do Ebit, o campo principal obrigatório são "ID Ebit" e "Parametro de Transação", no caso de configurar uma página de retorno na finalização (vide Pagseguro).
* Copie shortcode [wc_qsti_banner_ebit] e cole na página designada.
* Copie shortcode [wc_qsti_selo_ebit] e cole em local visivel na loja (recomendado em widget no rodapé)

== Frequently Asked Questions ==

= Eu encontrei um erro, como relatar ao desenvolvedor? =
Se encontrar erros no plugin, adicione um 'issues' no repositório Github: https://github.com/iranalves85/woocommerce-display-ebit-banner. Ou pode em enviar um email em iranjosealves@gmail.com, por favor coloque o assunto como "Woocommerce Display Ebit Banner". 
= Eu gostei do plugin! Como posso ajudar o desenvolvedor? =
Se você achou que o plugin lhe ajudou de alguma forma e gostaria de me pagar um café, acesse esse link (https://goo.gl/dN6U3T) ou avalie o plugin no diretórios de plugin Wordpress, muito obrigado. WordPress is love!

== Screenshots ==

1. Página de configuração do plugin dentro do Woocommerce.

== Changelog ==

= 0.1 =
* Criado

== Translations ==

* Português: Default!


== Credits ==
* Obrigado a comunidade e a equipe Wordpress por essa pltaforma maravilhosa!