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
<?php registrantTable($active_list, 'active'); ?>

<h2>Inactive Registrations</h2>
<?php registrantTable($pending_list, 'inactive'); ?>

<?php function registrantTable($reg_list, $type = 'active') { ?>
<?php
    $tableContents = '';
    $counts = array('male' => 0, 'female' => 0, 'undefined' => 0);
    foreach($reg_list as $reg) {
        if (isset($reg->gender)) {
            $counts[$reg->gender]++;
        } else {
            $counts['undefined']++;
        }
        $tableContents .= registrantRow($reg, $type); 
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
    echo registrantRow(null, $type);
    echo '<tbody>';
    echo $tableContents;
    echo '</tbody>';
?>
</table>
<?php } ?>
<?php

    function registrantRow($regObject = null, $type = 'active') {
        if (is_null($regObject)) {
            $headerRow  = '<thead><tr>';
            $headerRow .= '<th>&nbsp;</th>';
            $headerRow .= '<th>Gender</th>';
            $headerRow .= '<th>First Name</th>';
            $headerRow .= '<th>Middle Name</th>';
            $headerRow .= '<th>Last Name</th>';
            $headerRow .= $type == 'active' ? '<th>Rank</th>' : '';
            $headerRow .= '<th>Pair</th>';
            $headerRow .= $type == 'active' ? '<th>Player Type</th>' : '';
            $headerRow .= $type == 'active' ? '<th>Attendance</th>' : '';
            $headerRow .= $type == 'active' ? '<th>Tourneys</th>' : '';
            $headerRow .= $type == 'inactive' ? '<th>Payment Status</th>' : '';
            $headerRow .= $type == 'inactive' ? '<th>Payment Auth Date</th>' : '';
            $headerRow .= '</tr></thead>' . "\n";
            return $headerRow;
        }

        $out  = '<tr>';
        $out .= '<td><a href="/registrations/view/' . $regObject->_id . '"><i class="icon-exclamation-sign"></i></a></td>';
        
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

        if ($type == 'active') {
            $out .= '<td>' . $regObject->getOfficialRank() . '</td>';    
        }
        
        $out .= '<td>' . (isset($regObject->pair->text) ? $regObject->pair->text : '&mdash;') . '</td>';

        if ($type == 'active') {
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
        }

        if ($type == 'inactive') {
            $out .= '<td style="text-transform: capitalize">' . $regObject->status . ' (' . (isset($regObject->payment_status) ? $regObject->payment_status : 'not paid') . ')</td>'; 
            $out .= '<td>' . (isset($regObject->payment_timestamps->pending) ? date('Y-m-d H:i:s', $regObject->payment_timestamps->pending->sec) : '&mdash;') . '</td>'; 
        }

        $out .= "</tr>\n";

        return $out;
    }
?>