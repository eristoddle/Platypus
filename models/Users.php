<?php

    namespace app\models;

    use lithium\data\Model;

    class Users extends Model
    {
        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'address' => array('type' => 'string', 'null' => true),
            'birthdate' => array('type' => 'string', 'null' => true),
            'city' => array('type' => 'string', 'null' => true),
            'email_address' => array('type' => 'string', 'null' => true),
            'firstname' => array('type' => 'string', 'null' => true),
            'gender' => array('type' => 'string', 'null' => true),
            'handedness' => array('type' => 'string', 'null' => true),
            'height' => array('type' => 'integer', 'null' => true),
            'lastname' => array('type' => 'string', 'null' => true),
            'occupation' => array('type' => 'string', 'null' => true),
            'phone' => array('type' => 'subdocument', 'null' => true),
            'postal_code' => array('type' => 'string', 'null' => true),
            'privacy' => array('type' => 'subdocument', 'null' => true),
            'state' => array('type' => 'string', 'null' => true),
            'teams' => array('type' => 'array', 'null' => true),
            'weight'  => array('type' => 'integer', 'null' => true)
        );
    }