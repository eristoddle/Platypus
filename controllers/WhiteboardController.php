<?php
    namespace app\controllers;

    use \lithium\action\Controller;
    use \lithium\security\Auth;

    use app\models\Users;
    use app\models\Identities;
    use app\models\Leagues;
    use app\models\Registrations;
    use app\models\Games;

    class WhiteboardController extends Controller
    {
        public function index()
        {
            $game = Games::first();
            $league = $game->getLeague();
            $fieldsite = $game->getFieldSite();
            $teams = $game->getTeams();

            $a = array();
            foreach ($teams as $t) {
                $a[] = $t->name;
            }

            $b = $league->to('array');
            $c = $fieldsite->to('array');
            
            return compact('a', 'b', 'c');
        }
    }
    