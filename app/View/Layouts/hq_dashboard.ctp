<!DOCTYPE html>
<html lang="en">
    <head>

        <?php echo $this->Html->charset('UTF-8'); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">  
        <link rel="shortcut icon" href="/img/favicon.png">
        <title>
            HQ Admin Dashboard</title>
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
                    <div class="logoimg"><a href="/hq/dashboard" style="color:#FFFFFF;text-decoration: none;">Welcome to HQ Admin</a></div> 
                </div>

                <?php echo $this->element('admin/hq'); ?> 

            </nav>

            <div id="page-wrapper">
                <div class="container-fluid">
                    <?php echo $this->fetch('content'); ?>                    
                </div>
            </div><!-- /#page-wrapper -->
            <div class="push"></div>
        </div><!-- /#wrapper -->
        <?php echo $this->element('admin/footer');
        ?>
        <?php //echo $this->element('sql_dump'); ?>
    </body>
    <script>
        $(window).load(function () {
            // run code
            $('#loading').addClass('hidden');
        });
    </script>
</html>