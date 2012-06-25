<?php

    namespace app\models;

    use app\extensions\data\Model;

    class Teams extends Model
    {
        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'captains' => array('type' => 'id', 'array' => true),
            'draft_number' => array('type' => 'integer'),
            'league_id' => array('type' => 'id'),
            'name' => array('type' => 'string'),
            'players' => array('type' => 'id', 'array' => true),
            'stats' => array('type' => 'object')
        );

        public function getCaptains($entity)
        {
            $cids = $entity->captains->export();

            $conditions = array('_id' => array('$in' => $cids['data']));
            return Users::find('all', compact('conditions'));
        }

        public function getLeague($entity)
        {
            if (is_null($entity->tempDataGet('league'))) {
                $conditions = array('_id' => $entity->league_id);
                $entity->tempDataSet('league', Leagues::first(compact('conditions')));
            }

            return $entity->tempDataGet('league');
        }

        public function getPlayers($entity) 
        {
            $conditions = array('teams' => $entity->_id);
            $order      = array('gender' => -1, 'lastname' => 1, 'firstname' => 1);

            return Users::all(compact('conditions', 'order'));
        }

        public function getGames($entity)
        {
            $conditions = array('teams' => $entity->_id);
            $order      = array('game_time' => 1);

            return Games::all(compact('conditions', 'order'));
        }

        public function isManager($entity, $user)
        {
            if (!isset($user->_id)) {
                return null;
            }

            $league = $entity->getLeague();

            if ($league->isManager($user)) {
                return true;
            }

            if ($user->can('teams.manage')) {
                return true;
            }

            foreach ($entity->captains as $cid) {
                if ($cid == $user->_id) {
                    return true;
                }
            }

            return false;
        }

        public function canReport($entity, $user)
        {
            if (!isset($user->_id)) {
                return null;
            }

            if ($entity->isManager($user)) {
                return true;
            }

            return false;
        }
    }