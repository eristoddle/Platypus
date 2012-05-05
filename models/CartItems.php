<?php

    namespace app\models;

    use app\extensions\data\Model;

    class CartItems extends Model
    {
        // Statuses
        const STATUS_OPEN = 'open';
        const STATUS_AUTH = 'authorized';
        const STATUS_CAPT = 'capture_pending';
        const STATUS_PAID = 'paid';
        const STATUS_DONE = 'closed';


        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'name' => array('type' => 'string'),
            'description' => array('type' => 'string'),
            'price' => array('type' => 'float', 'default' => 0),
            'reference_class' => array('type' => 'string'),
            'status' => array('type' => 'string', 'default' => 'open'),
            'reference_id' => array('type' => 'id')
        );

        public function isValid($entity)
        {

            $refItem = $entity->getReference();
            if ($refItem) {
                return $refItem->isValid();
            } else {
                return false;
            }
        }

        public function getReference($entity)
        {
            switch ($entity->reference_class) {
                case 'registrations':
                    // TODO: change this to ::find($id);
                    return Registrations::find('first', array('conditions' => array('_id' => $entity->reference_id)));
                    break;
                default:
                    return null;
                    break;
            }
        }
    }