<?php

    namespace app\models;

    use lithium\data\Model;
    use app\models\Users;
    use app\models\Leagues;

    class Registrations extends Model
    {
        public $belongsTo = array('Users', 'Leagues');

        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'user_id' => array('type' => 'id'),
            'league_id' => array('type' => 'id'),
            'paid' => array('type' => 'boolean'),
            'official_rank' => array('type' => 'integer'),
            'player_strength' => array('type' => 'string'),
            'signup_timpestamp' => array('type' => 'datetime'),
            'pair' => array('type' => 'subdocument'),
            'availability' => array('type' => 'subdocument')
        );

        public function getUser($entity) {
            if (is_null($entity->tempDataGet('user'))) {
                $conditions = array('_id' => $entity->user_id);
                $entity->tempDataSet('user', Users::first(compact('conditions')));
            }

            return $entity->tempDataGet('user');
        }

        public function getLeague($entity) {
            if (is_null($entity->tempDataGet('league'))) {
                $conditions = array('_id' => $entity->league_id);
                $entity->tempDataSet('league', Leagues::first(compact('conditions')));
            }

            return $entity->tempDataGet('league');
        }
    }