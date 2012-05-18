<?php
    namespace app\controllers;

    use lithium\security\validation\RequestToken;
    use lithium\security\Auth;
    use app\extensions\action\Controller;
    use app\models\Leagues;
    use app\models\Registrations;
    use app\models\CartItems;
    use app\models\Users;

    class RegistrationsController extends Controller
    {
        public function view()
        {
            if (!isset($this->request->id)) {
                $this->flashMessage('Could not load that registration.', array('alertType' => 'error'));
                return $this->redirect('Leagues::index');
            }

            $registration = Registrations::find($this->request->id);
            $league = $registration->getLeague();
            $user = $registration->getUser();

            return compact('registration', 'league', 'user');
        }
    }