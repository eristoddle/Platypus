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
            // # Create ID
            // $temp = Users::first();
            // $a = Identities::create();

            // $a->type     = 'phpbb';
            // $a->user_id  = $temp->_id;
            // $a->prv_name = 'afdc.com';
            // $a->prv_uid  = 'peteholiday';
            // $a->save();

            $temp = Auth::check('phpbb', 'abc');

            $a = $temp->getUser();

            return compact('a', 'b', 'c', 'user');
        }
    }