<!DOCTYPE html>
<html>
    
<head>
	<?php echo $this->Html->charset(); ?>
	<link rel="shortcut icon" href="/img/favicon.png">
	<title>
		
		
		<?php echo $this->fetch('title'); ?>
	</title>
	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title><?php echo $title;?></title>
	<?php
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
                
                echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('master');
		echo $this->Html->css('color-theme');
                echo $this->Html->css('responsiveslides');
		
                echo $this->Html->script('jquery.min');
		echo $this->Html->script('bootstrap.min.js');
		echo $this->Html->script('responsiveslides.min');
		echo $this->Html->script('master');
                echo $this->Html->script('jquery-ui');
                echo $this->Html->script('validation/jquery.validate.js');
	        echo $this->Html->script('validation/additional-methods');
		
	
	?>
    <!--[if lt IE 9]>
      <script src="/js/html5shiv.min.js"></script>
      <script src="/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="theme-one">
        <?php echo $this->element('horizontal_header');
        echo $this->fetch('content'); 
        echo $this->element('footer'); ?>
		
</body>
</html>
