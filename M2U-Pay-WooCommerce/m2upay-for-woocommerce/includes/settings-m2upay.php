<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings for PayPal Gateway.
 */
return array(
    'enabled' => array(
        'title' => __('Enable/Disable', 'wcm2upay'),
        'type' => 'checkbox',
        'label' => __('Enable Maybank2u Pay', 'wcm2upay'),
        'default' => 'yes'
    ),
    'title' => array(
        'title' => __('Title', 'wcm2upay'),
        'type' => 'text',
        'description' => __('This controls the title which the user sees during checkout.', 'wcm2upay'),
        'default' => __('Maybank2u Pay', 'wcm2upay')
    ),
    'description' => array(
        'title' => __('Description', 'wcm2upay'),
        'type' => 'textarea',
        'description' => __('This controls the description which the user sees during checkout.', 'wcm2upay'),
        'default' => __('Pay with <strong>Maybank2u</strong>. ', 'wcm2upay')
    ),
    'endpoint' => array(
        'title' => __('Endpoint URL', 'wcm2upay'),
        'type' => 'text',
        'placeholder' => 'Example : https://somethingsomething.com/',
        'description' => __('Please enter your Maybank2u Pay Endpoint URL with thrailing slash "/". It should the same with the SYTSTEM URL', 'wcm2upay'),
        'default' => ''
    ),
    'signature' => array(
        'title' => __('Signature', 'wcm2upay'),
        'type' => 'text',
        'placeholder' => 'Example : abcd',
        'description' => __('Please enter your Signature Key. ', 'wcm2upay'),
        'default' => ''
    ),
    'clearcart' => array(
        'title' => __('Clear Cart Session', 'wcm2upay'),
        'type' => 'checkbox',
        'label' => __('Tick to clear cart session on checkout', 'wcm2upay'),
        'default' => 'no'
    ),
    'debug' => array(
        'title' => __('Debug Log', 'wcm2upay'),
        'type' => 'checkbox',
        'label' => __('Enable logging', 'wcm2upay'),
        'default' => 'no',
        'description' => sprintf(__('Log Billplz events, such as IPN requests, inside <code>%s</code>', 'wcm2upay'), wc_get_log_file_path('billplz'))
    ),
    'instructions' => array(
        'title' => __('Instructions', 'wcm2upay'),
        'type' => 'textarea',
        'description' => __('Instructions that will be added to the thank you page and emails.', 'wcm2upay'),
        'default' => '',
        'desc_tip' => true,
    ),
    'custom_error' => array(
        'title' => __('Error Message', 'wcm2upay'),
        'type' => 'text',
        'placeholder' => 'Example : You have cancelled the payment. Please make a payment!',
        'description' => __('Error message that will appear when customer cancel the payment.', 'wcm2upay'),
        'default' => 'You have cancelled the payment. Please make a payment!'
    )
);
