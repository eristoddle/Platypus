<?php

    namespace app\models;

    use app\extensions\data\Model;

    use lithium\analysis\Logger;

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
            'phone' => array('type' => 'object', 'null' => true),
            'postal_code' => array('type' => 'string', 'null' => true),
            'privacy' => array('type' => 'object', 'null' => true),
            'state' => array('type' => 'string', 'null' => true),
            'teams' => array('type' => 'id', 'array' => true, 'null' => true),
            'weight'  => array('type' => 'integer', 'null' => true),
            'permission_groups' => array('type' => 'string', 'array' => true)
        );

        public $validates = array(
            'email_address' => array(
                array('email', 'message' => 'Email Address is not valid')
            ),
            'gender' => array(
                array('inList', 'list' => array('male', 'female'), 'message' => 'You must select a gender.')
            ),
            'height' => array(
                array('inRange', 'upper' => 96, 'lower' =>  36, 'message' => 'Please enter your height in inches.', 'required' => false)
            ),
            'weight' => array(
                array('inRange', 'upper' => 400, 'lower' => 75, 'message' => 'Please enter your weight in pounds.', 'required' => false)
            ),
            'handedness' => array(
                array('inList', 'list' => array('left', 'right', 'both'), 'message' => 'Please select your dominant hand.', 'required' => false)
            ),
            'firstname' => 'Please enter a first name.',
            'lastname'  => 'Please enter a last name.'
        );

        public static function __init()
        {
            parent::__init();

            self::applyFilter('set', function($self, $params, $chain) {
                if (isset($params['data']['email_address'])) {
                    $newEmail = strtolower($params['data']['email_address']);
                    $params['data']['email_address'] = $newEmail;
                    #die;
                }

                return $chain->next($self, $params, $chain);
            });

            self::applyFilter('validates', function($self, $params, $chain) {
                $chainValid = $chain->next($self, $params, $chain);
                $valid = true;

                $conditions = array('email_address' => $params['entity']->email_address);

                if ($params['entity']->exists()) {
                    $conditions['_id'] = array('$ne' => $params['entity']->_id);
                }
                
                $othersWithSameEmail = $self::count($conditions);

                if ($othersWithSameEmail > 0) {
                    $valid = false;
                    $params['entity']->errors('email_address', 'This address is already taken.');
                }
                
                return $valid and $chainValid;
            });
        }

        public function can($entity, $action)
        {
            return \app\util\Permissions::check($action, $entity);
        }

        public function getShoppingCart($entity, $createIfMissing = false)
        {
            $cart = ShoppingCarts::first(array('conditions' => array('user_id' => $entity->_id, 'status' => 'open')));
            if (!isset($cart) and $createIfMissing) {
                $cart = ShoppingCarts::create();
                $cart->user_id = $entity->_id;
                $cart->status = 'open';
                $cart->save();
            }
            return $cart;
        }

        public function getIdentity($entity, $provider, $type)
        {
            if (!$entity->exists()) {
                return null;
            }

            return Identities::first(array('conditions' => array('user_id' => $entity->_id, 'prv_name' => $provider, 'type' => $type)));
        }

        public function getTeams($entity)
        {
            if (!$entity->exists()) {
                return null;
            }

            $conditions = array('players' => $entity->_id);
            $order = array('league_id' => 1);
            $list = Teams::all(compact('conditions', 'order'));
            return $list;            
        }
    }