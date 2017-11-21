<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

//$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
//$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
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
	<title>Restaurant Online Ordering System</title>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<?php
		echo $this->Html->meta('icon');
	        echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		
		echo $this->Html->css('style');
		echo $this->Html->css('custom');
		echo $this->Html->css('media');
		echo $this->Html->css('accordian');
		echo $this->Html->css('tips');
                echo $this->Html->css('easy-responsive-tabs');
                echo $this->Html->css('star-rating'); 
                
		echo $this->Html->script('jquery-1.11.1');
		echo $this->Html->css('jQueryUI/jquery-ui-1.10.3.custom');
		echo $this->Html->script('validation/jquery.validate.js');
	        echo $this->Html->script('validation/additional-methods');
		echo $this->Html->script('jquery-ui.js');
		echo $this->Html->script('datepicker');
		echo $this->Html->script('tips');
		echo $this->Html->script('star-rating');
                echo $this->Html->script('easy-responsive-tabs');
		
		
		
	?>
</head>
<body>
	<div class="wrapper clearfix">
		<?php
		if(($this->params['controller']=='Products'  || $this->params['controller']=='Payments')&& ($this->params['action']=='orderDetails' || $this->params['action']=='success' || $this->params['action']=='status')){
			
		}else{
		 echo $this->element('front_left_dashboard');
		}?>
		
		<?php
		//echo "<pre>";print_r($this->params);die;
		if(($this->params['controller']=='products' || $this->params['controller']=='Products' || $this->params['controller']=='Payments')&& ($this->params['action']=='items' || $this->params['action']=='orderDetails' || $this->params['action']=='success' || $this->params['action']=='status')){
		 
		 echo $this->element('items_header');	
		}else{
			echo $this->element('front_dashboard_header');	
		}
		?>
		<?php //echo $this->Session->flash(); ?>
           
		<?php echo $this->fetch('content'); ?>
                <?php echo $this->element('front_footer'); ?> 
		
            </div>
	
	
</body>
</html>