<div class="page-header"><h1>
    AFDC Teams
    <small>Search</small>
</h1></div>
<?php if (!isset($CURRENT_USER)): ?>
    <div class="alert alert-error">The team directory is only available to logged-in users. Please log in to access</div>
<?php endif; ?>
<div style="text-align: right">
    <form class="form-search">
      <input type="text" class="input-medium search-query" name="q" value="<?=(isset($query) ? $query : '')?>">
      <button type="submit" class="btn">Search</button>
    </form>
</div>
<?php if (isset($teamList) and $teamList->count() > 0): ?>
<table class="table table-striped tablesorter">
    <thead><tr>
        <th>Team</th>
        <th>League</th>
        <th>Sport</th>
        <th>Rank</th>
        <th>Wins</th>
        <th>Losses</th>
        <th>Pct</th>
        <th>Pt. Diff.</th>
    </tr></thead>
    <tbody><?php foreach ($teamList as $t): $l = $t->getLeague(); ?>
        <tr>
            <td><?=$this->html->link($t->name, array('Teams::view', 'id' => $t->_id))?></td>
            <td><?=$this->html->link($l->name, array('Leagues::view', 'id' => $l->_id))?></td>
            <td><?=$l->sport?></td>
            <?php if (is_object($t->stats)): ?>
                <td><?=$t->stats->rank?></td>
                <td><?=$t->stats->wins?></td>
                <td><?=$t->stats->losses?></td>
                <td>
                    <?php if ($t->stats->losses + $t->stats->wins > 0): ?>
                        <?=number_format($t->stats->wins / ($t->stats->losses + $t->stats->wins), 3)?>
                    <?php else: ?>
                        n/a
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($t->stats->point_differential)): ?>
                        <?=sprintf("%+d", $t->stats->point_differential)?>
                    <?php else: ?>
                        n/a
                    <?php endif; ?>
                </td>
            <?php else: ?>
                <td colspan="5" style="text-align: center">Not Available</td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?></tbody>
</table>
<?php elseif (isset($teamList)): ?>
    <div class="alert alert-warning">No results found for your search query.</div>
<?php else: ?>
    <p>Please enter a search term in the box above to search the AFDC's team listings.</p>
<?php endif; ?>