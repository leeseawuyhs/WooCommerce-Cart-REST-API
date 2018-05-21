<?php

/*
 * Plugin Name: ISOVN - WooCommerce Cart REST-API
 * Plugin URI: http://isovn.net
 * Description: Plugin providing additional REST-API endpoints for WooCommerce to enable the ability to add items, view items, update items and delete items from the cart. Required active Woocommerce plugin
 * Author: vinhle
 * Author URI: http://isovn.net
 * Version: 1.0
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wcri-cart
 */

/**
 * Check WooCommerce Active
 * WooCommerce Cart REST-API : WCRI_Cart
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

add_action('admin_notices', 'WCRI_Cart_plugin_admin_notices');

function WCRI_Cart_plugin_admin_notices() {
    $woo_plugin_name = "woocommerce/woocommerce.php";
    if (!is_plugin_active($woo_plugin_name)) {
        echo __('<div class="updated">This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/" targete="_blank">WooCommerce</a> plugins to be active!</div>', 'wcri-cart');

        deactivate_plugins('woocommerce-cart-rest-api/woocommerce-cart-rest-api.php');
        return;
    }
}

WCRI_Cart_init();

function WCRI_Cart_init() {

    add_action('rest_api_init', function () {
        $version = '1';
        $namespace = 'woo-rest/' . 'v' . $version;
        //    View Cart - /v1/cart
        register_rest_route($namespace, '/cart', array(
            'methods' => 'GET',
            'callback' => 'WCRI_Cart',
        ));
        //    Add Item to Cart - /v1/cart/add
        register_rest_route($namespace, '/cart/add', array(
            'methods' => 'POST',
            'callback' => 'WCRI_Cart_add',
            'args' => array(
                'product_id' => array(
                    'validate_callback' => 'is_numeric'
                ),
                'quantity' => array(
                    'validate_callback' => 'is_numeric'
                ),
            ),
        ));
        //    Update Cart - /v1/cart/update
        register_rest_route($namespace, '/cart/update/(?P<cart_item_key>[0-9a-z\-_]+)', array(
            'methods' => 'POST',
            'callback' => 'WCRI_Cart_update',
            'args' => array(
                'cart_item_key' => array(
                    'default' => null
                ),
                'quantity' => array(
                    'default' => null,
                    'validate_callback' => 'is_numeric'
                ),
            ),
        ));
//        Remove Item from Cart - /v1/cart/remove-cart-item/%cart_item_key%
        register_rest_route($namespace, '/cart/remove-cart-item/(?P<cart_item_key>[0-9a-z\-_]+)', array(
            'methods' => 'POST',
            'callback' => 'WCRI_Cart_remove_cart_item',
            'args' => array(
                'cart_item_key' => array(
                    'default' => null
                )
            ),
        ));
        //        Restore Item from Cart - /v1/cart/restore-cart-item/%cart_item_key%
        register_rest_route($namespace, '/cart/restore-cart-item/(?P<cart_item_key>[0-9a-z\-_]+)', array(
            'methods' => 'POST',
            'callback' => 'WCRI_Cart_restore_cart_item',
            'args' => array(
                'cart_item_key' => array(
                    'default' => null
                )
            ),
        ));
        //    Clear Cart - /v1/cart/clear
        register_rest_route($namespace, '/cart/clear', array(
            'methods' => 'POST',
            'callback' => 'WCRI_Cart_clear',
        ));
    });
}

//    View Cart - /v1/cart
function WCRI_Cart() {
    return WC()->cart->get_cart();
}

//    Add Item to Cart - /v1/cart/add
function WCRI_Cart_add($data = array()) {
    $error = WCRI_Cart_validate_data($data);
    if (empty($error)) {
        $item_key = WC()->cart->add_to_cart($data['product_id'], $data['quantity']);
        if ($item_key) {
            $data = WC()->cart->get_cart_item($item_key);
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error('cant-not-add', __("Error add to cart"), array('status' => 500));
        }
    } else {
        return new WP_Error('cant-not-add', __($error), array('status' => 500));
    }
}

//    Add Item to Cart - /v1/cart/update
function WCRI_Cart_update($data = array()) {
    $error = WCRI_Cart_validate_update_cart($data);
    if (empty($error)) {
        $cart_item_key = $data['cart_item_key'];
        if (WC()->cart->set_quantity($cart_item_key, $data['quantity'])) {
            $data = WC()->cart->get_cart_item($cart_item_key);
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error('cant-not-update', __("Error update to cart"), array('status' => 500));
        }
    } else {
        return new WP_Error('cant-not-update', $error, array('status' => 500));
    }
}

//    Add Item to Cart - /v1/cart/remove-cart-item
function WCRI_Cart_remove_cart_item($data = array()) {
    $error = array();
    if (!isset($data['cart_item_key'])) {
        $error['cart_item_key'] = 'cart_item_key is required';
    }
    if (empty($error)) {
        if (WC()->cart->remove_cart_item($data['cart_item_key'])) {
            return new WP_REST_Response(WCRI_Cart_message("Done"), 200);
        } else {
            return new WP_Error('cant-not-remove-item', __('Remove cart item fail', 'wcri-cart'), array('status' => 500));
        }
    } else {
        return new WP_Error('cant-not-remove-item', $error, array('status' => 500));
    }
}

//    Add Item to Cart - /v1/cart/restore-cart-item
function WCRI_Cart_restore_cart_item($data = array()) {
    $error = array();
    if (!isset($data['cart_item_key'])) {
        $error['cart_item_key'] = 'cart_item_key is required';
    }
    if (empty($error)) {
        if (WC()->cart->restore_cart_item($data['cart_item_key'])) {
            return new WP_REST_Response(WCRI_Cart_message("Done"), 200);
        } else {
            return new WP_Error('cant-not-remove-item', __('Restore cart item fail', 'wcri-cart'), array('status' => 500));
        }
    } else {
        return new WP_Error('cant-not-remove-item', $error, array('status' => 500));
    }
}

//    Clear Cart - /v1/cart/clear
function WCRI_Cart_clear() {
    if (WC()->cart->empty_cart() == null) {
        return new WP_REST_Response("Done", 200);
    } else {
        return new WP_Error('cant-not-clear-cart', __('Clear cart faild', 'wcri-cart'), array('status' => 500));
    }
}

/**
 * Validate request data
 * @param type $data
 * @return string
 */
function WCRI_Cart_validate_data($data = array()) {
    $error = array();
    if (!isset($data['product_id'])) {
        $error['product_id'] = 'product_id is required';
    } else if (!is_numeric($data['product_id'])) {
        $error['product_id'] = 'product_id require numberic';
    }
    if (!isset($data['quantity'])) {
        $error['quantity'] = 'Quantity is required';
    } else if (!is_numeric($data['quantity'])) {
        $error['quantity'] = 'Quantity require numberic';
    }
    return $error;
}

function WCRI_Cart_validate_update_cart($data = array()) {
    $error = array();
    if (!isset($data['cart_item_key']) && ($data['cart_item_key'] !== '')) {
        $error['cart_item_key'] = 'cart_item_key is required';
    }

    if (!isset($data['quantity'])) {
        $error['quantity'] = 'Quantity is required';
    } else if (!is_numeric($data['quantity'])) {
        $error['quantity'] = 'Quantity require numberic';
    }
    return $error;
}

/**
 * Set status
 * @param type $status
 * @return type
 */
function WCRI_Cart_message($status) {
    return array(
        "status" => $status
    );
}
