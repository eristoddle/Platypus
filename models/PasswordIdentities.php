<?php

    namespace app\models;

    use app\extensions\data\Model;
    use lithium\security\Password;
    use lithium\analysis\Logger;

    class PasswordIdentities extends Identities
    {
        protected $_meta = array(
            'source' => 'identities'
        );

        protected $_schema = array(
            '_id'     => array('type' => 'id'), // required for Mongo
            'user_id' => array('type' => 'MongoId', 'null' => false),
            'type'    => array('type' => 'string', 'null' => false, 'default' => 'password'),

            'prv_name'   => array('type' => 'string', 'default' => 'afdc.com'),
            'prv_secret' => array('type' => 'string'),
            'prv_uid'    => array('type' => 'string'),
            'prv_data'   => array('type' => 'object')
        );

        public static function __init()
        {
            parent::__init();
            
            self::applyFilter('validates', function($self, $params, $chain) {
                $validates = true;

                if (isset($params['entity']->confirm_password)) {
                    $plaintext_password = $params['entity']->confirm_password;
                    unset($params['entity']->confirm_password);
                    
                   // Check matching
                    if (!Password::check($plaintext_password, $params['entity']->prv_secret)) {
                        $validates = false;
                        $params['entity']->errors('password', 'Passwords do not match.');
                        $params['entity']->errors('confirm_password', 'Passwords do not match.');
                    }

                    // Check length
                    if (strlen($plaintext_password) < 6) {
                        $validates = false;
                        $params['entity']->errors('password', array('Passwords must be six characters long.'));
                    }
                }

                $chainValid = $chain->next($self, $params, $chain);
                return $validates and $chainValid;
            });
            self::applyFilter('save', function($self, $params, $chain) {
                if (isset($params['data']['password'])) {
                    $params['data']['prv_secret'] = Password::hash($params['data']['password']);
                    unset($params['data']['password']);
                }

                if (isset($params['data']['prv_uid'])) {
                    $params['data']['prv_uid'] = strtolower($params['data']['prv_uid']);
                }
                
                return $chain->next($self, $params, $chain);
            });
        }

        public function generatePassword($entity)
        {
            $newPassword = substr(md5(rand().rand()), 0, 8);

            $entity->prv_secret = Password::hash($newPassword);

            Logger::debug("New Password for " . $entity->prv_uid . ": {$newPassword} (hash: {$entity->prv_secret})");

            return $newPassword;
        }
    }