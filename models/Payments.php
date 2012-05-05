<?php

    namespace app\models;

    use app\extensions\data\Model;

    class Payments extends Model
    {
        protected $_schema = array(
            # These are internal fields
            '_id'  => array('type' => 'id'), // required for Mongo
            'shopping_cart_id' => array('type' => 'id'),
            # The rest of these are fields from paypal's IPN
            'invoice' => array('type' => 'string'),
            'mc_gross' => array('type' => 'float'),
            'auth_exp' => array('type' => 'string'),
            'protection_eligibility' => array('type' => 'string'),
            'payer_id' => array('type' => 'string'),
            'tax' => array('type' => 'float'),
            'payment_date' => array('type' => 'date'),
            'payment_status' => array('type' => 'string'),
            'charset' => array('type' => 'string'),
            'first_name' => array('type' => 'string'),
            'transaction_entity' => array('type' => 'string'),
            'mc_fee' => array('type' => 'float'),
            'notify_version' => array('type' => 'string'),
            'custom' => array('type' => 'string'),
            'payer_status' => array('type' => 'string'),
            'business' => array('type' => 'string'),
            'quantity' => array('type' => 'integer'),
            'verify_sign' => array('type' => 'string'),
            'payer_email' => array('type' => 'string'),
            'parent_txn_id' => array('type' => 'string'),
            'txn_id' => array('type' => 'string'),
            'payment_type' => array('type' => 'string'),
            'remaining_settle' => array('type' => 'float'),
            'auth_id' => array('type' => 'string'),
            'last_name' => array('type' => 'string'),
            'receiver_email' => array('type' => 'string'),
            'auth_amount' => array('type' => 'float'),
            'payment_fee' => array('type' => 'float'),
            'receiver_id' => array('type' => 'string'),
            'txn_type' => array('type' => 'string'),
            'item_name' => array('type' => 'string'),
            'mc_currency' => array('type' => 'string'),
            'item_number' => array('type' => 'string'),
            'residence_country' => array('type' => 'string'),
            'test_ipn' => array('type' => 'string'),
            'handling_amount' => array('type' => 'float'),
            'transaction_subject' => array('type' => 'string'),
            'payment_gross' => array('type' => 'float'),
            'auth_status' => array('type' => 'string'),
            'shipping' => array('type' => 'float'),
            'ipn_track_id' => array('type' => 'string'),
            'mc_shipping' => array('type' => 'float'),
            'mc_handling' => array('type' => 'float'),
            'num_cart_items' => array('type' => 'integer'),
            'pending_reason' => array('type' => 'string'),
            'auth_status' => array('type' => 'string')

        );
    }