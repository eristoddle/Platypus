<?php
    $buttonValues = array(
        'edit' => 'Update League',
        'create' => 'Create League'
    );
?>
<?=$this->form->create($league, array('class' => 'form-horizontal'))?>
<?=$this->security->requestToken();?>
<?=$this->form->field('name')?>
<?=$this->form->field('sport', array('type' => 'select', 'empty' => true))?>
<?=$this->form->field('age_division', array('type' => 'select', 'empty' => true))?>
<?=$this->form->field('season', array('type' => 'select', 'empty' => true))?>
<?=$this->form->field('player_limit.male', array('label' => 'Male Limit'))?>
<?=$this->form->field('player_limit.female', array('label' => 'Female Limit'))?>
<?=$this->form->field('start_date', array('type' => 'date', 'value' => date('Y-m-d', $league->start_date? $league->start_date->sec : strtotime('+1 Month'))))?>
<?=$this->form->field('end_date', array('type' => 'date', 'value' => date('Y-m-d', $league->end_date? $league->end_date->sec : strtotime('+4 Months'))))?>
<?=$this->form->field('price', array('type' => 'money'))?>
<?=$this->form->field('registration_open', array('type' => 'date', 'value' => date('Y-m-d', $league->registration_open? $league->registration_open->sec : strtotime('+1 Week'))))?>
<?=$this->form->field('registration_close', array('type' => 'date', 'value' => date('Y-m-d', $league->registration_close? $league->registration_close->sec : strtotime('+3 Weeks'))))?>
<?=$this->form->moduleOptions('\app\models\Registrations')?>
<?=$this->form->field('description', array('type' => 'textarea'))?>
<?=$this->form->field($buttonValues[$this->request()->action], array('type' => 'submit-button', 'class' => 'btn btn-primary', 'label' => ''))?>
<?=$this->form->end()?>
