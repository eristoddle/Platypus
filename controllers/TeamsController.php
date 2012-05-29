<?php
    namespace app\controllers;

    use app\models\Teams;
    use app\models\Users;
    use app\models\Leagues;

    use app\extensions\action\Controller;

    class TeamsController extends Controller
    {
        public function index()
        {
            // TODO: Security? users.search

            if (isset($this->request->query['q'])) {
                $query = $this->request->query['q'];
                $conditions = array(
                    'name' => array(
                        '$regex' => "{$query}",
                        '$options' => 'i'
                    )
                );
                $teamList = Teams::all(compact('conditions'));
            }

            return compact('teamList', 'query');
        }

        public function view()
        {
            $team = Teams::find($this->request->id);

            if (!isset($team)) {
                $redirectUrl = $this->request->env('HTTP_REFERER') ?: '/';

                $this->flashMessage('Team not found.', array('alertType' => 'error'));

                return $this->redirect($redirectUrl);
            }

            return compact('team');
        }

        public function checkForExisting()
        {

        }
    }
