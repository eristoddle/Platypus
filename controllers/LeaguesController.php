<?php
    namespace app\controllers;

    use lithium\security\validation\RequestToken;
    use lithium\security\Auth;
    use app\extensions\action\Controller;
    use app\models\Leagues;
    use app\models\Games;
    use app\models\Registrations;
    use app\models\CartItems;
    use app\models\Users;
    use app\models\Teams;

    class LeaguesController extends Controller
    {
        protected $league = null;

        protected function _init() {
            parent::_init();

            $this->league = Leagues::find($this->request->id);

            if ($this->league) {
                $this->set(array('league' => $this->league));
            }

            // Keep empty fields from being converted to zeros.
            if ($this->request->data and isset($this->request->data['player_limit'])) { 
                if ($this->request->data['player_limit']['male'] === '') {
                    unset($this->request->data['player_limit']['male']);
                }
                if ($this->request->data['player_limit']['female'] === '') {
                    unset($this->request->data['player_limit']['female']);
                }
            }
        }

        public function participants()
        {
            if (!isset($this->CURRENT_USER)) {
                $this->flashMessage('You must be logged in to view that page.', array('alertType' => 'error'));
                return $this->redirect('Leagues::index');
            }

            if (!$this->league and !in_array($this->request->action, $league_id_exempt)) {
                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

                $this->flashMessage('League not found.', array('alertType' => 'error'));

                return $this->redirect($redirectUrl);
            }

            $inLeague = array('league_id' => $this->league->_id);

            $active_list = Registrations::find('all', array(
                'conditions' => $inLeague + array('status' => 'active')
            ));

            $pending_list = Registrations::find('all', array(
                'conditions' => $inLeague + array('status' => array('$ne' => 'active'))
            ));

            return compact('active_list', 'pending_list');
        }

        public function index()
        {
            $l_list = Leagues::all(array('order' => array('start_date' => -1, 'end_date' => 1)));

            $leagues = array(
                'past' => array(),
                'present' => array(),
                'future' => array()
            );

            foreach ($l_list as $l) {
                $commissioner_names = array();
                foreach ($l->getCommissioners() as $c) {
                    $commissioner_names[] = $c->firstname . ' ' . $c->lastname;
                }

                $league_data = $l->to('array');
                $league_data['commissioners'] = implode(', ', $commissioner_names);
                $league_data['meta']['registration_open'] = $l->registrationOpen() ?: false;

                if ($l->start_date->sec > time()) {
                    $leagues['future'][] = $league_data;
                } else if ($l->end_date->sec < time()) {
                    $leagues['past'][] = $league_data;
                } else {
                    $leagues['present'][] = $league_data;
                }
            }

            return compact('leagues');
        }

        public function view()
        {
            if (!$this->league) {
                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

                $this->flashMessage('League not found.', array('alertType' => 'error'));

                return $this->redirect($redirectUrl);
            }

            $league = $this->league;
            return compact('league');
        }

        public function edit()
        {
            if (!$this->league) {
                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

                $this->flashMessage('League not found.', array('alertType' => 'error'));

                return $this->redirect($redirectUrl);
            }

            if ($this->request->data and $this->league->save($this->request->data)) {
                return $this->redirect('Leagues::index');
            }
            return;
        }

        public function create()
        {
            $league = Leagues::create();

            if ($this->request->data and $league->save($this->request->data)) {
                return $this->redirect('Leagues::index');
            }

            return compact('league');
        }

        public function register()
        {
            if (!$this->league) {
                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

                $this->flashMessage('League not found.', array('alertType' => 'error'));

                return $this->redirect($redirectUrl);
            }

            $league = $this->league;
            $user   = $this->CURRENT_USER;

            if (!$league->registrationOpen()) {
                throw new \Exception('Registration is not open for that league.');
            }

            if (!isset($user)) {
                throw new \Exception('You must be logged in to register for a league.');
            }

            if (!$user->can('leagues.register')) {
                throw new \Exception('You do not have permission to register for leagues.');
            }

            $registration = $league->getUserRegistration($user);

            $newRegistration = false;
            if (!isset($registration)) {
                $newRegistration = true;
                $registration = Registrations::create();
                $registration->user_id = $user->_id;
                $registration->league_id = $league->_id;
                $registration->signup_timestamp = time();
                $registration->gender = $user->gender;

                // Copy User Metadata to reduce queries for things like team rosters
                $user_metadata = array();
                foreach (Registrations::$userMetadataFields as $f) {
                    if (isset($user->{$f})) {
                        $user_metadata[$f] = $user->{$f};
                    }

                    $registration->user_data = $user_metadata;
                }
            }

            if ($this->request->data and $registration->save($this->request->data)) {
                $cartItem = CartItems::first(array('conditions' => array('reference_class' => 'registrations', 'reference_id' => $registration->_id)));

                // Free registrations
                if ($this->CURRENT_USER->can('register.free')) {
                    if ($cartItem and $cartItem->isValid()) {
                        // User has been added to this group after registering
                        $cartItem->status = CartItems::STATUS_DONE;
                        $cartItem->save();
                    }

                    if ($registration->isValid()) {
                        $registration->status = 'active';
                        $registration->save();
                        $this->flashMessage('Your registration has been comped by the AFDC, you are now registered.', array('alertType' => 'success'));
                        return $this->redirect('Leagues::index');
                    } else {
                        $this->flashMessage('Your registration has been comped by the AFDC, but the league is currently full.', array('alertType' => 'error'));
                        return $this->redirect('Leagues::index');
                    }

                }

                
                if (!isset($cartItem)) {
                    $cartItem = CartItems::create();
                    $cartItem->name = $league->name . ' registration for ' . $user->firstname . ' ' . $user->lastname;
                    $cartItem->price = $league->price;
                    $cartItem->reference_class = 'registrations';
                    $cartItem->reference_id = $registration->_id;

                    $cart = $user->getShoppingCart(true);

                    $cartItem->carts = array($cart->_id);
                    $cartItem->save();
                }

                // Notify the user
                $regMessage = 'Registration ' . ($newRegistration ? 'saved' : 'updated') . ' for <strong>' . $league->name . '</strong>. ';

                if ($registration->paid == false) {
                    $regMessage .= ' Please pay via the checkout page to complete your registration.';
                }

                $this->flashMessage($regMessage, array('alertType' => 'success'));
                
                if (!$registration->paid) {
                    return $this->redirect('Carts::index');
                }

                return $this->redirect('Leagues::index');
            }

            return compact('registration');
        }

        public function reportScore()
        {
            $game = Games::find($this->request->id);

            if (!$game) {
                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

                $this->flashMessage('Game not found.', array('alertType' => 'error'));

                return $this->redirect($redirectUrl);                
            }

            if (!isset($this->CURRENT_USER) or !$game->canReport($this->CURRENT_USER)) {
                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

                $this->flashMessage('You do not have permission to report scores for that game.', array('alertType' => 'error'));

                return $this->redirect($redirectUrl);                                
            }

            if ($this->request->data) {
                if ($game->scores) {
                    $old_score_report = $game->scores->export();
                    $old_score_report = $old_score_report['data'];
                }

                $team_ids = array();

                $new_score_report = array(
                    'reporter_id' => $this->CURRENT_USER->_id,
                    'report_time' => time(),
                    'reporter_ip' => $this->request->env('REMOTE_ADDR'),
                    'rainout' => false,
                    'forfeit' => false
                );

                $max_score = -1;
                $winner_id = null;
                foreach ($this->request->data['scores'] as $id => $score) {
                    $score = abs(intval($score));
                    if ($score > $max_score) {
                        $max_score = $score;
                        $winner_id = $id;
                    } else if ($score == $max_score) {
                        // This means we have a tie, somehow
                        $winner_id = null;
                    }

                    $new_score_report[$id] = $score;

                    $team_ids[] = new \MongoId($id);
                }

                $game->scores = $new_score_report;
                $game->winner = $winner_id;

                if ($game->save() and isset($old_score_report)) {

                    // Log the previously-reported score
                    $conditions = array('_id' => $game->_id);
                    $query = array('$push' => array('old_scores' => $old_score_report));
                    Games::update($query, $conditions);
                }

                // Mark teams as needing an update
                $conditions = array('_id' => array('$in' => $team_ids));
                $query = array('$set' => array('stats.needs_update' => true));
                Teams::update($query, $conditions);

                $this->flashMessage('Your score has been successfully reported.', array('alertType' => 'success'));
                return $this->redirect(($this->request->env('HTTP_REFERER') ?: '/'));
            }

            $league = $game->getLeague();

            $teams = array();

            foreach ($game->teams as $t_id) {
                $teams[(string) $t_id] = Teams::find((string) $t_id);
            }
            
            return compact('game', 'league', 'teams');
        }
    }