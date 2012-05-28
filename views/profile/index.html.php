<div class="page-header"><h1>
    Your Profile
</h1></div>

<div class="row">
    <div class="span2" style="font-weight: bold">&nbsp;</div>
    <div class="span4"><h3><?=$user->firstname?> <?=$user->middlename?> <?=$user->lastname?></h3></div>
</div>
<div class="row">
    <div class="span6">
        <div class="row">
            <div class="span2" style="font-weight: bold">E-Mail Address</div>
            <div class="span4"><?=$user->email_address?></div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Address</div>
            <div class="span4">
                <address>
                    <?=nl2br($user->address)?><br />
                    <?=$user->city?>, <?=$user->state?>&nbsp; <?=$user->postal_code?>
                </address>
            </div>
        </div>

        <?php if ($user->birthdate): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Birthdate</div>
                <div class="span4"><?=$user->birthdate?></div>
            </div>
        <?php endif; ?>

        <?php if ($user->handedness): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Handedness</div>
                <div class="span4"><?=$user->handedness?></div>
            </div>
        <?php endif; ?>

        <?php if ($user->height): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Height</div>
                <div class="span4"><?=$user->height?>"</div>
            </div>
        <?php endif; ?>

        <?php if ($user->weight): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Weight</div>
                <div class="span4"><?=$user->weight?>lbs.</div>
            </div>
        <?php endif; ?>

        <?php if ($user->occupation): ?>
            <div class="row">
                <div class="span2" style="font-weight: bold">Occupation</div>
                <div class="span4"><?=$user->occupation?></div>
            </div>
        <?php endif; ?>

        <?php if ($user->phone): ?>
            <?php foreach ($user->phone as $type => $number): ?>
                <div class="row">
                    <div class="span2" style="font-weight: bold; text-transform: capitalize;"><?=$type?></div>
                    <div class="span4">            
                        <?=$number?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="row">
            <div class="span2" style="font-weight: bold">System Groups</div>
            <div class="span4"><?=implode(', ', $user->permission_groups->to('array'))?></div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="span2" style="font-weight: bold">&nbsp;</div>
            <div class="span4"><?=$this->html->link('Edit Profile', 'Profile::edit', array('class' => 'btn btn-large btn-primary'))?></div>
        </div>
    </div> <!-- End First Column -->
    <div class="span6">
        <h4>Login Methods:</h4>
        <div class="row">
            <div class="span2" style="font-weight: bold">Password</div>
            <?php if (isset($identities['password'])): ?>
                <div class="span2">Not displayed.</div>
                <div class="span2"><?=$this->html->link('Change', 'Profile::edit', array('class' => 'btn btn-mini btn-info'))?></div>
            <?php elseif (!isset($user->email_address)): ?>
                <div class="span4">Passwords cannot be used until an email is set.</div>
            <?php else: ?>
                <div class="span2">None found.</div>
                <div class="span2"><?=$this->html->link('Create', array('Auth::resetPassword', 'args' => array($user->email_address)), array('class' => 'btn btn-mini btn-success'))?></div>
            <?php endif; ?>
        </div>
        </div>
        <div class="row">
            <div class="span2" style="font-weight: bold">Forum Login</div>
            <?php if (isset($identities['phpbb'])): ?>
                <div class="span2"><?=$this->html->link($identities['phpbb']['prv_uid'], 'http://www.afdc.com/forum/memberlist.php?mode=searchuser&username=' . urlencode($identities['phpbb']['prv_uid']))?></div>
                <!--<div class="span2"><?=$this->html->link('Change', array('Auth::editForumLogin', 'id' => $user->_id), array('class' => 'btn btn-mini btn-info'))?></div>-->
            <?php else: ?>
                <div class="span2">No associated forum login</div>
                <!--<div class="span2"><?=$this->html->link('Create', array('Auth::editForumLogin', 'id' => $user->_id), array('class' => 'btn btn-mini btn-success'))?></div>-->
            <?php endif; ?>

        </div>
    </div>
    <div class="row" style="margin-top:25px">
        <div class="span8">
            <?php $teams = $CURRENT_USER->getTeams(); ?>
            <?php if ($teams): ?>
            <h4>Teams:</h4>
            <table class="table table-striped tablesorter">
                <thead><tr>
                    <th>League</th>
                    <th>Sport</th>
                    <th>Team</th>
                    <th>Rank</th>
                    <th>Wins</th>
                    <th>Losses</th>
                    <th>Pct</th>
                    <th>Pt. Diff.</th>
                </tr></thead>
                <tbody><?php foreach ($teams as $t): $l = $t->getLeague(); ?>
                    <tr>
                        <td><?=$this->html->link($l->name, array('Leagues::view', 'id' => $l->_id) )?></td>
                        <td><?=$l->sport?></td>
                        <td><?=$t->name?></td>
                        <td><?=$t->stats->rank?></td>
                        <td><?=$t->stats->wins?></td>
                        <td><?=$t->stats->losses?></td>
                        <td><?=number_format($t->stats->wins / ($t->stats->losses + $t->stats->wins), 3)?></td>
                        <td><?=$t->stats->point_differential?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<!--
    <?php var_dump($user->permission_groups); ?>
-->