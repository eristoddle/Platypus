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
    <small>Edit Registrant</small>
</h1></div>
<p><?=$this->html->link('Return to participants list', array('Leagues::participants', 'id' => $registration->league_id))?></p>
<p><?=$this->html->link('Return to registrant detail', array('Registrations::view', 'id' => $registration->_id))?></p>
