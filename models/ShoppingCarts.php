<?php

    namespace app\models;

    use app\extensions\data\Model;

    class ShoppingCarts extends Model
    {
        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'user_id' => array('type' => 'id'),
            'status' => array('type' => 'string', 'default' => 'open')
        );

        public function getItems($entity)
        {
            return CartItems::find('all', array('conditions' => array('carts' => $entity->_id)));
        }
    }