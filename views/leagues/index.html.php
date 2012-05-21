<?php
    use app\models\Leagues;
?>
<div class="page-header"><h1>
    AFDC Leagues
    <small>League Listing</small>
</h1></div>

<?php 
    if (isset($CURRENT_USER) and $CURRENT_USER->can('leagues.edit')) {

    echo '<p>' . $this->html->link(
        '<i class="icon-plus icon-white"></i> Create a new league', 
        'Leagues::create', 
        array('class' => 'btn btn-primary', 'escape' => false)) . '</p>';

    } 

    $league_types = array(
        'future' => 'Upcoming Leagues',
        'present' => 'Current Leagues',
        'past' => 'Past Leagues'
    );

    foreach ($league_types as $key => $label) {
        if (count($leagues[$key]) <= 0) {
            continue;
        }

        echo "<h2>{$label}</h2>\n";
        echo '<table class="table table-striped tablesorter">';
            echo '<thead><tr><th width="7%">Action</th><th>League Name</th><th width="15%">Season</th><th width="15%">Sport</th><th width="15%">Commissioners</th><th width="15%">Start</th><th width="15%">End</th></tr></thead>';
            echo '<tbody>';
        foreach ($leagues[$key] as $l) {
            $this_league = Leagues::find($l['_id']);
            $league_link = $this->html->link($l['name'], array('Leagues::view', 'id' => $l['_id']));

            $actionLinks = array();

            if (isset($CURRENT_USER)) {
                $actionLinks[] = $this->html->link('<i class="icon-list"></i>', array('Leagues::participants', 'id' => $l['_id']), array('escape' => false, 'class' => 'hasTooltip', 'title' => 'Participant List'));    
            } else {
                $actionLinks[] = '<i class="icon-list hasTooltip" title="Please log in to view the participant list."></i>';
            }
            
            if (isset($CURRENT_USER) and $this_league->isManager($CURRENT_USER)) {
                $actionLinks[] = $this->html->link('<i class="icon-pencil"></i>', array('Leagues::edit', 'id' => $l['_id']), array('escape' => false, 'class' => 'hasTooltip', 'title' => 'Edit League'));
            }

            if ($l['meta']['registration_open'] and isset($CURRENT_USER) and $CURRENT_USER->can('leagues.register')) {
                $actionLinks[] = $this->html->link('<i class="icon-shopping-cart"></i>', array('Leagues::register', 'id' => $l['_id']), array('escape' => false, 'class' => 'hasTooltip', 'title' => 'Register'));
            }
            
            echo '<tr>';
                echo '<td>' . implode('&nbsp;', $actionLinks) . '</td>';
                echo "<td>{$league_link}</td>";
                echo "<td>{$l['season']}</td>";
                echo "<td>{$l['sport']}</td>";
                echo "<td>{$l['commissioners']}</td>";
                echo "<td>" . date('Y-m-d', $l['start_date']) . "</td>";
                echo "<td>" . date('Y-m-d', $l['end_date']) . "</td>";
            echo '</tr>';
        }
        echo '</tbody>';
        echo "</table>";
    }
?>