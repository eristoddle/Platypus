<?php
    namespace app\controllers;

    use app\models\Users;
    use app\models\Identities;
    use app\models\PasswordIdentities;

    use app\extensions\action\Controller;

    class UsersController extends Controller
    {
        public function index()
        {
            // TODO: Security? users.search

            if (isset($this->request->query['q'])) {
                $query = $this->request->query['q'];
                $conditions = array('$or' => array(
                    array(
                        'email_address' => array(
                            '$regex' => "^{$query}",
                            '$options' => 'i'
                        )
                    ),
                    array(
                        'firstname' => array(
                            '$regex' => "^{$query}",
                            '$options' => 'i'                            
                        )
                    ),
                    array(
                        'lastname' => array(
                            '$regex' => "^{$query}",
                            '$options' => 'i'                            
                        )
                    )
                ));
                $userList = Users::all(compact('conditions'));
            }
        
            return compact('userList', 'query');
        }

        public function create()
        {
            if (isset($this->CURRENT_USER)) {
                $this->flashMessage('You don\'t need to register, you\'re already logged in!', array('alertType' => 'warning'));
                return $this->redirect('Dashboard::user');
            }

            $user = Users::create();
            if ($this->request->data) {
                $user->set($this->request->data);

                $identity = PasswordIdentities::create();
                $identitySaveResult = $identity->save(array(
                    'prv_uid' => $this->request->data['email_address'],
                    'password' => $this->request->data['password'],
                    'confirm_password' => $this->request->data['confirm_password']
                ));

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

                    if ($user->save(array('permission_groups' => 'user'))) {
                        $identity->user_id = $user->_id;
                        $identity->save();

                        $this->flashMessage('You have successfully registered!');
                        return $this->redirect('/');
                    } 
                }                    
            }

            return compact('user');
        }

        public function checkForExisting()
        {

        }
    }
