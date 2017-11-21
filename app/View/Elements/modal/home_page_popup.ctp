<?php
if (!empty($modalPopupData)) {
    if ($modalPopupData['HomeModal']['modal_box_size_type'] == 1) {
        $modalBoxSizeTypeClass = "modal-square";
    } else {
        $modalBoxSizeTypeClass = "modal-rectangle";
    }
    ?>
    <style>
        #homePagePopup .modal-square button.close { position:relative;font-size:24px;top:-45px;right:-45px;}
        #homePagePopup .modal-square { max-width:550px;}
        #homePagePopup .modal-square .modal-content { min-height:480px;border-radius:0;}
        #homePagePopup .modal-square .modal-content .modal-body { padding:50px 55px;}
        #homePagePopup .modal-rectangle { max-width:720px;}
        #homePagePopup .modal-dialog { margin:150px auto;}
        #homePagePopup .modal-rectangle .modal-content { min-height:300px;border-radius:0;}
        #homePagePopup .modal-rectangle .modal-content .modal-body { padding:55px 45px;}
        #homePagePopup .modal-rectangle button.close { position:relative;font-size:24px;top:-50px;right:-37px;}
        @media screen and (max-width:768px){
            .modal-dialog { margin:30px;}
            #homePagePopup .modal-dialog { width:auto;margin-left:auto;margin-right:auto;}
        }
        @media screen and (max-width:480px){
            #homePagePopup .modal-dialog {margin:20px;}
        }
    </style>
    <div id="homePagePopup" class="modal" role="dialog" tabindex="-1" data-focus-on="input:first">
        <div class="modal-dialog <?php echo $modalBoxSizeTypeClass; ?>">        
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body" style="color:#000;">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                    </button>
                    <?php echo $modalPopupData['HomeModal']['modal_text']; ?>
                </div>
            </div>  
        </div>        
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#homePagePopup").modal('show');
        });
    </script>
    <?php
}?>
