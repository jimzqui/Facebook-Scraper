<!DOCTYPE html>
<!--[if IEMobile 7 ]>    <html class="no-js iem7"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9 lt-ie10"> <![endif]-->
<!--[if IE 9]><html class="no-js lt-ie10"><![endif]-->
<!--[if (gt IEMobile 7)|!(IEMobile)]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<!-- <?=Configure::read('TestCheat')?> -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $title_for_layout ?></title>
		<meta name="description" content="<?php echo $title_for_layout ?>">
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>
		<?php echo $this->fetch('meta'); ?>

		<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>

		<?php
		echo $this->element('javascript_export');
		echo $this->Html->css(array('bootstrap', 'jquery.dataTables', 'style'));
		echo $this->Html->script(array('vendor/modernizr-2.6.2.min', 'vendor/jquery.dataTables.min', 'vendor/bootstrap.min'));
		?>
	</head>
	<body>
		<?php echo $this->element('facebook_script') ?>
		<div id="container">
		<?php echo $this->fetch('content'); ?>
		</div>

		<?php echo $this->Html->script(array('functions', 'main')); ?>
		<script type="text/javascript">
		  //<![CDATA[
		  $(function() {
			<?php echo $this->fetch('onload'); ?>
		  })
		  //]]>
		</script>
	</body>
</html>