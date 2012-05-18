<div class="page-header"><h1>
    League Registration
    <small><?=$league->name?></small>
</h1></div>

<h3><?=$league->name?></h3>
<div class="row">
    <div class="span5">
        <div class="row">
            <div class="span2" style="font-weight: bold">Sport:</div>
            <div class="span3" style="text-transform: capitalize"><?=$league->season?> <?=$league->sport?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Price:</div>
            <div class="span3"><?=money_format('%n', $league->price)?></div>
        </div>
        <?php if ($league->start_date and $league->end_date): ?>
        <div class="row">
            <div class="span2" style="font-weight: bold">Dates:</div>
            <div class="span3"><?=date('M jS, Y', $league->start_date->sec)?> to <?=date('M jS, Y', $league->end_date->sec)?></div>
        </div>
        <?php endif; ?>
        <?php if ($league->description): ?>
        <div class="row">
            <div class="span2" style="font-weight: bold">Description:</div>
            <div class="span3"><?php echo nl2br($league->description); ?></div>
        </div>
        <?php endif; ?>

        <h3 style="margin-top: 24px">Your Registration</h3>
        <?php if ($registration->exists()): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Status:</div>
                <div class="span3" style="text-transform: capitalize"><span class="badge badge-<?=($registration->status == 'active' ? 'success' : 'important')?>"><?=$registration->status?></span></div>
            </div>
            <div class="row">
                <div class="span2" style="font-weight: bold">Payment:</div>
                <div class="span3"><?=$registration->paid ? 'Paid' : 'Not paid'?></div>
            </div>
            <div class="row">
                <div class="span2" style="font-weight: bold">League Rank:</div>
                <div class="span3"><?=$registration->getOfficialRank()?></div>
            </div>
            <?php if (isset($registration->secondary_rank_data->commish_rank)): ?>
                <div class="row">
                    <div class="span1 offset1" style="font-weight: bold">Commish:</div>
                    <div class="span3"><?=$registration->secondary_rank_data->commish_rank?></div>
                </div>
            <?php endif; ?>
            <?php if (isset($registration->secondary_rank_data->grank)): ?>
                <div class="row">
                    <div class="span1 offset1" style="font-weight: bold">gRank:</div>
                    <div class="span3"><?=$registration->secondary_rank_data->grank?></div>
                </div>
            <?php endif; ?>
            <?php if (isset($registration->secondary_rank_data->self_rank)): ?>
                <div class="row">
                    <div class="span1 offset1" style="font-weight: bold">Self:</div>
                    <div class="span3"><?=$registration->secondary_rank_data->self_rank?></div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            You are not yet registered.
        <?php endif; ?>
    </div>
    <div class="span7">
        <?=$this->form->create($registration, array('class' => 'form-horizontal'))?>
        <?=$this->security->requestToken();?>
        <?=$this->form->field('availability.general', array('type' => 'select', 'label' => 'Attendance', 'empty' => true))?>
        <?=$this->form->field('secondary_rank_data.self_rank', array('type' => 'select', 'label' => 'Player Rank', 'empty' => true))?>
        <?=$this->form->field('player_strength', array('type' => 'select', 'label' => 'Primary Role', 'empty' => true))?>
        <?=$this->form->field('pair.text', array('type' => 'text', 'label' => 'Requested Pair'))?>

        <!-- Tournament Attendance -->
        <div class="control-group">
            <label class="control-label" for="optionsCheckboxList">Tournament Attendance</label>
            <div class="controls">
                <?php if ($league->season === 'summer' and $league->sport === 'ultimate'): ?>
                    <label class="checkbox">
                        <?=$this->form->checkbox('availability.attend_tourney_mst')?>
                        Midseason
                    </label>
                <?php endif; ?>
                <label class="checkbox">
                    <?=$this->form->checkbox('availability.attend_tourney_eos')?>
                    End of Season
                </label>
            </div>
        </div>

        <!-- Team Preferences -->
          <div class="control-group">
            <label class="control-label" for="optionsCheckboxList">Team Style Preference</label>
            <div class="controls">
              <label class="checkbox">
                <?=$this->form->checkbox('team_style_pref.competitive')?>
                Competitive
              </label>
              <label class="checkbox">
                <?=$this->form->checkbox('team_style_pref.social')?>
                Social
              </label>
              <label class="checkbox">
                <?=$this->form->checkbox('team_style_pref.family')?>
                Family-friendly
              </label>
              <p class="help-block"><strong>Note:</strong> These preferences are to help captains draft appropriately, and cannot be guaranteed.</p>
            </div>
          </div>
        <!-- Modules -->
        <?php
            $potentialModules = $registration->modules();

            foreach ($potentialModules as $name => $config) {
                if (isset($league->modules->{$name}) and $league->modules->{$name}) {
                    if (isset($config['partial'])) {
                        echo $this->_render('element', $config['partial']);
                    }
                }
            }
        ?>
        <?=$this->form->field('notes', array('type' => 'textarea', 'label' => 'Notes'))?>
        <?php
            if ($registration->exists()) {
                $buttonText = 'Update Registration';
            } else {
                $buttonText = 'Register';
            }
            

        ?>
        <?=$this->form->field($buttonText, array('type' => 'submit-button', 'class' => 'btn btn-primary', 'label' => ''))?>
        <?=$this->form->end()?>
    </div>
</div>
