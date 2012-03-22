<?php

    namespace app\models;

    use lithium\data\Model;

    class Leagues extends Model
    {
    {
        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'age_division' => array('type' => 'string'),
            'end_date' => array('type' => 'datetime'),
            'name' => array('type' => 'string'),
            'price' => array('type' => 'float'),
            'registration_close' => array('type' => 'datetime'),
            'registration_open' => array('type' => 'datetime'),
            'season' => array('type' => 'string'),
            'sport' => array('type' => 'string'),
            'start_date' => array('type' => 'datetime'),
            'player_limit' => array('type' => 'subdocument'),
            'commissioner_ids' => array('type' => 'array')
        }

        public function getCommissioners($entity) {
            if (is_null($entity->tempDataGet('commissioners'))) {
                $commishArray = array();
                $conditions = array('_id' => array('$in' => $entity->commissioner_ids));
                $commishes = Users::find('all', compact('conditions'));

                foreach ($commishes as $c) {
                    $commishArray[] = $c;
                }

                $entity->tempDataSet('commissioners', $commishArray);
            }

            return $entity->tempDataGet('commissioners');
        }

    }