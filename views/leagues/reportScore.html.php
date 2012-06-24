<div class="page-header"><h1>
    AFDC Leagues
    <small>Report Scores</small>
</h1></div>
<h2><?=$league->name?></h2>
<?php
    $team_link_list = array();

    foreach ($teams as $t) {
        $team_link_list[] = $this->html->link($t->name, array('Teams::view', 'id' => $t->_id));
    }
?>
<h3><?=implode(' vs. ', $team_link_list)?></h3>
<?php 
    $fieldsite = $game->getFieldsite(); 
    $reporter  = $game->getReporter();
?>
<h4 style="margin-bottom: 15px"><?=date('g:ia, D, M jS, Y', $game->game_time->sec)?> at <?=$fieldsite->name?></h4>

<?=$this->form->create($game, array('class' => 'form-horizontal'))?>
<?=$this->security->requestToken();?>
<?php foreach ($teams as $t): ?>
    <?=$this->form->field('scores.' . $t->_id, array('type' => 'text', 'class' => 'span1', 'label' => $t->name))?>
<?php endforeach; ?>
<?=$this->form->field('Update Score', array('type' => 'submit-button', 'class' => 'btn btn-primary', 'label' => ''))?>
<?=$this->form->end()?>
<?php if (isset($reporter)): ?>
    <span style="font-style: italic">Score reported by: <?=$reporter->firstname?> <?=$reporter->lastname?></span>
<?php endif; ?>


<pre class="debug hide">
<?php
    print_r($game->to('array'));

    print_r($league->to('array'));
?>
</pre>