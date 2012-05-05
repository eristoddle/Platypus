<pre>
<?php
    $vars = get_defined_vars();
    unset($vars['CURRENT_USER']);
    unset($vars['template__']);
    unset($vars['h']);
    var_dump($vars);
?>
</pre>