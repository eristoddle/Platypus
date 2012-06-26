<?php
namespace app\extensions\command;

use app\models\Teams;
use app\models\Leagues;
use app\models\Games;

class UpdateLeagueStandings extends \lithium\console\Command {

    public function run() {
        $this->updateTeamStats();
        $this->updateLeagueStandings();
    }

    protected function updateTeamStats()
    {
        // Compile a list of all teams needing an update across all leagues
        $conditions = array('stats.needs_update' => true);
        $fields = array('league_id');
        $teams_needing_update = Teams::all(compact('conditions', 'fields'));

        $team_id_list = array();
        $team_stats = array();
        foreach ($teams_needing_update as $team) {
            $team_id_list[] = $team->_id;
        }

        // Grab all of the games that apply to these teams, standings should be re-calced from the ground up.
        $conditions = array('teams' => array('$in' => $team_id_list), 'scores' => array('$exists' => true), 'winner' => array('$ne' => null));
        $fields = array('scores', 'winner', 'teams', 'league_id');
        $relevant_games_list = Games::all(compact('conditions', 'fields'));

        $league_list = array();
        foreach ($relevant_games_list as $game) {
            // Ensure we have a stats entry for each team:
            foreach ($game->getTeams() as $t) {
                $team_id = (string)$t->_id;
                if (!isset($team_stats[$team_id])) {
                    $team_stats[$team_id] = array(
                        'wins' => 0,
                        'losses' => 0,
                        'point_differential' => 0
                    );
                }

                if ($t->_id == $game->winner) {
                    $team_stats[$team_id]['wins']++;
                    $team_stats[$team_id]['point_differential'] += $game->getScoreDiff();
                } else {
                    $team_stats[$team_id]['losses']++;
                    $team_stats[$team_id]['point_differential'] -= $game->getScoreDiff();
                }
            }

            // Make sure we have a list of all of the leagues being updated
            $league_list[] = $game->league_id;
        }

        // Do the team stats updates:
        foreach ($team_stats as $team_id => $stats) {
            $conditions = array('_id' => new \MongoId($team_id));
            $query = array('$set' => array('stats' => $stats));
            Teams::update($query, $conditions);
        }

        // Mark leagues as needing an update
        $league_list = array_unique($league_list);
        $conditions = array('_id' => array('$in' => $league_list));
        $query = array('$set' => array('needs_standings_update' => true));
        Leagues::update($query, $conditions);        
    }

    protected function updateLeagueStandings()
    {
        // Compile a list of all teams needing an update across all leagues
        $conditions = array('needs_standings_update' => true);
        $fields = array('_id');
        $leagues_needing_update = Leagues::all(compact('conditions', 'fields'));
    }
}