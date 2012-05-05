<div class="page-header"><h1>
    Edit League
    <small><?=$league->name?></small>
</h1></div>

<p><?=$this->html->link('Return to league listing', 'Leagues::index')?></p>

<?=$this->_render('element', 'LeagueForm'); ?>
