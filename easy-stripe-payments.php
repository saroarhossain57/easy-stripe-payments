<?php
/**
 * Plugin Name: Easy Stripe Payment
 * Description: A simple solution for stripe payment in WooCommerce
 * Version: 1.0.0
 * Author: Saroar Hossain
 */


 add_action( 'plugins_loaded', 'init_your_gateway_class' );

 function init_your_gateway_class(){
    class WC_Gateway_Custom_Gateway extends WC_Payment_Gateway {
        public function __construct() {
            $this->id = 'my_custom_gateway';
            $this->icon = apply_filters( 'wc_your_gateway_icon', '' );
            $this->has_fields = false;
            $this->method_title = 'My Custom Gateway';
            $this->method_description = 'My Custom Gateway Description';
            $this->title = 'Stripe Payment Bro';
            $this->description = 'Cool Payment Tai Na?';
            $this->supports = array(
                'products',
                'subscriptions',
                'subscriptions',
                'subscription_cancellation', 
                'subscription_suspension', 
                'subscription_reactivation',
                'subscription_amount_changes',
                'subscription_date_changes',
                'subscription_payment_method_change',
                'subscription_payment_method_change_customer',
                'subscription_payment_method_change_admin',
                'multiple_subscriptions',
            );

            $this->init_form_fields();
            $this->init_settings();

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            //add_filter('woocommerce_available_payment_gateways', [$this, 'hide_payment_gateways_on_pay_for_order_page'], 100, 1);
        }

        public function init_form_fields() {


            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Enable/Disable', 'woocommerce' ),
                    'type' => 'checkbox',
                    'label' => __( 'Enable Custom Payment', 'woocommerce' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __( 'Title', 'woocommerce' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'default' => __( 'Cheque Payment', 'woocommerce' ),
                    'desc_tip'      => true,
                ),
                'description' => array(
                    'title' => __( 'Customer Message', 'woocommerce' ),
                    'type' => 'textarea',
                    'default' => ''
                )
            );


        }

        public function get_new_payment_method_option_html() {

            return 'amar sonabar';
        }


        // public function payment_fields() {
            
        //     echo '<div style = "margin-left: 30px; padding: 5px;">You\'ll be directed to the next page to complete the payment. Powered by <a href="https://tillpayments.com/">Till Payments</a></div>';
        //     echo '<div id="loader">
        //     <input type="text" placeholder="Card number" />
        //     </div>';

        // }


        public function process_payment( $order_id ) {
            global $woocommerce;
            $order = new WC_Order( $order_id );

            error_log( print_r( $order->get_checkout_payment_url() , true ) );
            

            return [
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url(),
            ];


            // wc_add_notice( __('Payment error: Ki je ekta somossa', 'woothemes'), 'error' );
            // return;

            // Mark as on-hold (we're awaiting the cheque)
            $order->update_status('completed', __( 'Order completed successfully', 'woocommerce' ));

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );
        }


        public function hide_payment_gateways_on_pay_for_order_page($available_gateways)
        {
            if (is_checkout_pay_page()) {
                global $wp;
                $order = new WC_Order($wp->query_vars['order-pay']);
                foreach ($available_gateways as $gateways_id => $gateways) {
                    if ($gateways_id !== $order->get_payment_method()) {
                        unset($available_gateways[$gateways_id]);
                    }
                }
            }

            return $available_gateways;
        }

    }
 }


 add_filter( 'woocommerce_payment_gateways', 'add_your_gateway_class' );
 function add_your_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_Custom_Gateway'; 
    return $methods;
}