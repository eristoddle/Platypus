<?php 
    $fieldsToShow = array('_id', 'payer_email', 'auth_amount', 'remaining_settle', 'txn_id', 'invoice', 'payment_status', 'pending_reason');

    // All Fields
    #$fieldsToShow = array ('_id','shopping_cart_id','mc_gross','invoice','auth_exp','protection_eligibility','item_number1','payer_id','tax','payment_date','payment_status','charset','mc_shipping','mc_handling','first_name','transaction_entity','notify_version','custom','payer_status','business','num_cart_items','mc_handling1','verify_sign','payer_email','mc_shipping1','tax1','parent_txn_id','txn_id','payment_type','remaining_settle','auth_id','last_name','item_name1','receiver_email','auth_amount','quantity1','receiver_id','pending_reason','txn_type','mc_gross_1','mc_currency','residence_country','transaction_subject','payment_gross','auth_status','ipn_track_id');

    echo '<h2>Payments</h2>';
    echo '<table class="table table-striped tablesorter">';
        echo '<thead><tr>';
        foreach ($fieldsToShow as $f) { echo "<th>{$f}</th>"; }
        echo '</tr></thead>';
        echo "<tbody>\n";

    foreach ($payments as $p) {
        $pArray = $p->to('array');
        echo '<tr>';
        foreach ($fieldsToShow as $f) { echo '<td>' . (isset($pArray[$f]) ? $pArray[$f] : '&mdash;') . '</td>'; }
        echo "</tr>\n";
    }

        echo '</tbody>';
        echo '</table>';
?>
<pre>
    <?php #var_export(array_keys($p->to('array'))); ?>
</pre>
