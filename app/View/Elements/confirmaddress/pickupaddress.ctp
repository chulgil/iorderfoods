<style>
    .modal-dialog {    
        margin: 0 auto;        
        position:relative;          
    }
    .modal-body{
        color:#000000;
    } 
    .modal{
/*        top: 32% !important; */
    }
    .order-form-layout{
        float: left;
        width: 100%;
        margin: 15px auto;
        }

    .modal-backdrop {
        z-index: 0;
    }
    .error {
        color: #b50000;
        font-size: 12px;
        font-weight: 400;
        margin: 0;
        padding: 0;
    }
    @media all and ( max-width: 680px) {
        .modal-dialog {
            width: auto;
        }
    }
    @media all and ( min-width: 700px) {
        .modal-dialog {
            width: 700px;
        }
    }    

</style>
<?php $StoreDetails=$this->Common->getStoreDetails();?>
 
<!-- -------modal for ups calculator -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="orderpickup" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog select-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="lineModalLabel">Confirm address</h3>
            </div>
            <div class="modal-body">
                <span class="errorMsg"></span>
                <div class="container" style="width:auto;"> 
                    <div class="tab-pane chkDeliveryAdd">                         
                        <address class="inbox">
                            <p>
                                <?php
                              echo $StoreDetails['Store']['store_name']."<br>".$StoreDetails['Store']['address']."<br>".$StoreDetails['Store']['city'].', '.$StoreDetails['Store']['state'].', '.$StoreDetails['Store']['zipcode'].'<br>'.$StoreDetails['Store']['phone'];
                              ?>
                            </p>
                        </address>                        
                        <p>
                            
                        </p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="pickbtn"><span>Confirm</span></button>                        
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>

<script>
    
$('#pickbtn').click(function () {       
    //window.location=window.location;
    window.location=window.location;
});

</script>