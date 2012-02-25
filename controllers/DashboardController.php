<?php
    namespace app\controllers;

    use lithium\action\Controller;
    use lithium\security\Auth;

    class DashboardController extends Controller
    {
        public function index()
        {
            $a = Auth::check('any');
            return compact('a', 'b', 'c');
        }
        public function user()
        {
            $id = Auth::check('any');

            if ($id) {
                $user = $id->getUser();
                $a = $user->email_address;
                $b = $user->privacy['home'];
            }

            return compact('a', 'b', 'c');
        }
    }