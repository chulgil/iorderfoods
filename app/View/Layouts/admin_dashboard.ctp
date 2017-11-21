<!DOCTYPE html>
<html lang="en">
    <head>

        <?php echo $this->Html->charset('UTF-8'); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">  
        <link rel="shortcut icon" href="/img/favicon.png">
        <title>Store Admin Dashboard</title>
        <?php
        echo $this->Html->css('star-rating');
        echo $this->Html->css('store_admin/bootstrap');
        echo $this->Html->css('store_admin/sb-admin');
        echo $this->Html->css('store_admin/backend');
        echo $this->Html->css('store_admin/font/css/font-awesome.min');
        echo $this->Html->css('store_admin/custom_admin');
        echo $this->Html->css('store_admin/jquery-ui');
        echo $this->Html->script('store_admin/jquery-1.11.0.min');
        echo $this->Html->script('store_admin/jquery-ui');
        echo $this->Html->script('store_admin/bootstrap');
        echo $this->Html->script('validation/jquery.validate.js');
        echo $this->Html->script('validation/additional-methods');
        echo $this->Html->script('store_admin/general');
        echo $this->Html->script('star-rating');
	echo $this->Html->script('jquery.maskedinput');
        ?>
	<style>
    	.side-nav {
            top: 71px;
        }
    </style>
</head>
   
<style>
    #loading {
        width:100%;
        height:100%;
        background-color: black;
        opacity: 0.54;
        position:fixed;
        z-index: 9;
    }
  .ajax-loader {
    position: absolute;
    left: 50%;
    top: 50%;
    margin-left: -32px; /* -1 * image width / 2 */
    margin-top: -32px; /* -1 * image height / 2 */
  }
</style>
<div id="loading">
    <img src="/img/ajax-loader.gif" class="ajax-loader">
</div>

<?php if (($this->params['controller'] == 'kitchens') && ($this->params['action'] == 'index')) {?>
     <body style='margin-top: 0px !important;background-color:#FCFCFC;'>       
    <div id="wrapper" class="wrapper" style="padding-left: 50px !important;">  
        <div id="page-wrapper" style='padding: 0px !important;'>
        <?php } else {
	   ?>
                 <body>
                <div id="wrapper" class="wrapper">
                    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">        
                    <div class="navbar-header">        
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="logoimg"><a href="/stores/dashboard" style="color:#FFFFFF;text-decoration: none;">Welcome to <?php echo $this->Session->read('admin_storeName') . " Store"; ?></a></div> 
                    </div>
                    
                     <?php   echo $this->element('admin/store'); ?>

                </nav>
                <div id="page-wrapper">
                	<div class="container-fluid">
					<?php } ?>
                    <?php echo $this->fetch('content'); ?>                  
                    </div>
                </div><!-- /#page-wrapper -->
                <div class="push"></div>
            </div><!-- /#wrapper -->
            
            
    <?php 
    if($this->Session->read('Auth.Admin.role_id')){
        echo $this->Html->css('popup');
        echo $this->element('session/timeout');
    ?>
                    
    <style>.ui-dialog { z-index: 1000 !important ;}</style>  
                    
    <script>

        function clearsession(data) {
            window.location="/stores/logout"            
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
                 <script>
                     $(window).load(function () {
                        // run code
                        $('#loading').hide();
                     });
                 </script>
</html>