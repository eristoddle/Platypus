<?php
    namespace app\controllers;

    use app\extensions\action\Controller;
    use app\models\Leagues;
    use app\models\Registrations;
    use app\models\CartItems;
    use app\models\Users;

    class RegistrationsController extends Controller
    {
        protected function _init()
        {
            parent::_init();

            if (!isset($this->CURRENT_USER)) {
                $this->flashMessage('You must be logged in to view that page.', array('alertType' => 'error'));                
                return $this->redirect('Leagues::index');
            }

            if (!isset($this->request->id)) {
                $this->flashMessage('Registration ID not supplied.', array('alertType' => 'error'));
                return $this->redirect('Leagues::index');
            }

            $this->_registration = Registrations::find($this->request->id);
            
            if (!isset($this->_registration)) {
                $this->flashMessage('Could not load that registration.', array('alertType' => 'error'));
                return $this->redirect('Leagues::index');
            }

            $this->set(array(
                'registration' => $this->_registration,
                'league'       => $this->_registration->getLeague(),
                'user'         => $this->_registration->getUser()
            ));
        }

        public function view()
        {
        }

        public function edit()
        {
        }
    }