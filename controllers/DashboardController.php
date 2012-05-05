<?php
    namespace app\controllers;

    use app\extensions\action\Controller;

    class DashboardController extends Controller
    {
        public function index()
        {
            return compact('a', 'b', 'c');
        }
        public function user()
        {
            $this->flashMessage('The user dashboard is still under construction.', array('alertType' => 'warning'));
            return $this->redirect('Profile::index');
            $user = $this->CURRENT_USER;

            if ($user) {
                $a = $user->email_address;
                $b = $user->privacy['home'];
            }

            return compact('a', 'b', 'c');
        }
    }