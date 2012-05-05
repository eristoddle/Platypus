<?php
/**
 * li3_flash_message plugin for Lithium: the most rad php framework.
 *
 * @copyright     Copyright 2010, Michael Hüneburg
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * Copy this file to `app/views/elements` to customize the output.
 */ 
?>
<div class="alert alert-<?php echo !isset($alertType) ? 'info' : $alertType ?>">
    <?php if (!isset($alertClose) or $alertClose === false): ?>
        <a class="close" data-dismiss="alert">×</a>
    <?php endif; ?>
    <?php if (isset($alertTitle)): ?>
        <h4 class="alert-heading"><?=$alertTitle?></h4>
    <?php endif; ?>
    <?php echo $message; ?>
</div>