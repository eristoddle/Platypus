<?php
    namespace app\extensions\action;


    use lithium\security\validation\RequestToken;
    use lithium\security\Auth;

    use app\models\Identities;

    use li3_flash_message\extensions\storage\FlashMessage;

    class Controller extends \lithium\action\Controller
    {
        protected $CURRENT_USER = null;

        protected function _init()
        {
            parent::_init();

            # Check CSRF forgery signature
            if ($this->request->data and !RequestToken::check($this->request)) {
                throw new \Exception('Invalid request token.');
            }

            if (isset($this->request->data['security']['token']) ) {
                unset($this->request->data['security']); 
            }

            # Load active user
            $current_identity = Auth::check('any');

            if (is_object($current_identity)) {
                $u = $current_identity->getUser();
                $this->CURRENT_USER = $u;
            }

            $this->set(array('CURRENT_USER' => $this->CURRENT_USER));
        }

        protected function flashMessage($msg, $viewAttribs = array(), $namespace = 'global')
        {
            return FlashMessage::write($msg, $viewAttribs, $namespace);
        }
    }