<div class="page-header"><h1>
    AFDC Teams
    <small>team details</small>
</h1></div>
<div class="row">
    <div class="span2" style="font-weight: bold">&nbsp;</div>
    <div class="span4"><h3><?=$team->name?></h3></div>
</div>
<div class="row">
    <div class="span6">
        <div class="row">
            <div class="span2" style="font-weight: bold">Captains:</div>
            <div class="span4">
                <?php
                    $captains = $team->getCaptains();
                    $c_list = array();

                    foreach($captains as $c) {
                        $c_list[] = $c->firstname . ' ' . $c->lastname;
                    }

                    if (count($c_list) == 0) {
                        $c_list[] = 'None Listed';
                    }

                    echo implode(', ', $c_list);
                ?>
            </div>
        </div>
        <?php if (is_object($team->stats)): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">League Rank:</div>
                <div class="span4"><?=$team->stats->rank?></div>
            </div>
            <div class="row">
                <div class="span2" style="font-weight: bold">League Record:</div>
                <div class="span4">
                    <?=$team->stats->wins?>-<?=$team->stats->losses?>
                    <?php if ($team->stats->losses + $team->stats->wins > 0): ?>(<?=number_format($team->stats->wins / ($team->stats->losses + $team->stats->wins), 3)?>)<?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="span2" style="font-weight: bold">Point Diff.:</div>
                <div class="span4"><?=sprintf("%+d", $team->stats->point_differential)?></div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="span6">
                    <div class="alert alert-warning">Statistics not available.</div>
                </div>
            </div>
        <?php endif; ?>
    </div><!-- End of First Column -->
    <div class="span6">
        <h4>Roster:</h4>
        <?php if (isset($CURRENT_USER)): ?>
            <?php 
                $playerList = $team->getPlayers();
            ?>
            <?php if (isset($playerList) and $playerList->count() > 0): ?>
            <table class="table table-striped tablesorter">
                <thead><tr>
                    <th>First Name</th>
                    <th>Middle</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Email Address</th>
                </tr></thead>
                <tbody><?php foreach ($playerList as $u): ?>
                    <tr style="text-transform: capitalize">
                        <td><?=$u->firstname?></td>
                        <td><?=$u->middlename?></td>
                        <td><?=$u->lastname?></td>
                        <td><?=$u->gender?></td>
                        <td style="text-transform: lowercase"><?=$u->email_address?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table>
            <?php else: ?>
                <div class="alert alert-warning">No team roster found.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">You must be logged in to view team rosters.</div>
        <?php endif; ?>
    </div>
</div>
<pre class="hide debug">
<?php
    var_dump($team->to('array'));
?>
</pre>