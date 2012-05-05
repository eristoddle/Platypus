<div style="text-align: center">
<?php
    echo '<p>' . $this->html->link(
        '<i class="icon-user icon-white"></i> Login via the Forum', 
        array('Auth::login', 'adapter' => 'phpbb'), 
        array('class' => 'btn btn-info btn-large', 'escape' => false)) . '</p>';
?>
<hr />

<div class="alert alert-error">
    <strong>Note:</strong> this password <em>is not</em> the same as your forum password.
</div>
<?=$this->form->create(null, array('class' => 'form-horizontal', 'url' => array('Auth::login', 'adapter' => 'password'))); ?>
    <?=$this->security->requestToken();?>
    <?=$this->form->field('email', array('label' => 'Email Address')); ?>
    <?=$this->form->field('password', array('type' => 'password')); ?>
    <?=$this->form->field('Login with Password', array('type' => 'submit-button', 'class' => 'btn', 'label' => ''))?>
<?=$this->form->end(); ?>
<hr />

<?=$this->form->create(null, array('class' => 'form-horizontal', 'url' => array('Auth::resetPassword'))); ?>
    <?=$this->security->requestToken();?>
    <?=$this->form->field('email', array('label' => 'Email Address')); ?>
    <?=$this->form->field('Reset My Password', array('type' => 'submit-button', 'class' => 'btn', 'label' => ''))?>
<?=$this->form->end(); ?>
<hr />
<?php
    echo '<p>' . $this->html->link(
        '<i class="icon-plus icon-white"></i> Register for the site', 
        'Users::create', 
        array('class' => 'btn btn-success', 'escape' => false)) . '</p>';
?>
</div>