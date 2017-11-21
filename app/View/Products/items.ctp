
<?php
    echo $this->Html->css('remodal');
    echo $this->Html->css('remodal-default-theme');

    echo $this->Html->script('js.cookie');
    echo $this->Html->script('remodal.min');

    $store_data=$this->Common->getStoreDetail($this->Session->read('store_id'));
    $popupMessage = '';
    switch($store_data['Store']['store_url']) {
        case "pokeocncafe.com" :
            $popupHeader = '<strong>*** Poké Served from 10:30am ***</strong>';
            $popupMessage = '
                <strong>Click the menu item name from "Regular Bowl" "Large Bowl" "Poké wrap" to start ordering!</strong>
                </br></br>
                ** Poké served from 10:30am ~ 6:00pm (Mon.~Fri.), 10:30am~2:00pm (Sat.)
                </br></br>
                Serving coffee, drinks, & Snacks All Day
                </br>
                </br>Thank you!</br>
                Poké OC
            ';
    }
    if($popupMessage) { ?>

        <div data-remodal-id="modal2" role="dialog" aria-labelledby="modal2Title" aria-describedby="modal2Desc" >
          <div>
            <h2 id="modal2Title"><?=$popupHeader?></h2></br><hr></br>
            <p id="modal2pDesc">
              <?=$popupMessage?>
            </p>
          </div>
          <br>
           <hr></br>
            <button data-remodal-action="cancel" class="remodal-cancel">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>

        </div>
        <script>
            var result = Cookies.get('menu-attention-popup-<?=$store_data['Store']['store_url']?>');
            if(result != 'end' ) {
                $("[data-remodal-id=modal2]").remodal({ hashTracking: false });
                $("[data-remodal-id=modal2]").remodal().open();
                $("#is_popup_confirm").val("true");
                $(document).on('cancellation', '.remodal', function () {
                    Cookies.set('menu-attention-popup-<?=$store_data['Store']['store_url']?>', 'end');
                    $("#is_popup_confirm").val("true");
                });
            }
        </script>
<?php } ?>


<?php
    echo $this->Html->css('remodal');
    echo $this->Html->css('remodal-default-theme');
    echo $this->Html->script('js.cookie');
    echo $this->Html->script('remodal.min');
    $store_data=$this->Common->getStoreDetail($this->Session->read('store_id'));
    $popupMessage = '';
    switch($store_data['Store']['store_url']) {
        case "pokeocncafe.com" :
            $popupHeader = '<strong>*** Poké Served from 10:30am ***</strong>';
            $popupMessage = '
                <strong>Click the menu item name from "Regular Bowl" "Large Bowl" "Poké wrap" to start ordering!</strong>
                </br></br>
                ** Poké served from 10:30am ~ 6:00pm (Mon.~Fri.), 10:30am~2:00pm (Sat.)
                </br></br>
                Serving coffee, drinks, & Snacks All Day
                </br>
                </br>Thank you!</br>
                Poké OC
            ';
    }
    if($popupMessage) { ?>

        <div data-remodal-id="modal2" role="dialog" aria-labelledby="modal2Title" aria-describedby="modal2Desc" >
          <div>
            <h2 id="modal2Title"><?=$popupHeader?></h2></br><hr></br>
            <p id="modal2pDesc">
              <?=$popupMessage?>
            </p>
          </div>
          <br>
           <hr></br>
            <button data-remodal-action="cancel" class="remodal-cancel">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>

        </div>
        <script>
            var result = Cookies.get('menu-attention-popup-<?=$store_data['Store']['store_url']?>');
            if(result != 'end' ) {
                $("[data-remodal-id=modal2]").remodal({ hashTracking: false });
                $("[data-remodal-id=modal2]").remodal().open();
                $("#is_popup_confirm").val("true");
                $(document).on('cancellation', '.remodal', function () {
                    Cookies.set('menu-attention-popup-<?=$store_data['Store']['store_url']?>', 'end');
                    $("#is_popup_confirm").val("true");
                });
            }
        </script>
<?php } ?>


<?php
if (DESIGN == 1) {
    echo $this->element('design/aaron/item');
    echo $this->Html->script('item_new_design');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/item');
    echo $this->Html->script('item_new_design');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/item');
    echo $this->Html->script('item_new_design');
} elseif (DESIGN == 4) {
    echo $this->element('design/oldlayout/product/items');
    echo $this->Html->script('item_old_design');
    ?>
<?php }
?>
<?php
echo $this->Html->script('theme/custom');
?>
<input id="itemIdpopup" type="hidden"/>