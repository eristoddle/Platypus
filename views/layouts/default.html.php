<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php echo $this->html->charset();?>
    <title>AFDC &gt; <?php echo $this->title() ?: 'League Management System'; ?></title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
	<?php echo $this->html->style(array(
        'http://www.afdc.com/bootstrap/css/bootstrap.min.css',
        'http://www.afdc.com/datepicker/css/datepicker.css'
    )); ?>
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
	<?php echo $this->html->style(array(
        'http://www.afdc.com/bootstrap/css/bootstrap-responsive.min.css',
        'http://www.afdc.com/tablesort/themes/blue/style.css'
    )); ?>
    <?php echo $this->styles(); ?>

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">

    <?php 
        echo $this->html->script(array(
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
        'http://www.afdc.com/bootstrap/js/bootstrap.min.js',
        'http://www.afdc.com/tablesort/jquery.metadata.js',
        'http://www.afdc.com/tablesort/jquery.tablesorter.js',
        'http://www.afdc.com/datepicker/js/bootstrap-datepicker.js'
        ));

        echo $this->script();

        echo $this->scripts();
    ?>
    <script lang="text/javascript">
        $(function(){
            // Login Modal
            $("#loginModal").modal({
                "keyboard": true,
                "show": false
            });

            $("#navBar").delegate("#loginLink", "click", function() {
                $("#loginModal").modal("show");
                return false;
            });

            // Global Tooltips
            $(".hasTooltip").tooltip();

            // Global Tablesorter
            $(".tablesorter").tablesorter();

            // Global date picker
            $(".date-field").datepicker();

            // Global Popover
            $(".hasPopover").popover();
        });

    </script>
    <?php $googleAnalytics = \app\util\Config::get('google_analytics'); ?>
    <?php if (isset($googleAnalytics)): ?>
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '<?=$googleAnalytics['account']?>']);
      <?php if (isset($googleAnalytics['domain'])): ?>
        _gaq.push(['_setDomainName', <?=$googleAnalytics['domain']?>]);
      <?php endif; ?>
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
    <?php endif; ?>
  </head>

  <body>
    <div class="navbar navbar-fixed-top" id="navBar">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/">AFDC League Management System</a>
          <div class="nav-collapse">
            <ul class="nav">
            <?php
                $at_dashboard = $at_leagues = $at_whiteboard = '';
                // Set the highlight in the menu bar.
                $c = 'at_' . $this->_request->controller;
                $$c = ' class="active"';
            ?>
              <li<?php echo (isset($at_dashboard) ? $at_dashboard : ''); ?>><a href="/">Home</a></li>
              <li<?php echo (isset($at_leagues) ? $at_leagues : ''); ?>><?=$this->html->link('Leagues', 'Leagues::index')?></li>
              <li<?php echo (isset($at_users) ? $at_users : ''); ?>><?=$this->html->link('Users', 'Users::index')?></li>
            </ul>
            <ul class="nav pull-right">
            <?php if (isset($CURRENT_USER)): ?>
                <?php
                    $cartCount = 0;
                    $cart = $CURRENT_USER->getShoppingCart();
                    if ($cart) {
                        $cartCount = $cart->getItems()->count();
                    }
                ?>
                <?php if ($cartCount > 0): ?>
                    <li><?=$this->html->link('<span class="badge badge-info">' . $cartCount . ' <i class="icon-shopping-cart icon-white"></i></span>', 'Carts::index', array('escape' => false))?></li>
                <?php endif; ?>
                <li class="dropdown" id="loggedInMenu">
                    <?=$this->html->link('Logged in as: ' . $CURRENT_USER->firstname . ' ' . $CURRENT_USER->lastname . ' <b class="caret"></b>', '#loggedInMenu', array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'escape' => false))?>
                    <ul class="dropdown-menu">
                        <li><?=$this->html->link('User Dashboard', 'Dashboard::user')?></li>
                        <li><?=$this->html->link('Your Profile', 'Profile::index')?></li>
                        <li class="divider"></li>
                        <?php if ($CURRENT_USER->can('whiteboard.view')): ?>
                            <li<?php echo $at_whiteboard; ?>><?=$this->html->link('Whiteboard', 'Whiteboard::index')?></li>
                            <li class="divider"></li>
                        <?php endif; ?>
                        <li><?=$this->html->link('Log out', 'Auth::logout')?></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><?=$this->html->link('Log in', 'Auth::index', array('id' => 'loginLink'))?></li>
            <?php endif; ?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <?php if (!\lithium\core\Environment::is('production')): ?>
        <div class="alert alert-info">Your current environment is: <strong><?=\lithium\core\Environment::get()?></strong></div>
    <?php endif; ?>
    <div class="container">
        <?=$this->flashMessage->output('global')?>
        <?php echo $this->content(); ?>
    </div> <!-- /container -->

    <!-- Login Modal Window -->
    <div class="modal hide fade" id="loginModal">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Login to the AFDC Leagues Manager</h3>
        </div>
        <div class="modal-body">
            <?=$this->_render('element', 'LoginMethods'); ?>
        </div>
    </div>
  </body>
</html>