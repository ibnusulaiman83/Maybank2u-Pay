<?php

/**
 * Plugin Name: WZ M2UPay for WooCommerce
 * Plugin URI: https://wordpress.org/plugins-wp/wz-m2upay-for-woocommerce/
 * Description: Maybank2u Pay Payment Gateway | Accept Payment using Maybank2u
 * Author: Wanzul Hosting Enterprise
 * Author URI: http://www.wanzul-hosting.com/
 * Version: 1.00
 * License: GPLv3
 * Text Domain: wcm2upay
 * Domain Path: /languages/
 */
/*
 *  Add settings link on plugin page
 */
function m2upay_for_woocommerce_plugin_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=m2upay">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'm2upay_for_woocommerce_plugin_settings_link');

function wcm2upay_woocommerce_fallback_notice()
{
    $message = '<div class="error">';
    $message .= '<p>' . __('WooCommerce M2upay Gateway depends on the last version of <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to work!', 'wcm2upay') . '</p>';
    $message .= '</div>';
    echo $message;
}
// Load the function
add_action('plugins_loaded', 'wcm2upay_gateway_load', 0);

/**
 * Load M2upay gateway plugin function
 * 
 * @return mixed
 */
function wcm2upay_gateway_load()
{
    if (!class_exists('WC_Payment_Gateway')) {
        add_action('admin_notices', 'wcm2upay_woocommerce_fallback_notice');
        return;
    }
    // Load language
    load_plugin_textdomain('wcm2upay', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    add_filter('woocommerce_payment_gateways', 'wcm2upay_add_gateway');

    /**
     * Add M2upay gateway to ensure WooCommerce can load it
     * 
     * @param array $methods
     * @return array
     */
    function wcm2upay_add_gateway($methods)
    {
        $methods[] = 'WC_M2upay_Gateway';
        return $methods;
    }

    /**
     * Define the M2upay gateway
     * 
     */
    class WC_M2upay_Gateway extends WC_Payment_Gateway
    {

        /** @var bool Whether or not logging is enabled */
        public static $log_enabled = false;

        /** @var WC_Logger Logger instance */
        public static $log = false;
        private $clearcart;
        public $custom_error;
        public $title;
        public $description;
        public $endpoint;
        private $signature;

        /**
         * Construct the M2upay gateway class
         * 
         * @global mixed $woocommerce
         */
        public function __construct()
        {
            //global $woocommerce;

            $this->id = 'm2upay';
            $this->has_fields = false;
            $this->method_title = __('M2upay', 'wcm2upay');
            $this->debug = 'yes' === $this->get_option('debug', 'no');
            $this->order_button_text = __('Pay with Maybank2u', 'woocommerce');

            // Load the form fields.
            $this->init_form_fields();
            // Load the settings.
            $this->init_settings();

            // Define user setting variables.
            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->clearcart = $this->settings['clearcart'];
            $this->endpoint = $this->settings['endpoint'];
            $this->signature = $this->settings['signature'];
            $this->custom_error = $this->settings['custom_error'];

            // Payment instruction after payment
            $this->instructions = isset($this->settings['instructions']) ? $this->settings['instructions'] : '';

            add_action('woocommerce_thankyou_m2upay', array($this, 'thankyou_page'));
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);

            self::$log_enabled = $this->debug;

            add_action('woocommerce_receipt_m2upay', array(
                &$this,
                'receipt_page'
            ));
            // Save setting configuration
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(
                $this,
                'process_admin_options'
            ));
            // Payment listener/API hook
            add_action('woocommerce_api_wc_m2upay_gateway', array(
                $this,
                'check_ipn_response'
            ));
            // Checking if api_key is not empty.
            $this->endpoint == '' ? add_action('admin_notices', array(
                        &$this,
                        'endpoint_missing_message'
                    )) : '';
            // Checking if x_signature is not empty.
            $this->signature == '' ? add_action('admin_notices', array(
                        &$this,
                        'signature_missing_message'
                    )) : '';
        }

        /**
         * Checking if this gateway is enabled and available in the user's country.
         *
         * @return bool
         */
        public function is_valid_for_use()
        {
            if (!in_array(get_woocommerce_currency(), array(
                    'MYR'
                ))) {
                return false;
            }
            return true;
        }

        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis.
         *
         */
        public function admin_options()
        {

            ?>
            <h3><?php
                _e('M2upay Payment Gateway', 'wcm2upay');

                ?></h3>
            <p><?php
                _e('M2upay Payment Gateway works by sending the user to M2upay for payment. ', 'wcm2upay');

                ?></p>
            <p><?php
                _e('To immediately reduce stock on add to cart, we strongly recommend you to use this plugin. ', 'wcm2upay');

                ?><a href="http://bit.ly/1UDOQKi" target="_blank"><?php
                    _e('WooCommerce Cart Stock Reducer', 'wcm2upay');

                    ?></a></p>
            <table class="form-table">
                <?php
                $this->generate_settings_html();

                ?>
            </table><!--/.form-table-->
            <?php
        }

        /**
         * Gateway Settings Form Fields.
         * 
         */
        public function init_form_fields()
        {
            $this->form_fields = include(__DIR__ . '/includes/settings-m2upay.php');
        }

        /**
         * 
         * @return array string
         */
        private static function get_order_data($order)
        {
            global $woocommerce;
            if (version_compare($woocommerce->version, '3.0', "<")) {
                $data = array(
                    'first_name' => !empty($order->billing_first_name) ? $order->billing_first_name : $order->shipping_first_name,
                    'last_name' => !empty($order->billing_last_name) ? $order->billing_last_name : $order->shipping_last_name,
                    'email' => $order->billing_email,
                    'phone' => $order->billing_phone,
                    'total' => $order->order_total,
                    'id' => $order->id,
                );
            } else {
                $data = array(
                    'first_name' => !empty($order->get_billing_first_name()) ? $order->get_billing_first_name() : $order->get_shipping_first_name(),
                    'last_name' => !empty($order->get_billing_last_name()) ? $order->get_billing_last_name() : $order->get_shipping_last_name(),
                    'email' => $order->get_billing_email(),
                    'phone' => $order->get_billing_phone(),
                    'total' => $order->get_total(),
                    'id' => $order->get_id(),
                );
            }

            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];

            /*
             * Compatibility with some themes
             */
            $data['email'] = !empty($data['email']) ? $data['email'] : $order->get_meta('_shipping_email');
            $data['email'] = !empty($data['email']) ? $data['email'] : $order->get_meta('shipping_email');
            $data['phone'] = !empty($data['phone']) ? $data['phone'] : $order->get_meta('_shipping_phone');
            $data['phone'] = !empty($data['phone']) ? $data['phone'] : $order->get_meta('shipping_phone');

            return $data;
        }

        /**
         * Create bills function
         * Save to database
         * 
         * @return string Return URL
         */
        protected function create_bill($order, $order_data)
        {
            require_once(__DIR__ . '/includes/curl-m2upay.php');

            /**
             * Generate Description for Bills
             */
            if (sizeof($order->get_items()) > 0)
                foreach ($order->get_items() as $item)
                    if ($item['qty'])
                        $item_names[] = $item['name'] . ' x ' . $item['qty'];
            $desc = sprintf(__('Order %s', 'woocommerce'), $order->get_order_number()) . " - " . implode(', ', $item_names);

            $m2upay = new M2UPay_Curl($this->endpoint);

            /*
             * Prepare Hash
             */
            $str = $order_data['id'] . $order_data['total'] . $order_data['name'] . $order_data['email'] . $order_data['phone'] . home_url('/?wc-api=WC_M2upay_Gateway') . home_url('/?wc-api=WC_M2upay_Gateway') . 'json';
            $validation = hash_hmac('sha256', $str, $this->signature);

            $prepare_data = array(
                'info' => $order_data['id'],
                'amount' => $order_data['total'],
                'name' => $order_data['name'],
                'email' => $order_data['email'],
                'phone' => $order_data['phone'],
                'redirect' => home_url('/?wc-api=WC_M2upay_Gateway'),
                'callback' => home_url('/?wc-api=WC_M2upay_Gateway'),
                'output_type' => 'json',
                'validation' => $validation,
            );

            $url_raw = $m2upay->get_url($prepare_data);

            $url = $url_raw['url'];
            $id = $url_raw['id'];

            // Log the bills creation
            self::log('Creating bills ' . $id . ' for order number #' . $order_data['id']);
            return array(
                'url' => $url,
                'id' => $id,
            );
        }

        /**
         * Logging method.
         * @param string $message
         */
        public static function log($message)
        {
            if (self::$log_enabled) {
                if (empty(self::$log)) {
                    self::$log = new WC_Logger();
                }
                self::$log->add('m2upay', $message);
            }
        }

        /**
         * Order error button.
         *
         * @param  object $order Order data.
         * @return string Error message and cancel button.
         */
        protected function m2upay_order_error($order)
        {
            $html = '<p>' . __('An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'wcm2upay') . '</p>';
            $html .= '<a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Click to try again', 'wcm2upay') . '</a>';
            return $html;
        }

        /**
         * Process the payment and return the result.
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id)
        {
            global $woocommerce;
            if ($this->clearcart === 'yes')
                $woocommerce->cart->empty_cart();
            /*
             * If don't want to use global:
             * WC()->cart->empty_cart();
             */

            $order = new WC_Order($order_id);
            $order_data = self::get_order_data($order);
            $bills = $this->create_bill($order, $order_data);

            return array(
                'result' => 'success',
                'redirect' => $bills['url']
            );
        }

        /**
         * Check for M2UPay Response
         *
         * @access public
         * @return void
         */
        function check_ipn_response()
        {
            @ob_clean();
            //global $woocommerce;
            
            if (isset($_POST['hash_validation'])) {
                $signal = 'Return';
                $order_info = $_POST['order_info'];
                $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';
                $payment_amount = isset($_POST['payment_amount']) ? $_POST['payment_amount'] : '';
                $maybank_refid = isset($_POST['maybank_refid']) ? $_POST['maybank_refid'] : '';
                $maybank_trndatetime = isset($_POST['maybank_trndatetime']) ? $_POST['maybank_trndatetime'] : '';
                $hash_validation = isset($_POST['hash_validation']) ? $_POST['hash_validation'] : '';
                $str = $order_info . $payment_status . $payment_amount . $maybank_refid . $maybank_trndatetime;
                $new_validation = hash_hmac('sha256', $str, $this->signature);
            } else if (isset($_POST['validation'])) {
                $signal = 'Callback';
                $dataMsg = $_POST;
                $order_info = $dataMsg['OrderInfo'];
                $payment_status = $dataMsg['payment_status'];
                $str = '';
                foreach ($dataMsg as $key => $value) {
                    if ($key !== 'validation') {
                        $str .= $value;
                    }
                }
                
                $hash_validation = $dataMsg['validation'];
                $new_validation = hash_hmac('sha256', $str, $this->signature);
            } else {
                exit;
            }

            if ($hash_validation !== $new_validation) {
                exit('Validation Failed');
            }
            $order = new WC_Order($order_info);
            if ($payment_status === 'true') {
                $this->save_payment($order, $signal);
                $redirectpath = $order->get_checkout_order_received_url();
            } else {
                wc_add_notice(__('ERROR: ', 'woothemes') . $this->custom_error, 'error');
                $redirectpath = $order->get_cancel_order_url();
            }

            if ($signal === 'Return') {
                wp_redirect($redirectpath);
            } else {
                exit('OK');
            }
        }

        /**
         * Save payment status to DB for successful return/callback
         */
        private function save_payment($order, $type)
        {
            $referer .= "Type: " . $type;
            $order->add_order_note($referer);
            $order->payment_complete();
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page()
        {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false)
        {

            if ($this->instructions && !$sent_to_admin && 'offline' === $order->get_payment_method() && $order->has_status('on-hold')) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }

        /**
         * Adds error message when not configured the endpoint url.
         * 
         */
        public function endpoint_missing_message()
        {
            $message = '<div class="error">';
            $message .= '<p>' . sprintf(__('<strong>Gateway Disabled</strong> You should inform your Endpoint URL in M2upay. %sClick here to configure!%s', 'wcm2upay'), '<a href="' . get_admin_url() . 'admin.php?page=wc-settings&tab=checkout&section=m2upay">', '</a>') . '</p>';
            $message .= '</div>';
            echo $message;
        }

        /**
         * Adds error message when not configured the app_secret.
         * 
         */
        public function signature_missing_message()
        {
            $message = '<div class="error">';
            $message .= '<p>' . sprintf(__('<strong>Gateway Disabled</strong> You should inform your Signature Key in M2upay. %sClick here to configure!%s', 'wcm2upay'), '<a href="' . get_admin_url() . 'admin.php?page=wc-settings&tab=checkout&section=m2upay">', '</a>') . '</p>';
            $message .= '</div>';
            echo $message;
        }
    }

}
