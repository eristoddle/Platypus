<?php
    $buttonValues = array(
        'edit' => 'Update Profile',
        'create' => 'Register!'
    );

    if (!isset($full)) {
        $full = true;
    }

    if (!isset($showPassword)) {
        $showPassword = true;
    }
?>
<?=$this->form->create($user, array('class' => 'form-horizontal'))?>
<?=$this->security->requestToken();?>
<?=$this->form->field('gender', array('type' => 'select', 'empty' => true))?>
<?=$this->form->field('firstname', array('label' => 'First name'))?>
<?php if($full): ?> 
    <?=$this->form->field('middlename', array('label' => 'Middle name'))?>
<?php endif; ?>
<?=$this->form->field('lastname', array('label' => 'Last name'))?>
<?=$this->form->field('email_address')?>

<?php if($full): ?> 
    <?=$this->form->field('birthdate', array('class' => 'date-field', 'data-date-format' => 'yyyy-mm-dd'))?>
    <?=$this->form->field('address', array('type' => 'textarea'))?>
    <?=$this->form->field('city')?>
    <?=$this->form->field('state')?>
    <?=$this->form->field('postal_code')?>
    <?=$this->form->field('handedness', array('type' => 'select', 'empty' => true))?>
    <?=$this->form->field('height', array('label' => 'Height (in inches)'))?>
    <?=$this->form->field('weight', array('label' => 'Weight (in pounds)'))?>
    <?=$this->form->field('occupation')?>
<?php endif; ?>

<?php if($showPassword): ?>
    <?=$this->form->field('password', array('type' => 'password'))?>
    <?=$this->form->field('confirm_password', array('type' => 'password'))?>
<?php endif; ?>

<?=$this->form->field($buttonValues[$this->request()->action], array('type' => 'submit-button', 'class' => 'btn btn-primary', 'label' => ''))?>
<?=$this->form->end()?>

