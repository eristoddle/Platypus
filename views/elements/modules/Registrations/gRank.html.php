<?php
    use app\modules\Registrations\GRankModule;

    $modalTitle = 'AFDC Ranking Questionaire for Ultimate Leagues';

    $gRankMatrix = GRankModule::getQuestionMatrix();
?>
<div id="gRankContainer">
    <div class="control-group">
        <label class="control-label" for="optionsCheckboxList">gRank Survey</label>
        <div class="controls">
            <div class="uneditable-input" id="gRank-display-status"></div>
            <div class="uneditable-input hide" id="gRank-display-experience"></div>
            <?=$this->form->hidden('gRank.answers.experience', array('id' => 'gRank-answers-experience'))?>
            <?=$this->form->hidden('gRank.score', array('id' => 'gRank-score'))?>
            <span class="control-group error"><?=$this->form->error('gRank.score')?></span><br />

            <?php
                foreach (GRankModule::getQuestionCategories() as $catName) {
                    echo $this->form->hidden("gRank.answers.{$catName}", array('id' => "gRank-answers-{$catName}"));
                    # echo '<div class="uneditable-input hide" id="gRank-display-' . $catName . '"></div>';
                }
            ?>
            <a class="btn" href="#" id="gRankShow"></a>
        </div>
    </div>
</div>
<!-- Step Zero: The Disclaimer and Instructions -->
<div class="modal hide fade gRankModal" id="gRankStepZero">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?=$modalTitle?></h3>
  </div>
  <div class="modal-body">
    <h4>Purpose</h4>
    <p>The purpose of this questionaire is to gather data so we can most accurately assess your abilities.</p>
    <h4>Important Notes:</h4>
    <ul>
        <li style="margin: 10px">These answers will be public -- please answer all questions honestly and with spirit of the game in mind.</li>
        <li style="margin: 10px">When choosing your level of experience, please select <strong>CLUB</strong> if and only if:
            <ol>
                <li>You have <strong>completed</strong> 1 season on a registered USAU club team, and</li>
                <li>You have played on a registered USAU Club team in the last three years (i.e., for the 2009, 2010, or 2011 series).</li>
            </ol>
        </li>
        <li style="margin: 10px">When choosing your level of experience, please select <strong>COLLEGE</strong> if and only if:
            <ol>
                <li>You have <strong>completed</strong> 1 season on a registered USAU college team, and</li>
                <li>You have played on a registered USAU college team in the last three years (i.e., for the 2009, 2010, or 2011 series).</li>
            </ol>
        </li>
    </ul>
    <p></p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-primary" id="s0-next">Agree and continue <i class="icon-chevron-right icon-white"></i></a>
  </div>
</div>

<!-- Step One: Level of Experience -->
<div class="modal hide fade gRankModal form" id="gRankStepOne">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?=$modalTitle?></h3>
  </div>
  <div class="modal-body">
    <h4>What is the highest level of ultimate you have played <em>in the last 3 years</em>?</h3>
    <div class="control-group">
        <div class="controls" id="experienceOptionList">
        <?php foreach ($gRankMatrix as $name => $matrix): ?>
            <label class="radio">
                <input type="radio" name="expOption" id="exp-option-<?=$name?>" value="<?=$name?>">
                <?=$matrix['desc']?>
            </label>
        <?php endforeach; ?>
        </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" id="s1-back"><i class="icon-chevron-left"></i> Back</a>
    <a href="#" class="btn btn-primary hide" id="s1-next">Next <i class="icon-chevron-right icon-white"></i></a>
  </div>
</div>

<!-- Step Two: Level of play, athleticism, and skill -->
<div class="modal hide fade gRankModal" id="gRankStepTwo" data-section="xx">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?=$modalTitle?></h3>
    <div style="text-align: right;"><span id="gRankRunningTotal" class="badge">Approximate Rank: <span id="gRankRunningTotalValue">0.0</span></span></div>
  </div>

  <div class="modal-body">
    <div class="control-group">
        <?php foreach ($gRankMatrix as $name => $matrix): ?>
        <div class="controls hide" id="stepTwoQuestions-<?=$name?>">
            <?php foreach ($matrix['questions'] as $category => $questions): ?>
            <?php if (count($questions) <= 0) { continue; } ?>
            <div style="margin-bottom: 16px">
                <h4><?=\lithium\util\Inflector::humanize($category)?></h4>
                <div class="questionGroup">
                    <?php foreach ($questions as $key => $question): ?>
                        <label class="radio">
                            <input type="radio" name="grank-<?=$name?>-<?=$category?>" data-category="<?=$category?>" data-index="<?=$key?>" data-score="<?=$question['score']?>" value="<?=$name?>">
                            <?=$question['text']?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
  </div>

  <div class="modal-footer">
    <a href="#" class="btn" id="s2-back"><i class="icon-chevron-left"></i> Back</a>
    <a href="#" class="btn btn-primary hide" id="s2-next">Finish <i class="icon-ok icon-white"></i></a>
  </div>
</div>
<?php
    $this->html->script('gRank', array('inline' => false));
?>