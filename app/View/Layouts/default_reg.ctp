
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		
		
		<?php echo $this->fetch('title'); ?>
	</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title;?></title>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<?php
		echo $this->Html->meta('icon');
	        echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		
		echo $this->Html->css('style');
		echo $this->Html->css('custom');
		echo $this->Html->css('media');
		echo $this->Html->script('jquery-1.11.1');
		echo $this->Html->css('jQueryUI/jquery-ui-1.10.3.custom');
		echo $this->Html->script('jquery-1.11.1');
		echo $this->Html->script('validation/jquery.validate.js');
	        echo $this->Html->script('validation/additional-methods');
		echo $this->Html->script('jquery-ui.js');
		echo $this->Html->script('datepicker');
		
	
		
		
	?>
</head>
<body>
	<div class="wrapper clearfix">
		<?php echo $this->element('front_left_dashboard');?>
		<?php echo $this->element('front_header');?>
		<?php echo $this->fetch('content'); ?>
		<?php echo $this->element('front_footer'); ?>
	
	
</body>
</html>
