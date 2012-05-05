<?php if (!isset($CURRENT_USER)): ?>
    <div class="alert alert-error">The user directory is only available to logged-in users. Please log in to access</div>
<?php endif; ?>
<div style="text-align: right">
    <form class="form-search">
      <input type="text" class="input-medium search-query" name="q" value="<?=(isset($query) ? $query : '')?>">
      <button type="submit" class="btn">Search</button>
    </form>
</div>
<?php 
    if (isset($userList)) {
        echo '<table class="table table-striped tablesorter">';
        echo '<tbody>';
        echo userRow();
        foreach ($userList as $u) {
            echo userRow($u);
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo "Do a search.\n";
    }
?>

<?php

    function userRow($user = null) {
        if (is_null($user)) {
            return '<thead><tr><th width="15%">ID</th><th>First Name</th><th>Middle</th><th>Last Name</th><th>Gender</th><th>Email Address</th><th width="8%">City</th><th>State</th><th>Postal Code</th></tr></thead>' . "\n";
        }

        $out  = '<tr style="text-transform: capitalize">';
        $out .= '<td>' . $user->_id . '</td>';
        $out .= '<td>' . $user->firstname . '</td>';
        $out .= '<td>' . $user->middlename . '</td>';
        $out .= '<td>' . $user->lastname . '</td>';
        $out .= '<td>' . $user->gender . '</td>';
        $out .= '<td style="text-transform: lowercase">' . $user->email_address . '</td>';
        $out .= '<td>' . $user->city . '</td>';
        $out .= '<td>' . $user->state . '</td>';
        $out .= '<td>' . $user->postal_code . '</td>';
        $out .= "</tr>\n";

        return $out;
    }
?>