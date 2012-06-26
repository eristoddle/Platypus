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
            'round_number' => array('type' => 'integer'),
            'old_scores' => array('type' => 'object', 'array' => true),
            'scores' => array('type' => 'object'),
                'scores.report_time' => array('type' => 'date'),
                'scores.forfeit' => array('type' => 'boolean'),
                'scores.rainout' => array('type' => 'boolean'),
                'scores.reporter_id' => array('type' => 'id'),
                'scores.reporter_ip' => array('type' => 'string'),
            'winner' => array('type' => 'id')
        );

        public function getLeague($entity) {
            $conditions = array('_id' => $entity->league_id);

            return Leagues::first(compact('conditions'));
        }

        public function getFieldSite($entity) {
            $conditions = array('_id' => $entity->fieldsite_id);

            return FieldSites::first(compact('conditions'));
        }

        public function getScoreDiff($entity)
        {
            $diff = null;
            if (is_object($entity->scores)) {
                foreach ($entity->teams as $t) {
                    $team_score = $entity->scores[(string)$t];
                    if (!isset($diff)) {
                        $diff = $team_score;
                    } else {
                        $diff -= $team_score;
                    }
                }

                $diff = abs($diff);
            }

            return $diff;
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

        public function getReporter($entity)
        {
            if (!isset($entity->scores->reporter_id)) {
                return null;
            }
            $conditions = array('_id' => $entity->scores->reporter_id);

            return Users::first(compact('conditions'));            
        }

        public function getTeams($entity)
        {
            $team_ids = $entity->teams->export();
            $conditions = array('_id' => array('$in' => $team_ids['data']));

            return Teams::all(compact('conditions'));
        }

        public function canReport($entity, $user)
        {
            if (!isset($user->_id)) {
                return null;
            }

            if ($user->can('games.report_score')) {
                return true;
            }

            $teams = $entity->getTeams();

            foreach ($teams as $t) {
                if ($t->canReport($user)) {
                    return true;
                }
            }

            return false;
        }
    }