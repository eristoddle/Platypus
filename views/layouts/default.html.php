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
    <?php echo $this->scripts(); ?>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
	<?php echo $this->html->style(array('http://www.afdc.com/bootstrap/css/bootstrap.min.css')); ?>
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
	<?php echo $this->html->style(array('http://www.afdc.com/bootstrap/css/bootstrap-responsive.min.css')); ?>

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
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
              <li class="active"><a href="/">Home</a></li>
              <li><a href="/whiteboard">Whiteboard</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
    <?php echo $this->content(); ?>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php echo $this->html->script(array(
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
        'http://www.afdc.com/bootstrap/js/bootstrap.min.js'
    )); ?>

  </body>
</html>