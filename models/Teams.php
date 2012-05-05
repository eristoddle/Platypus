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

        public function getCaptains($entity) {
            if (is_null($entity->tempDataGet('captains'))) {
                $captainsArray = array();
                $conditions = array('_id' => array('$in' => $entity->captains));
                $users = Users::find('all', compact('captains'));

                foreach ($users as $u) {
                    $usersArray[] = $u;
                }

                $entity->tempDataSet('captains', $captainsArray);
            }

            return $entity->tempDataGet('captains');
        }

        public function getLeague($entity) {
            if (is_null($entity->tempDataGet('league'))) {
                $conditions = array('_id' => $entity->league_id);
                $entity->tempDataSet('league', Leagues::first(compact('conditions')));
            }

            return $entity->tempDataGet('league');
        }

        public function getPlayers($entity) {
            $conditions = array('teams' => $entity->_id);

            return Users::all(compact($conditions));
        }   
    }