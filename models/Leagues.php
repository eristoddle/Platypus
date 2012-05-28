<?php

    namespace app\models;

    use app\extensions\data\Model;
    
    class Leagues extends Model
    {
        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'age_division' => array('type' => 'string'),
            'end_date' => array('type' => 'date'),
            'name' => array('type' => 'string'),
            'price' => array('type' => 'float'),
            'registration_close' => array('type' => 'date'),
            'registration_open' => array('type' => 'date'),
            'season' => array('type' => 'string'),
            'sport' => array('type' => 'string'),
            'start_date' => array('type' => 'date'),
            'player_limit' => array('type' => 'object'),
                'player_limit.male' => array('type' => 'integer'),
                'player_limit.female' => array('type' => 'integer'),
            'commissioner_ids' => array('type' => 'object', 'array' => true)
        );

        public $validates = array(
            'age_division' => array(
                array('inList', 'message' => 'Age division must be either adult or juniors.', 'list' => array('adult', 'juniors'))
            ),
            'name' => 'League name must not be empty.',
            'price' => 'Price must not be empty.',
            'season' => array(
                array('inList', 'message' => 'Season must be Fall, Winter, Spring, Summer, or Saturday', 'list' => array('fall', 'winter', 'spring', 'summer', 'saturday'))
            ),
            'sport' => array(
                array('inList', 'message' => 'Sport must be Ultimate or Goaltimate', 'list' => array('goaltimate', 'ultimate'))
            ),
            'player_limit.male' => 'You must enter a limit for men.',
            'player_limit.female' => 'You must enter a limit for women.'
        );        

        public static function __init()
        {
            parent::__init();
            
            self::applyFilter('save', function($self, $params, $chain) {
                if (isset($params['data']['registration_open'])) {
                    $oldDate = $params['data']['registration_open'];
                    $params['data']['registration_open'] = mktime(12, 0, 0, date('n', $oldDate), date('j', $oldDate), date('Y', $oldDate));
                }

                if (isset($params['data']['registration_close'])) {
                    $oldDate = $params['data']['registration_close'];
                    $params['data']['registration_close'] = mktime(23, 59, 59, date('n', $oldDate), date('j', $oldDate), date('Y', $oldDate));
                }

                return $chain->next($self, $params, $chain);
            });
        }

        public function getCommissioners($entity) {
            if (empty($entity->commissioner_ids)) { return array(); }

            if (is_null($entity->tempDataGet('commissioners'))) {

                $cids = $entity->commissioner_ids->export();

                $commishArray = array();
                $conditions = array('_id' => array('$in' => $cids['data']));
                $commishes = Users::find('all', compact('conditions'));

                foreach ($commishes as $c) {
                    $commishArray[] = $c;
                }

                $entity->tempDataSet('commissioners', $commishArray);
            }

            return $entity->tempDataGet('commissioners');
        }

        public function registrationOpen($entity) {
            if (!isset($entity->registration_open) or !isset($entity->registration_close)) {
                return false;
            }

            if (!($entity->registration_open instanceof \MongoDate) or !($entity->registration_close instanceof \MongoDate)) {
                var_dump($entity); die;
                throw new \Exception('Registration open and close are of an invalid type.');
            }

            $now   = time();
            $open  = $entity->registration_open->sec;
            $close = $entity->registration_close->sec;

            return($now >= $open and $now <= $close);
        }

        public function getUserRegistration($entity, $user)
        {
            if (!isset($user->_id) or !isset($entity->_id)) {
                return null;
            }

            $conditions = array(
                'league_id' => $entity->_id,
                'user_id'   => $user->_id
            );

            return Registrations::first(compact('conditions'));
        }

        public function getTeams($entity)
        {
            if (!$entity->exists()) {
                return null;
            }

            $conditions = array('league_id' => $entity->_id);
            $order = array('stats.rank' => 1);
            $list = Teams::all(compact('conditions', 'order'));
            return $list;
        }

        public function isManager($entity, $user)
        {
            if (!isset($user->_id)) {
                return null;
            }

            if ($user->can('leagues.manage')) {
                return true;
            }

            foreach ($entity->commissioner_ids as $cid) {
                if ($cid == $user->_id) {
                    return true;
                }
            }

            return false;
        }

    }