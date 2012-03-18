<?php
    namespace app\controllers;

    use \lithium\action\Controller;
    use \lithium\security\Auth;

    use app\models\Users;
    use app\models\Identities;

    class WhiteboardController extends Controller
    {
        public function index()
        {
            $pete = Users::find('first', array('conditions' => array('email_address' => 'pete.holiday@gmail.com')));

            $a = $pete->to('array');

            return compact('a', 'b');
        }
    }