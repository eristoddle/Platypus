<?php

    namespace app\models;

    use app\extensions\data\Model;
    
    class Registrations extends Model
    {
        public static $userMetadataFields = array('firstname', 'middlename', 'lastname', 'height', 'weight', 'birthdate');
        
        public $belongsTo = array('Users', 'Leagues');
        
        protected $_meta = array(
            'key' => '_id',
            'title' => 'user_id'
        );

        protected $_schema = array(
            '_id'  => array('type' => 'id'), // required for Mongo
            'user_id' => array('type' => 'id'),
            'league_id' => array('type' => 'id'),
            'paid' => array('type' => 'boolean', 'default' => false),
            'status' => array('type' => 'string', 'default' => 'pending'),
            'player_strength' => array('type' => 'string'),
            'signup_timestamp' => array('type' => 'date'),
            'payment_timestamps' => array('type' => 'object'),
                'payment_timestamps.completed' => array('type' => 'date'),
                'payment_timestamps.pending' => array('type' => 'date'),
                'payment_timestamps.refunded' => array('type' => 'date'),
            'payments' => array('type' => 'id', 'array' => true),
            'pair' => array('type' => 'object'),
            'gender' => array('type' => 'string'),
            'availability' => array('type' => 'object'),
                'availability.general' => array('type' => 'string'),
                'availability.attend_tourney_mst' => array('type' => 'boolean'),
                'availability.attend_tourney_eos' => array('type' => 'boolean'),
            'team_style_pref' => array('type' => 'object'),
                'team_style_pref.competitive' => array('type' => 'boolean'),
                'team_style_pref.social' => array('type' => 'boolean'),
                'team_style_pref.family' => array('type' => 'boolean'),
            'secondary_rank_data' => array('type', 'object'),
                'secondary_rank_data.self_rank' => array('type' => 'string'),
                'secondary_rank_data.grank' => array('type' => 'float'),
                'secondary_rank_data.commish_rank' => array('type' => 'float')
        );

        protected static $_rankPriority = array('commish_rank', 'grank', 'self_rank');

        public $validates = array(
            'user_id' => 'User ID must not be empty.',
            'league_id' => 'League ID must not be empty.',
            'paid' => array('boolean', 'Paid must be either true or false.'),
            'availability.general' => array(
                array('inList', 'list' => array('25%', '50%', '75%', '100%'), 'message' => 'You must select an attendance rate.')
            ),
            'player_strength' => array(
                array('inList', 'message' => 'Player strength must be thrower, runner, or both.', 'list' => array('thrower', 'runner', 'both'))
            ),
            'secondary_rank_data.self_rank' => array(
                array('inList', 'message' => 'Rank must be a whole number between 0 and 9', 'list' => array('0','1','2','3','4','5','6','7','8','9'))
            ),
            'status' => array(
                array('inList', 'list' => array('active', 'pending', 'canceled'), 'message' => 'Invalid registration status.')
            ),
            'secondary_rank_data.commish_rank' => array(
                array('inList', 'list' => array('0','0.5','1','1.5','2','2.5','3','3.5','4','4.5','5','5.5','6','6.5','7','7.5','8','8.5','9','9.5'), 'message' => 'League rank must be between 0 and 9.', 'required' => false)
            )
        );

        public function getOfficialRank($entity)
        {
            if ($entity->secondary_rank_data) {
                $srd = $entity->secondary_rank_data;
                
                foreach (static::$_rankPriority as $r) {
                    if (isset($srd->{$r})) {
                        return $srd->{$r};
                    }
                }
            }

            return null;
        }

        public function getUser($entity)
        {
            if (empty($entity->user_id)) { return null; }

            if (is_null($entity->tempDataGet('user'))) {
                $conditions = array('_id' => $entity->user_id);
                $entity->tempDataSet('user', Users::first(compact('conditions')));
            }

            return $entity->tempDataGet('user');
        }

        public function getLeague($entity)
        {
            if (empty($entity->league_id)) { return null; }

            if (is_null($entity->tempDataGet('league'))) {
                $conditions = array('_id' => $entity->league_id);
                $entity->tempDataSet('league', Leagues::first(compact('conditions')));
            }

            return $entity->tempDataGet('league');
        }

        public function regErrors($entity, $new_error = null, $doValidation = true)
        {

            if ($new_error) {
                $errors = $entity->tempDataGet('errors');
                $errors[] = $new_error;
                $entity->tempDataSet('errors', $errors);
                return;
            } else {
                $errors = $entity->tempDataGet('errors');
                if (is_array($errors)) {
                    return $errors;
                } else {
                    if ($doValidation) {
                        $entity->isValid();    
                    } 

                    return (array) $entity->tempDataGet('errors');
                }
            }
        }

        public function isValid($entity)
        {
            $league = $entity->getLeague();
            $user   = $entity->getUser();

            if (is_null($league)) {
                $entity->errors('League not found.');
            }

            # Check Player Limits
            if (isset($league->player_limit)) {
                $limits = $league->player_limit->to('array');

                if (isset($limits['male']) and $user->gender == 'male') {
                    if ($limits['male'] == 0) {
                        $entity->regErrors('This is a women-only league.');
                    } else {
                        // TODO: Change this to ::count();
                        $men_count = Registrations::all(array('conditions' => array('league_id' => $league->_id, 'status' => 'active', 'gender' => 'male')))->count();
                        if ($men_count >= $limits['male']) {
                            $entity->regErrors('Leauge cannot accommodate any more men.');
                        }
                    }
                }

                if (isset($limits['female']) and $user->gender == 'female') {
                    if ($limits['female'] == 0) {
                        $entity->regErrors('This is a men-only league.');
                    } else {
                        $women_count = Registrations::all(array('conditions' => array('league_id' => $league->_id, 'status' => 'active', 'gender' => 'female')))->count();
                        if ($women_count >= $limits['female']) {
                            $entity->regErrors('Leauge cannot accommodate any more men.');
                        }                        
                    }
                }

            }
            return count($entity->regErrors(null, false)) == 0;
        }
    }