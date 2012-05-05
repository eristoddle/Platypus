<?php
    use lithium\net\http\Router;

    $paypal_items = array();

    $total_auth = 0;
?>
<div class="page-header"><h1>
    AFDC Leagues
    <small>Checkout</small>
</h1></div>

<h3>Invoice</h3>
<?php if ($cart->is_authorized): ?>
<div class="alert alert-success">
    We have received an authorization from PayPal for this transaction, you do not need to checkout again. It may take a few minutes for the payment to complete.
    If the league is not full, and you see this message for an inordinate amount of time, please contact <a href="mailto:webmaster@afdc.com">the webmaster</a> for help.
</div>
<?php endif; ?>
<table class="table table-bordered">
    <tr><th>ID</th><th>Item Description</th><th>Price</th></tr>
    <?php foreach ($cart_items as $ci):  ?>
        <?php 
            $total_auth += $ci->price;

            $paypal_items[] = array(
                'name' => $ci->name,
                'number' => (string) $ci->_id,
                'amount' => $ci->price
            );
        ?>
        <tr>
            <td><?=$ci->_id?></td>
            <td>
                <h4><?=$ci->name?></h4>
                <p><?=$ci->Description?></p>
                <?php if (!$ci->isValid()): ?>
                <div class="unstyled alert alert-error">
                    <h4 class="alert-heading">There are some problems with this item:</h4>
                    <ul>
                        <li><?php echo implode('</li><li>', $ci->getReference()->regErrors()); ?></li>
                    </ul>
                    <p>Completing the checkout process will authorize a charge just in case these issues are resolved, but you will not be charged unless you are successfully registered for the league.</p>
                </div>
                <?php endif; ?>
            </td>
            <td><?=money_format('%n', $ci->price)?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <th colspan="2" style="text-align: right">Total:</th>
        <td><?=money_format('%n', $total_auth)?></td>
    </tr>
</table>
<div style="text-align: right">
    <!-- Paypal Form -->
    <form action="<?=$paypal_config['api_target']?>" method="post">
        <input type="hidden" name="cmd" value="_cart" />
        <input type="hidden" name="upload" value="1" />
        <input type="hidden" name="business" value="<?=$paypal_config['recipient']?>" />
        <input type="hidden" name="notify_url" value="<?=Router::match($paypal_config['ipn_route'], $this->request(), array('absolute' => true));?>" />        
        <input type="hidden" name="paymentaction" value="<?=$paypal_config['paymentaction']?>" />
        <input type="hidden" name="invoice" value="<?=$cart->_id?>" />
        <?php foreach ($paypal_items as $i => $p): ?>
            <input type="hidden" name="item_name_<?=($i+1)?>" value="<?=$p['name']?>" />
            <input type="hidden" name="item_number_<?=($i+1)?>" value="<?=$p['number']?>" />
            <input type="hidden" name="amount_<?=($i+1)?>" value="<?=$p['amount']?>" />
        <?php endforeach; ?>

        <?php if ($paypal_config['success_route']): ?>
            <input type="hidden" name="return" value="<?=Router::match($paypal_config['success_route'], $this->request(), array('absolute' => true));?>">
        <?php endif; ?>
        <?php if ($paypal_config['success_route']): ?>
            <input type="hidden" name="cancel_return" value="<?=Router::match($paypal_config['cancel_route'], $this->request(), array('absolute' => true));?>">
        <?php endif; ?>

        <input type="hidden" name="no_shipping" value="1">
        <input type="hidden" name="no_note" value="1">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="bn" value="AFDC_ShoppingCart_WPS_US">
        <button class="btn btn-primary btn-large" type="submit">Proceed to PayPal</button>
    </form>
</div>