<?php

    namespace app\models;

    use app\extensions\data\Model;

    class Identities extends Model
    {
        public $validates = array();

        protected $_schema = array(
            '_id'     => array('type' => 'id'), // required for Mongo
            'user_id' => array('type' => 'MongoId', 'null' => false),
            'type'    => array('type' => 'string', 'null' => false),

            'prv_name'   => array('type' => 'string'),
            'prv_secret' => array('type' => 'string'),
            'prv_uid'    => array('type' => 'string'),
            'prv_data'   => array('type' => 'object')
        );

        public function getUser($entity)
        {
            $uid = $entity->user_id;
            return Users::first(array('conditions' => array(
                '_id' => $uid
            )));
        }
    }