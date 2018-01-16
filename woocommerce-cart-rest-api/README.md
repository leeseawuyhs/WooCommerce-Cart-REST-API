# ISOVN - WooCommerce Cart REST-API

Feature plugin providing additional REST-API endpoints for WooCommerce to enable the ability to add items, view items, update items and delete items from the cart.

## Requirements

### Woocommerce plugin

This plugin was extend the [Woocommerce](https://wordpress.org/plugins/woocommerce/) 

## Namespace and Endpoints

When the plugin is activated, a new namespace is added.


```
wp-json/woo-rest/v1/cart
```
Also, two new endpoints are added to this namespace.


Endpoint | HTTP Verb

This is how I see the endpoints.

    View Cart - /woo-rest/v1/cart | GET
    Clear Cart - /woo-rest/v1/cart/clear | POST
    Update Cart - /woo-rest/v1/cart/update/(?P<cart_item_key>[0-9a-z\-_]+) | POST
    Remove item from Cart - /woo-rest/v1/cart/remove-cart-item/(?P<cart_item_key>[0-9a-z\-_]+) | POST
    Restore Item from Cart - /woo-rest/v1/cart/restore-cart-item/(?P<cart_item_key>[0-9a-z\-_]+) | POST
    Update Item in Cart - /woo-rest/v1/cart/update | POST
    Add Item to Cart - /woo-rest/v1/cart/add | POST

## Usage
### wp-json/woo-rest/v1/cart

This is request get cart

Success response from the server:

```json
{
    "99c5e07b4d5de9d18c350cdf64c5aa3d": {
        "key": "99c5e07b4d5de9d18c350cdf64c5aa3d",
        "product_id": 567,
        "variation_id": 0,
        "variation": [],
        "quantity": 2,
        "line_tax_data": {
            "subtotal": [],
            "total": []
        },
        "line_subtotal": 150,
        "line_subtotal_tax": 0,
        "line_total": 150,
        "line_tax": 0,
        "data": {}
    }
}
```
Cart empty response from the server:

```json
[]
```

### wp-json/woo-rest/v1/cart/add

This is POST method add cart

Success response from the server:

```json
{
    "99c5e07b4d5de9d18c350cdf64c5aa3d": {
        "key": "99c5e07b4d5de9d18c350cdf64c5aa3d",
        "product_id": 567,
        "variation_id": 0,
        "variation": [],
        "quantity": 2,
        "line_tax_data": {
            "subtotal": [],
            "total": []
        },
        "line_subtotal": 150,
        "line_subtotal_tax": 0,
        "line_total": 150,
        "line_tax": 0,
        "data": {}
    }
}
```
Error response from the server:

Required value:
```json
{
    "code": "cant-not-add",
    "message": {
        "product_id": "product_id id is required",
        "quantity": "quantity is required"
    },
    "data": {
        "status": 500
    }
}
```
Invalid value:

```json
{
    "code": "cant-not-add",
    "message": {
        "quantity": "quantity require numberic"
    },
    "data": {
        "status": 500
    }
}
```


### /woo-rest/v1/cart/update/(?P<cart_item_key>[0-9a-z\-_]+)

This is POST method update quantity on cart
Required value:
```json
{
    "code": "cant-not-update",
    "message": {
        "cart_item_key": "cart_item_key is required",
        "quantity": "quantity is required"
    },
    "data": {
        "status": 500
    }
}
```
Success response from the server:

```json
{
    "99c5e07b4d5de9d18c350cdf64c5aa3d": {
        "key": "99c5e07b4d5de9d18c350cdf64c5aa3d",
        "product_id": 567,
        "variation_id": 0,
        "variation": [],
        "quantity": 2,
        "line_tax_data": {
            "subtotal": [],
            "total": []
        },
        "line_subtotal": 150,
        "line_subtotal_tax": 0,
        "line_total": 150,
        "line_tax": 0,
        "data": {}
    }
}
```

Error response from the server:

Required value:
```json
{
    "code": "cant-not-update",
    "message": {
        "cart_item_key": "cart_item_key id is required",
        "quantity": "quantity is required"
    },
    "data": {
        "status": 500
    }
}
```

Example call update cart:
```
cart_item_key = 99c5e07b4d5de9d18c350cdf64c5aa3d
quantity = 2
```
Request
```
/woo-rest/v1/cart/update/99c5e07b4d5de9d18c350cdf64c5aa3d?quantity=2
```
Success response from the server:

```json
{
    "99c5e07b4d5de9d18c350cdf64c5aa3d": {
        "key": "99c5e07b4d5de9d18c350cdf64c5aa3d",
        "product_id": 567,
        "variation_id": 0,
        "variation": [],
        "quantity": 2,
        "line_tax_data": {
            "subtotal": [],
            "total": []
        },
        "line_subtotal": 150,
        "line_subtotal_tax": 0,
        "line_total": 150,
        "line_tax": 0,
        "data": {}
    }
}
```