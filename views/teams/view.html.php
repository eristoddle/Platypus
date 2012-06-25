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
            <?php if ($team->stats->needs_update == true): ?>
            <div class="row">
                <div class="span6">
                    <div class="alert alert-info">Scores have been reported that are not reflected above.</div>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="row">
                <div class="span6">
                    <div class="alert alert-warning">Statistics not available.</div>
                </div>
            </div>
        <?php endif; ?>
        <hr />
        <div class="row">
            <div class="span6">
                <table class="table table-striped tablesorter">
                    <thead><tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Opponent</th>
                        <th>Field</th>
                        <th>Score</th>
                    </tr></thead>
                    <tbody><?php foreach ($team->getGames() as $g): ?>
                            <?php 
                                $fs = $g->getFieldSite(); 
                                $opp = $g->getOpponent($team->_id); 

                                if ((is_object($g->game_time) and ($g->game_time->sec <= time())) and $g->canReport($CURRENT_USER)) {
                                    $reportScoreLink = $this->html->link('<i title="Report Score" class="hasTooltip icon-edit"></i>', array('Leagues::reportScore', 'id' => $g->_id), array('escape' => false));
                                } else {
                                    $reportScoreLink = '';
                                }
                            ?>
                        <tr style="text-transform: capitalize">
                            <td><?=date('Y M jS (D)', $g->game_time->sec)?></td>
                            <td><?=date('g:ia', $g->game_time->sec)?></td>
                            <td><?=$this->html->link($opp->name, array('Teams::view', 'id' => $opp->_id))?></td>
                            <td><?=$fs->name?></td>
                            <?php if (is_object($g->scores)): ?>
                            <?php
                                $scores = $g->scores->to('array'); 

                                $my_score = $scores[(string) $team->_id];
                                $opp_score = $scores[(string) $opp->_id];

                                $icon_class = '';
                                if ($my_score > $opp_score) {
                                    $bg_color   = '#468847';
                                    $color      = '#FFFFFF';
                                    $icon_class = 'icon-white';
                                } else if ($opp_score > $my_score) {
                                    $bg_color   = '#B94A48';
                                    $color      = '#FFFFFF';
                                    $icon_class = 'icon-white';
                                }
                            ?>
                                <td style="background-color: <?=$bg_color?>; color: <?=$color?>;"><?=$reportScoreLink?> <?=$my_score?> - <?=$opp_score?></td>
                            <?php else: ?>
                                <td><?=$reportScoreLink?> n/a</td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?></tbody>
                </table>
            </div>
        </div>
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