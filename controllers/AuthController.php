<?php

    namespace app\controllers;

    use \lithium\action\Controller;
    use \lithium\security\Auth;

    class AuthController extends Controller
    {

        public function index()
        {
            // Display different login methods
        }

        public function logout()
        {
            Auth::clear('any');

            return $this->redirect('/');
        }

        public function login()
        {
            $result = Auth::check($this->request->adapter, $this->request);

            if ($result) {
                Auth::set('any', $result);
                return $this->redirect('/home');
            } else {
                // TODO: Set login failure flash message

                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';
                return $this->redirect($redirectUrl);
            }
        }
    }