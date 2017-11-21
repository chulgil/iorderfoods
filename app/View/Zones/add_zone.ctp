<style>
    .modal-dialog {    
        margin: 0 auto;        
        position:relative;          
    }
    .modal-body{
        color:#000000;
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
.ordr-bx-pos{ padding-right: 0px;}
.modal-dialog .ordr-bx-pos .orderTypePickUp, .modal-dialog .ordr-bx-pos .orderTypeDelivery{width:80%;}
.modal-dialog label {
    color: #737373;
    font-size: 16px;
    font-weight: 400;
}
.nav > li.disabled > a{
    font-weight:500;
}
</style>
<?php $StoreDetails=$this->Common->getStoreDetails();?>
 
<!-- -------modal for ups calculator -->
<div class="modal fade" id="deliveryzone" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog select-modal">
        <div class="modal-content">
            <div class="modal-header">                
                <h3 class="modal-title" id="lineModalLabel">Add Delivery Zone</h3>
            </div>
            <div class="modal-body">
                <span class="errorMsg"></span>
                <div class="container" style="width:auto;">
                    
                    
                </div>
            </div> 
        </div>
    </div>
</div>