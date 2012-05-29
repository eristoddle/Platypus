<div class="page-header"><h1>
    AFDC Players
    <small>Search</small>
</h1></div>
<?php if (!isset($CURRENT_USER)): ?>
    <div class="alert alert-error">The user directory is only available to logged-in users. Please log in to access</div>
<?php endif; ?>
<div style="text-align: right">
    <form class="form-search">
      <input type="text" class="input-medium search-query" name="q" value="<?=(isset($query) ? $query : '')?>">
      <button type="submit" class="btn">Search</button>
    </form>
</div>
<?php if (isset($userList) and $userList->count() > 0): ?>
<table class="table table-striped tablesorter">
    <thead><tr>
        <th width="15%">ID</th>
        <th>First Name</th>
        <th>Middle</th>
        <th>Last Name</th>
        <th>Gender</th>
        <th>Email Address</th>
        <th width="8%">City</th>
        <th>State</th>
        <th>Postal Code</th>
    </tr></thead>
    <tbody><?php foreach ($userList as $u): ?>
        <tr style="text-transform: capitalize">
            <td><?=$u->_id?></td>
            <td><?=$u->firstname?></td>
            <td><?=$u->middlename?></td>
            <td><?=$u->lastname?></td>
            <td><?=$u->gender?></td>
            <td style="text-transform: lowercase"><?=$u->email_address?></td>
            <td><?=$u->city?></td>
            <td><?=$u->state?></td>
            <td><?=$u->postal_code?></td>
        </tr>
    <?php endforeach; ?></tbody>
</table>
<?php elseif (isset($userList)): ?>
    <div class="alert alert-warning">No results found for your search query.</div>
<?php else: ?>
    <p>Please enter a search term in the box above to search the AFDC's member directory.</p>
<?php endif; ?>