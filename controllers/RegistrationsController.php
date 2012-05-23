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
            $league = $this->_registration->getLeague();
            if ($league->isManager($this->CURRENT_USER) and $this->request->data) {                
                $this->request->data['secondary_rank_data'] = (array)$this->request->data['secondary_rank_data'] + $this->_registration->secondary_rank_data->to('array');;

                if (isset($this->request->data['secondary_rank_data']['commish_rank']) and empty($this->request->data['secondary_rank_data']['commish_rank'])) {
                    unset($this->request->data['secondary_rank_data']['commish_rank']);
                }

                if ($this->_registration->save($this->request->data)) {
                    $this->flashMessage('Registration updated successfully.', array('alertType' => 'success'));
                } else {
                    $this->flashMessage('There was an error.', array('alertType' => 'error'));
                    var_dump($this->_registration->errors(), $this->_registration->to('array')); die;
                }
            }
        }

        public function edit()
        {
        }
    }