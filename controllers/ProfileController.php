<?php

    namespace app\controllers;

    use app\extensions\action\Controller;
    use app\models\Identities;
    use app\models\PasswordIdentities;

    use \lithium\security\Auth;

    class ProfileController extends Controller
    {
        protected function _init()
        {
            parent::_init();

            // Profile Controller requires an existing user
            if (!isset($this->CURRENT_USER)) {
                $this->flashMessage('You must be logged in to view that page.', array('alertType' => 'error'));
                return $this->redirect('/');
            }
        }

        public function index()
        {
            $user = $this->CURRENT_USER;

            $ids = Identities::all(array('conditions' => array('user_id' => $user->_id)));

            $identities = array();

            foreach ($ids as $id) {
                $identities[$id->type] = $id->to('array');
            }

            return compact('user', 'identities');
        }

        public function edit()
        {
            $user = $this->CURRENT_USER;

            if ($this->request->data) {
                $user->set($this->request->data);

                if (isset($this->request->data['password']) and !empty($this->request->data['password'])) {
                    $identity = $user->getIdentity('afdc.com', 'password');

                    if (!isset($identity)) {
                        $identity = PasswordIdentities::create();
                        $identity->user_id = $user->_id;
                    } else {
                        $identity = PasswordIdentities::find((string)$identity->_id);
                    }

                    $identitySaveResult = $identity->save(array(
                            'password' => $this->request->data['password'],
                            'confirm_password' => $this->request->data['confirm_password'],
                            'prv_uid' => $this->request->data['email_address']
                    ));
                } else {
                    $identitySaveResult = true;
                }

                if (!$identitySaveResult) {
                    $identityErrors = $identity->errors();

                    if (isset($identityErrors['password'])) {
                        $user->errors('password', $identityErrors['password']);    
                    }
                    if (isset($identityErrors['confirm_password'])) {
                        $user->errors('confirm_password', $identityErrors['confirm_password']);    
                    }
                } else {
                    unset($user->password);
                    unset($user->confirm_password);

                    if ($user->save()) {
                        $this->flashMessage('Your profile has been updated!', array('alertType' => 'success'));
                        return $this->redirect('Profile::index');
                    }
                }                    
            }


            return compact('user');
        }
    }