<?php

    namespace app\models;

    use lithium\data\Model;

    class Games extends Model
    {
        public $belongsTo = array('Leagues', 'FieldSites');

        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'game_time' => array('type' => 'datetime'),
            'league_id' => array('type' => 'id'),
            'fieldsite_id' => array('type' => 'id'),
            'teams' => array('type' => 'array'),
            'field' => array('type' => 'string'),
            'round_number' => array('type' => 'integer')
        );

        public function getLeague($entity) {
            if (is_null($entity->tempDataGet('league'))) {
                $conditions = array('_id' => $entity->league_id);
                $entity->tempDataSet('league', Leagues::first(compact('conditions')));
            }

            return $entity->tempDataGet('league');
        }

        public function getFieldSite($entity) {
            if (is_null($entity->tempDataGet('fieldsite'))) {
                $conditions = array('_id' => $entity->fieldsite_id);
                $entity->tempDataSet('fieldsite', FieldSites::first(compact('conditions')));
            }

            return $entity->tempDataGet('fieldsite');
        }

        public function getTeams($entity) {
            if (is_null($entity->tempDataGet('teams'))) {
                $teamsArray = array();
                $conditions = array('_id' => array('$in' => $entity->teams));
                $teams = Teams::find('all', compact('conditions'));

                foreach ($teams as $t) {
                    $teamsArray[] = $t;
                }

                $entity->tempDataSet('teams', $teamsArray);
            }

            return $entity->tempDataGet('teams');
        }
    }