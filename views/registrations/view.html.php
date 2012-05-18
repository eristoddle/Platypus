<?php
    use app\modules\Registrations\GRankModule;

    $dateFormat = 'l, F jS, Y';
    $timeFormat = 'g:ia';

     if (isset($registration->gRank) and isset($registration->gRank->answers->experience)) {
        $gRankMatrix = GRankModule::getQuestionMatrix($registration->gRank->answers->experience);
     }
?>
<div class="page-header"><h1>
    League Registration
    <small>View Registrant</small>
</h1></div>
<?=$this->html->link('Return to participants list', array('Leagues::participants', 'id' => $registration->league_id))?>

<div class="row">
    <div class="span2" style="font-weight: bold">&nbsp;</div>
    <div class="span4"><h3><?=$registration->user_data->firstname?> <?=$registration->user_data->middlename?> <?=$registration->user_data->lastname?></h3></div>
</div>

<div class="row">
    <div class="span6">
        <div class="row">
            <div class="span2" style="font-weight: bold">League</div>
            <div class="span4"><?=$league->name?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Status</div>
            <div class="span4"><span class="badge" style="text-transform: capitalize"><?=$registration->status?></span></div>
        </div>

<?php if ($CURRENT_USER and $CURRENT_USER->can('leagues.manage')): ?>
        <?php if (isset($registration->payment_status)): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Payment Status</div>
                <div class="span4" style="text-transform: capitalize"><?=$registration->payment_status?></div>
            </div>
        <?php endif; ?>


        <div class="row">
            <div class="span2" style="font-weight: bold">Signup Time</div>
            <div class="span4"><?=date("{$dateFormat} {$timeFormat}", $registration->signup_timestamp->sec)?></div>
        </div>

        <?php if (isset($registration->payment_timestamps->completed)): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Payment Time</div>
                <div class="span4"><?=date("{$dateFormat} {$timeFormat}", $registration->payment_timestamps->completed->sec)?></div>
            </div>
        <?php endif; ?>

        <?php if (isset($registration->payment_timestamps->refunded)): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Refund Time</div>
                <div class="span4"><?=date("{$dateFormat} {$timeFormat}", $registration->payment_timestamps->refunded->sec)?></div>
            </div>
        <?php endif; ?>
<?php endif; ?>

        <div class="row">
            <div class="span2" style="font-weight: bold">Team Style</div>
            <div class="span4" style="text-transform: capitalize">
                <?php 
                $teamStyles = array();
                foreach ($registration->team_style_pref as $k => $v) {
                    if ($v) {
                        $teamStyles[] = $k;
                    }
                } 
                if (empty($teamStyles)) {
                    $teamStyles[] = 'n/a';
                }
                echo implode(', ', $teamStyles)
                ?>
            </div>
        </div>

        <h4 style="margin-top: 15px">Player Info:</h4>
        <div class="row">
            <div class="span2" style="font-weight: bold;">Height</div>
            <div class="span4"><?=$registration->user_data->height?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold;">Weight</div>
            <div class="span4"><?=$registration->user_data->weight?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold;">Gender</div>
            <div class="span4"><?=$registration->gender?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold;">Birthdate</div>
            <div class="span4"><?=$registration->user_data->birthdate?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold;">Player Style</div>
            <div class="span4"><?=$registration->player_strength?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold;">Availability</div>
            <div class="span4">
                <?=$registration->availability->general?>
                <?php
                    $tourneys = array();
                    if (isset($registration->availability->attend_tourney_mst) and $registration->availability->attend_tourney_mst) {
                        $tourneys[] = '<abbr title="Midseason Tournament" class="initialism">MST</abbr>';
                    }
                    if (isset($registration->availability->attend_tourney_eos) and $registration->availability->attend_tourney_eos) {
                        $tourneys[] = '<abbr title="End-of-season Tournament" class="initialism">EOS</abbr>';
                    }
                    echo '(Tourneys: ' . (count($tourneys) > 0 ? implode(', ', $tourneys) : '&mdash;') . ')';
                ?>
            </div>
        </div>        
    </div><!-- End First Column -->
    <div class="span6">
        <h4>Notes:</h4>
        <div class="row">
            <div class="span4 well"><?=$registration->notes?></div>
        </div> 
        <div class="row">
            <div class="span1" style="font-weight: bold">Pair</div>
            <div class="span5"><?=($registration->pair->text ?: "n/a")?></div>
        </div>

        <h4 style="margin-top: 15px">IDs:</h4>
        <div class="row">
            <div class="span1" style="font-weight: bold">User</div>
            <div class="span5"><?=$user->_id?></div>
        </div>
        <div class="row">
            <div class="span1" style="font-weight: bold">Registration</div>
            <div class="span5"><?=$registration->_id?></div>
        </div>
    </div>
</div>

<h4 style="margin-top: 15px">Ranking Info:</h4>
<div class="row">
    <div class="span2" style="font-weight: bold">League Rank</div>
    <div class="span4"><?=($registration->secondary_rank_data->commish_rank ?: "n/a")?></div>
</div>
<div class="row">
    <div class="span2" style="font-weight: bold">Self Rank</div>
    <div class="span4"><?=($registration->secondary_rank_data->self_rank ?: "n/a")?></div>
</div>
<?php if (isset($registration->gRank)): ?>
<div class="row">
    <div class="span2" style="font-weight: bold">gRank</div>
</div>
<div class="row">
    <dl class="dl-horizontal well span6">
        <dt>Experience</dt>
        <dd><?=$gRankMatrix['desc']?></dd>

        <?php if (!empty($gRankMatrix['questions']['level_of_play'])): ?>
            <dt>Level of Play</dt>
            <dd><?=$gRankMatrix['questions']['level_of_play'][$registration->gRank->answers->level_of_play]['text']?></dd>
        <?php endif; ?>

        <?php if (!empty($gRankMatrix['questions']['athleticism'])): ?>
            <dt>Athleticism</dt>
            <dd><?=$gRankMatrix['questions']['athleticism'][$registration->gRank->answers->athleticism]['text']?></dd>
        <?php endif; ?>

        <?php if (!empty($gRankMatrix['questions']['ultimate_skills'])): ?>
            <dt>Skill</dt>
            <dd><?=$gRankMatrix['questions']['ultimate_skills'][$registration->gRank->answers->ultimate_skills]['text']?></dd>
        <?php endif; ?>

        <dt>Score</dt>
        <dd><?=$registration->secondary_rank_data->grank?></dd>
    </dl>
</div>
<?php endif; ?>
<pre style="margin-top: 30px"><?php print_r($registration->to('array')); ?></pre>