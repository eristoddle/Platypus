<?php
    namespace app\controllers;

    use app\extensions\action\Controller;

    use lithium\security\Auth;
    use lithium\security\Password;
    use lithium\net\http\Router;

    use app\util\Permissions;

    use app\models\Users;
    use app\models\Identities;
    use app\models\Leagues;
    use app\models\Registrations;
    use app\models\Games;
    use app\models\ShoppingCarts;
    use app\models\Payments;
    use app\util\Config;

    use PaypalWrapper\Capture;

    class WhiteboardController extends Controller
    {
        protected function _init()
        {
            parent::_init();

            // Profile Controller requires an existing user
            if (!isset($this->CURRENT_USER) or !$this->CURRENT_USER->can('whiteboard.view')) {
                $this->flashMessage('You must be logged in to view that page.', array('alertType' => 'error'));
                return $this->redirect('/');
            }


        }

        public function index()
        {
            // [ "active", "pending", null, "canceled" ]
            $league_id = '4f9ffe2b406eb74b4100001d';
            $a['male'] = array(
                'active' => Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => 'active', 'gender' => 'male'))),
                'pending' => Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => 'pending', 'gender' => 'male'))),
                'canceled' => Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => 'canceled', 'gender' => 'male'))),
                'null' =>Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => null, 'gender' => 'male')))
            );

            $a['female'] = array(
                'active' => Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => 'active', 'gender' => 'female'))),
                'pending' => Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => 'pending', 'gender' => 'female'))),
                'canceled' => Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => 'canceled', 'gender' => 'female'))),
                'null' =>Registrations::count(array('conditions' => array('league_id' => $league_id, 'status' => null, 'gender' => 'female')))
            );

            return compact('a', 'b', 'c');
        }

        public function flashTest()
        {
            $this->flashMessage('Woo!');

            return $this->redirect('Whiteboard::index');
        }

        public function gRank()
        {
            # code...
        }
    }
    