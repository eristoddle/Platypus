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

                if (isset($registration->team_style_pref)) {
                    foreach ($registration->team_style_pref as $k => $v) {
                        if ($v) {
                            $teamStyles[] = $k;
                        }
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

        <h4 style="margin-top: 15px">Ranking Info:</h4>
        <div class="row">
            <div class="span2" style="font-weight: bold">League Rank</div>
            <div class="span4"><?=(isset($registration->secondary_rank_data->commish_rank) ? $registration->secondary_rank_data->commish_rank : "n/a")?></div>
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
            <dl class="dl-horizontal well span5">
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

    </div><!-- End First Column -->
    <div class="span6">
        <h4>Notes:</h4>
        <div class="row">
            <div class="span4 well"><?=$registration->notes?></div>
        </div> 
        <div class="row">
            <div class="span1" style="font-weight: bold">Pair</div>
            <div class="span5"><?=isset($registration->pair->text) ? $registration->pair->text : "n/a"?></div>
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
        <?php if ($CURRENT_USER and $league->isManager($CURRENT_USER)): ?>
            <h4 style="margin-top: 15px">Edit Registrant:</h4>
            <div class="row">    
                <div class="span5">
                    <?=$this->form->create($registration, array('class' => 'form-horizontal well'))?>
                    <?=$this->security->requestToken();?>
                    <?php if (strtolower($registration->status) == 'active'): ?>
                    <div class="alert alert-warning">
                        <p>
                            <strong>Caution:</strong>
                            Always move a player's replacement from the waitlist into a full league before you change the status of the canceled player. 
                            <a href="#" class="hasPopover" rel="popover" 
                                data-original-title="Waitlist Procedure"
                                data-content="
                                    <p>
                                        Changing a registrant status from 'Active' to 'Pending' or 'Canceled' will remove them from the league.
                                        If the league is full, this will open a spot for a new registrant but will not fill that spot in from the waitlist, meaning a new 
                                        registrant would be more likely to get that spot than someone on the waitlist.
                                    </p>
                                    <br />
                                    <p>
                                        To maintain control over who gets the spot being opened by moving a player from Active status, you should first move a player into
                                        the league off of the waitlist and <em>then</em> remove the canceled player from the league.
                                    </p>
                                "
                            >
                                Tell me why!
                            </a>
                        </p>
                    </div>
                    <?php endif; ?>
                    <?=$this->form->field('status', array('type' => 'select', 'label' => 'League Status', 'empty' => true))?>
                    <?=$this->form->field('secondary_rank_data.commish_rank', array('type' => 'select', 'label' => 'Official League Rank', 'empty' => 'Not set', 
                        'list' => array('0' => '0.0', '0.5' => '0.5', '1'   => '1.0', '1.5' => '1.5', '2'   => '2.0', '2.5' => '2.5', '3'   => '3.0', '3.5' => '3.5', '4'   => '4.0', '4.5' => '4.5', 
                        '5'   => '5.0', '5.5' => '5.5', '6'   => '6.0', '6.5' => '6.5', '7'   => '7.0', '7.5' => '7.5', '8'   => '8.0', '8.5' => '8.5', '9'   => '9.0', '9.5' => '9.5') ))?>
                    <?=$this->form->field('Update Registration', array('type' => 'submit-button', 'class' => 'btn btn-primary', 'label' => ''))?>
                    <?=$this->form->end()?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>