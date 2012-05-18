<?php

    namespace app\controllers;

    use app\extensions\action\Controller;
    use app\models\Identities;
    use app\models\PasswordIdentities;
    use app\models\Users;

    use lithium\security\Auth;
    use lithium\analysis\Logger;
    use lithium\core\Environment;

    class AuthController extends Controller
    {

        public function index()
        {
            if (isset($this->CURRENT_USER)) {
                return $this->redirect('Dashboard::user');
            }
        }

        public function logout()
        {
            Auth::clear('any');

            return $this->redirect('/');
        }

        public function login()
        {
            $result = Auth::check($this->request->adapter, $this->request);

            $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

            if ($result) {
                # Convert array to identity object
                if ($this->request->adapter === 'password') {
                    $result = Identities::find($result['_id']);
                }

                Auth::set('any', $result);
            } else {
                $addendum = '';

                // Adapter-specific error messages
                if ($this->request->adapter == 'phpbb') {
                    $addendum = ' Please ensure that you are logged into the <a href="http://www.afdc.com/forum/">forums</a>.'; 
                } else {
                    Logger::debug("Failed login for " . $this->request->data['email'] . " with password " . $this->request->data["password"]);
                }

                $this->flashMessage('Your login was unsuccessful.' . $addendum, array('alertType' => 'error'));
            }

            return $this->redirect($redirectUrl);
        }

        public function resetPassword()
        {
            $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

            $email = null;
            if (isset($this->request->data['email'])) {
                $email = $this->request->data['email'];
            } else if (isset($this->request->args[0])) {
                $email = $this->request->args[0];
            }

            $user = Users::first(array('conditions' => array('email_address' => $email)));

            if (!$user) {
                $this->flashMessage('User not found for password reset!', array('alertType' => 'error'));
                return $this->redirect($redirectUrl);
            } else if (!isset($user->email_address)) {
                $this->flashMessage('That user does not have an email address on file. Please email the webmaster for assistance.', array('alertType' => 'error'));
                return $this->redirect($redirectUrl);
            }

            $identity = PasswordIdentities::first(array('conditions' => array('user_id' => $user->_id, 'type' => 'password', 'prv_name' => 'afdc.com')));

            if (!$identity) {
                $identity = PasswordIdentities::create();
                $identity->user_id = $user->_id;
                $identity->prv_uid = strtolower($user->email_address);
            }

            $newPassword = $identity->generatePassword();

            if ($identity->save()) {
                if (Environment::is('production')) {
                    // Todo: replace this with something that doesn't suck
                    $to      = $user->email_address;
                    $subject = '[AFDC.com] Password Reset';
                    $message = 'Your password has been reset. It is now: ' . $newPassword;
                    $headers = implode("\n", array(
                        'From: system@leagues.afdc.com',
                        'Reply-To: webmaster@afdc.com',
                        'X-Mailer: PHP/' . phpversion(),
                    ));

                    mail($to, $subject, $message, $headers);

                    $this->flashMessage('An email message has been sent with the new password. Please be sure to check your spam folder.', array('alertType' => 'info'));
                } else {
                    $this->flashMessage("A new password generated: {$user->email_address} / {$newPassword}. Due to environment limitations, no email was sent.", array('alertType' => 'info'));
                }

                return $this->redirect($redirectUrl);
            } else {
                $this->flashMessage('A new password could not be saved; please try again or email the webmaster for assistance.', array('alertType' => 'error'));
                return $this->redirect($redirectUrl);                
            }

            return compact('user', 'identity', 'newPassword');
        }
    }