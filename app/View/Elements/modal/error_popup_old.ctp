<style>
    .modal { overflow: auto !important; }
</style>
<?php if (DESIGN == 4) { ?>
    <div id="errorPop" class="modal notify-msg-popup" role="dialog" tabindex="-1" data-focus-on="input:first">
        <div class="modal-dialog modal-sm" style="width: 500px;top: 150px;">        
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px">
                        <span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <div id="errorPopMsg">
                    </div>
                </div>  
            </div>        
        </div> 
    </div>
<?php } else {
    ?>
    <style>
        .modal-content{
            width:100%;
        }
    </style>
    <div id="errorPop" class="modal notify-msg-popup" role="dialog" tabindex="-1" data-focus-on="input:first">
        <div class="modal-dialog modal-sm">        
            <!-- Modal content-->
            <div class="modal-content">
                <!--                <div class="modal-header">
                                </div>-->
                <div class="modal-body btn-info theme-bg-1">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <div id="errorPopMsg">

                    </div>
                </div>  
            </div>        
        </div> 
    </div>
<?php } ?>
    <?php //prx($this->Session->read('Message'));?>
<?php 
if ($this->Session->check('Message.flash.message')) { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            var msg = '<?php echo $this->Session->read('Message.flash.message'); ?>';
            <?php unset($_SESSION['Message']);?>
            $("#errorPop").modal('show');
            $("#errorPopMsg").html(msg);
        });
    </script>
<?php } ?>



