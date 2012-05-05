<?php
    if (isset($CURRENT_USER) and $CURRENT_USER->can('registrants.view_details')) {
        $linkIds = true;
    } else {
        $linkIds = false;
    }
?>
<div class="page-header"><h1>
    <?=$league->name?>
    <small>League Registrants</small>
</h1></div>

<p><?=$this->html->link('Return to league listing', 'Leagues::index')?></p>

<h2>Active Registrations</h2>
<?php registrantTable($active_list, false, $linkIds); ?>

<h2>Inactive Registrations</h2>
<?php registrantTable($pending_list, true, $linkIds); ?>

<?php function registrantTable($reg_list, $showStatus = false, $linkIds = false) { ?>
<?php
    $tableContents = '';
    $counts = array('male' => 0, 'female' => 0, 'undefined' => 0);
    foreach($reg_list as $reg) {
        if (isset($reg->gender)) {
            $counts[$reg->gender]++;
        } else {
            $counts['undefined']++;
        }
        $tableContents .= registrantRow($reg, $showStatus, $linkIds); 
    }
?>
<p>
    <span class="badge">Men: <?php echo $counts['male']; ?></span>
    <span class="badge">Women: <?php echo $counts['female']; ?></span>
    <?php
        if ($counts['undefined'] > 0) {
            echo '<span class="badge">Unlisted: ' . $counts['undefined'] . '</span>';
        }
    ?>
    <span class="badge badge-inverse">Total: <? echo array_sum($counts); ?></span>
</p>
<table class="table table-striped tablesorter">
<?php 
    echo registrantRow(null, $showStatus, $linkIds);
    echo '<tbody>';
    echo $tableContents;
    echo '</tbody>';
?>
</table>
<?php } ?>
<?php

    function registrantRow($regObject = null, $showStatus = false, $linkIds = false) {
        if (is_null($regObject)) {
            $headerRow = '<thead><tr><th width="15%">ID</th><th>Gender</th><th>First Name</th><th>Middle Name</th><th>Last Name</th>' . ($showStatus ? '<th>Payment Status</th>' : '') . '<th width="8%">Rank</th><th>Pair</th><th>Player Type</th><th>Attendance</th><th>Tourneys</th><th width="10%">Signup Date</th></tr></thead>' . "\n";
            return $headerRow;
        }

        $out  = '<tr>';
        if ($linkIds) {
            $out .= '<td><a href="/registrations/view/' . $regObject->_id . '">' . $regObject->_id . '</a></td>';
        } else {
            $out .= '<td>' . $regObject->_id . '</td>';    
        }
        
        $out .= '<td>' . $regObject->gender . '</td>';

        if (isset($regObject->user_data)) {
            $ud = $regObject->user_data;
            $out .= '<td>' . (isset($ud->firstname) ? $ud->firstname : '&nbsp') . '</td>';
            $out .= '<td>' . (isset($ud->middlename) ? $ud->middlename : '&nbsp') . '</td>';
            $out .= '<td>' . (isset($ud->lastname) ? $ud->lastname : '&nbsp') . '</td>';
        } else {
            #TODO: Do query for user here
            $out .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
        }

        if ($showStatus) {
            $out .= '<td style="text-transform: capitalize">' . $regObject->status . ' (' . (isset($regObject->payment_status) ? $regObject->payment_status : 'not paid') . ')</td>'; 
        }

        $out .= '<td>' . $regObject->getOfficialRank() . '</td>';
        $out .= '<td>' . (isset($regObject->pair->text) ? $regObject->pair->text : '&mdash;') . '</td>';
        $out .= '<td>' . (isset($regObject->player_strength) ? $regObject->player_strength : '&mdash;')  . '</td>';
        $out .= '<td>' . (isset($regObject->availability->general) ? $regObject->availability->general : '&mdash;')  . '</td>';

        #Tourney
        $tourneys = array();
        if (isset($regObject->availability->attend_tourney_mst) and $regObject->availability->attend_tourney_mst) {
            $tourneys[] = '<abbr title="Midseason Tournament" class="initialism">MST</abbr>';
        }
        if (isset($regObject->availability->attend_tourney_eos) and $regObject->availability->attend_tourney_eos) {
            $tourneys[] = '<abbr title="End-of-season Tournament" class="initialism">EOS</abbr>';
        }
        $out .= '<td>' . (count($tourneys) > 0 ? implode(', ', $tourneys) : '&mdash;') . '</td>';

        $out .= '<td>' . (isset($regObject->signup_timestamp) ? date('Y-m-d', $regObject->signup_timestamp->sec) : '0000-00-00') . '</td>';
        $out .= "</tr>\n";

        return $out;
    }
?>