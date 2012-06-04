<?php

    namespace app\models;

    use app\extensions\data\Model;

    class Games extends Model
    {
        public $belongsTo = array('Leagues', 'FieldSites');

        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'game_time' => array('type' => 'date'),
            'league_id' => array('type' => 'id'),
            'fieldsite_id' => array('type' => 'id'),
            'teams' => array('type' => 'array'),
            'field' => array('type' => 'string'),
            'round_number' => array('type' => 'integer')
        );

        public function getLeague($entity) {
            $conditions = array('_id' => $entity->league_id);

            return Leagues::first(compact('conditions'));
        }

        public function getFieldSite($entity) {
            $conditions = array('_id' => $entity->fieldsite_id);

            return FieldSites::first(compact('conditions'));
        }

        public function getOpponent($entity, $team_id)
        {
            $team_list = $entity->teams->export();
            $team_list = $team_list['data'];

            $opp_id = null;

            foreach ($team_list as $t) {
                if ($t == $team_id) {
                    continue;
                }

                if (!is_null($opp_id)) {
                    return null;
                }

                $opp_id = $t;
            }

            $opp_team = Teams::find((string) $opp_id);
            return $opp_team;
        }
    }