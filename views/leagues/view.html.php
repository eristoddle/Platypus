<?php
    $dateFormat = 'l, F jS, Y';
    $timeFormat = 'g:ia';

    $commissioners = $league->getCommissioners();
?>
<h1>League Details</h1>
<?php echo $this->html->link('Return to league list', 'Leagues::index'); ?>
<hr />
<div class="row">
    <div class="span2" style="font-weight: bold">&nbsp;</div>
    <div class="span4" style="text-transform: capitalize;"><h3><?=$league->name?></h3></div>
</div>
<div class="row">
    <div class="span6">
        <div class="row">
            <div class="span2" style="font-weight: bold">Sport</div>
            <div class="span4" style="text-transform: capitalize;"><?=$league->age_division?> <?=$league->sport?> (<?=$league->season?>)</div>
        </div>
        <?php if ($commissioners): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Commissioners</div>
                <div class="span4" style="text-transform: capitalize;">
                <?php
                    foreach ($commissioners as $c) {
                        echo $c->firstname . ' ' . $c->lastname . '<br />';
                    }
                ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="span2" style="font-weight: bold">Price</div>
            <div class="span4" style="text-transform: capitalize;"><?=money_format('%n', $league->price)?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Registration Opens</div>
            <div class="span4" style="text-transform: capitalize;"><?=date("{$dateFormat}, {$timeFormat}", $league->registration_open->sec)?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Registration Closes</div>
            <div class="span4" style="text-transform: capitalize;"><?=date("{$dateFormat}, {$timeFormat}", $league->registration_close->sec)?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Play Begins</div>
            <div class="span4" style="text-transform: capitalize;"><?=date($dateFormat, $league->start_date->sec)?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Play Ends</div>
            <div class="span4" style="text-transform: capitalize;"><?=date($dateFormat, $league->end_date->sec)?></div>
        </div>
        <?php if (isset($league->player_limit)): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Player Limits</div>
                <div class="span4" style="text-transform: capitalize;">
                    <?php
                        $limits = array();
                        if (isset($league->player_limit->male)) {
                            $limits[] = $league->player_limit->male . ' men';
                        }

                        if (isset($league->player_limit->female)) {
                            $limits[] = $league->player_limit->female. ' women';
                        }

                        if (empty($limits)) {
                            $limits[] = '&mdash;';
                        }

                        echo implode(', ', $limits);
                    ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($league->description): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Description:</div>
                <div class="span3"><?php echo nl2br($league->description); ?></div>
            </div>
        <?php endif; ?>
        <?php if (isset($CURRENT_USER) and $CURRENT_USER->can('leagues.edit')): ?>
            <div class="row" style="margin-top: 5px;">
                <div class="span2" style="font-weight: bold">&nbsp;</div>
                <div class="span3">
                    <?=$this->html->link('<i class="icon-pencil icon-white"></i> Edit League', array('Leagues::edit', 'id' => (string) $league->_id), array('escape' => false, 'class' => 'btn btn-large btn-primary'))?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($league->registrationOpen() and isset($CURRENT_USER) and $CURRENT_USER->can('leagues.register')): ?>
            <div class="row" style="margin-top: 5px;">
                <div class="span2" style="font-weight: bold">&nbsp;</div>
                <div class="span3">
                    <?=$this->html->link('<i class="icon-shopping-cart icon-white"></i> Register Now', array('Leagues::register', 'id' => (string) $league->_id), array('escape' => false, 'class' => 'btn btn-large btn-primary'))?>
                </div>
            </div>
        <?php elseif ($league->registrationOpen() and !isset($CURRENT_USER)): ?>
            <div class="row" style="margin-top: 10px">
                <div class="span4 alert alert-error">
                    You must log in to register.
                </div>
            </div>
        <?php endif; ?>
    </div><!-- End First Column -->
    <div class="span6">
        <?php $teams = $league->getTeams(); ?>
        <?php if ($teams): ?>
        <h4>Teams:</h4>
        <table class="table table-striped tablesorter">
            <thead><tr>
                <th>Rank</th>
                <th>Team</th>
                <th>Wins</th>
                <th>Losses</th>
                <th>Pct</th>
                <th>Pt. Diff.</th>
            </tr></thead>
            <tbody><?php foreach ($teams as $t): ?>
                <tr>
                    <td><?=$t->stats->rank?></td>
                    <td><?=$t->name?></td>
                    <td><?=$t->stats->wins?></td>
                    <td><?=$t->stats->losses?></td>
                    <td><?=number_format($t->stats->wins / ($t->stats->losses + $t->stats->wins), 3)?></td>
                    <td><?=$t->stats->point_differential?></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<pre class="hide" id="debug">
<?php
    var_dump($league->to('array'));
?>
</pre>
