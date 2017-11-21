    <!DOCTYPE html>
    <html lang="en">
        <head>
            
            <?php echo $this->Html->charset('UTF-8'); ?>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="">
            <meta name="author" content="">  
            <link rel="shortcut icon" href="/img/favicon.png">
            <title>Super Admin</title>
            <?php
                echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
                echo $this->Html->css('store_admin/bootstrap'); 
                echo $this->Html->css('store_admin/sb-admin');
                echo $this->Html->css('store_admin/login');     
                echo $this->Html->css('store_admin/font/css/font-awesome.min');
                echo $this->Html->css('store_admin/custom_admin');
                echo $this->Html->script('jquery.min');
                echo $this->Html->css('jQueryUI/jquery-ui-1.10.3.custom');
                echo $this->Html->script('validation/jquery.validate.js');
	        echo $this->Html->script('validation/additional-methods');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('datepicker');
                
            ?>                
        </head>    
        <body>
            <section class="container wrapper">
                <div class="row">
                    <nav role="navigation" class="navbar navbar-inverse navbar-fixed-top">            
                        <div class="navbar-header admin-nav-header-left">             
                            <div class="logoimg"> <?php //echo $this->Html->image($logoimage);?></div> 
                            <?php //echo $this->Html->link($logoLink,'/admin',array('class' => 'navbar-brand logotxt','title' => $logoLink));?>
                        </div>
                        <div class="headerRightText">Welcome to Super Admin</div>
                    </nav>
                    <div class="col-md-5 col-md-offset-4">
                        <div class="login-panel panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
				    <?php if($this->params['action']=='forgetPassword'){
					echo "<span>Forgot Password</span> ";
				    }elseif($this->params['action']=='resetPassword'){
					echo "<span>Reset Password</span> ";
				    }elseif($this->params['controller']=='super' && $this->params['action']=='login'){
					echo "<span>Super Admin Login</span> ";
				    }
				    ?>
				    
				</h3>
                            </div>
                            <?php echo $this->fetch('content'); ?>
                        </div>
                    </div>
                </div>
                <div class="push"></div>
            </section>
            <?php echo $this->element('admin/footer'); ?>  
        </body>
    </html>