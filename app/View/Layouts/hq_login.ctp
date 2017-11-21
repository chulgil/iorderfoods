    <!DOCTYPE html>
    <html lang="en">
        <head>
            
            <?php echo $this->Html->charset('UTF-8'); ?>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="">
            <meta name="author" content="">  
            <link rel="shortcut icon" href="/img/favicon.png">
            <title><?php echo "HQ Admin"; ?>  </title>
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
                        <div class="headerRightText">Welcome to HQ Admin </div>
                    </nav>
                    <div class="col-md-5 col-md-offset-4">
                        <div class="login-panel panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
				    <?php if($this->params['action']=='forgetPassword'){
					echo "<span>Forgot Password</span> ";
				    }elseif($this->params['action']=='resetPassword'){
					echo "<span>Reset Password</span> ";
				    }
                                    elseif($this->params['controller']=='hq' && $this->params['action']=='login'){
					echo "<span>HQ Login</span> ";
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
            
            
            <?php 
            if($this->Session->read('Auth.hq.role_id')){
                echo $this->Html->css('popup');
                echo $this->element('session/timeout');
            ?>

            <style>.ui-dialog { z-index: 1000 !important ;}</style>  

            <script>

                function clearsession(data) {
                    window.location="/hq/logout"            
                }

                function sessionpopup(){
                    $('#timeoutpop').modal('show');
                }

                // If theres no activity for 5 Minutes do something
                var activityTimeout = setTimeout(inActive, 600000);
                function resetActive(){                
                    clearTimeout(activityTimeout);
                    activityTimeout = setTimeout(inActive, 600000);
                }
                // No activity do something.
                function inActive(){ 
                    sessionpopup();
                }
                // Check for mousemove, could add other events here such as checking for key presses ect.
                $(document).bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function(){resetActive()});
            </script>
           <?php } ?>
            
        </body>
    </html>