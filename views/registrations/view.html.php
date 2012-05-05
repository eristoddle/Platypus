<div class="page-header"><h1>
    League Registration
    <small>View Registrant</small>
</h1></div>
<?=$this->html->link('Return to participants list', array('Leagues::participants', 'id' => $registration->league_id))?>

<pre><?php print_r($registration->to('array')); ?></pre>