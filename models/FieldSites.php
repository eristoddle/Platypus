<?php

    namespace app\models;

    use lithium\data\Model;

    class FieldSites extends Model
    {
        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'name' => array('type' => 'string'),
            'active' => array('type' => 'boolean'),
            'map_url' => array('type' => 'string'),
            'directions' => array('type' => 'string')
        );
    }