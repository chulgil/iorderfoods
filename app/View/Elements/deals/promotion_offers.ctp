<?php /*  if (!empty($promotionalOfferData)) { ?>
  <div class="row">
  <div class="col-sm-12 col-xs-12">
  <?php
  foreach ($promotionalOfferData as $data) {
  $EncryptStoreID = $this->Encryption->encode($data['Item']['store_id']);
  $EncryptMerchantID = $this->Encryption->encode($data['Item']['merchant_id']);
  $EncryptItemId = $this->Encryption->encode($data['Item']['id']);
  $EncryptCatId = $this->Encryption->encode($data['Item']['category_id']);
  $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
  ?>
  <div class="deal-wrap">
  <a href="<?php echo $url; ?>">
  <div class="common-title-deal">
  <h3><?php echo substr($data['Item']['name'], 0, 70); ?></h3>
  </div>
  <div class="deal-img-wrap">
  <?php if (!empty($data['Offer']['offerImage']) && file_exists(WWW_ROOT . '/Offer-Image/thumb/' . $data['Offer']['offerImage'])) { ?>
  <img src="/Offer-Image/thumb/<?php echo $data['Offer']['offerImage']; ?>" alt="deals-img">
  <?php
  } elseif (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) {
  ?>
  <img src="/MenuItem-Image/deals-images/<?php echo $data['Item']['image']; ?>" alt="deals-img">
  <?php } else { ?>
  <img src = "img/no_images.jpg" alt = "deals-img">
  <?php } ?>
  </div>
  <div class="deals-detail clearfix">
  <p><?php echo substr($data['Offer']['description'], 0, 140); ?> <span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" title="<?php echo $data['Offer']['description']; ?>"></span></p>
  </div>
  </a>
  </div>
  <?php }
  ?>
  </div>
  </div>
  <?php //} */
?>
<!--<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>-->
<?php
if (!empty($promotionalOfferData)) {
    $EncryptStoreID = $this->Encryption->encode($store_data_app['Store']['id']);
    $EncryptMerchantID = $this->Encryption->encode($store_data_app['Store']['merchant_id']);
    $dealsstr = '';
    foreach ($promotionalOfferData as $promotional) {
        if (!empty($promotional['Item']['Category'])) {
            $EncryptItemId = $this->Encryption->encode($promotional['Offer']['item_id']);
            $EncryptCatId = $this->Encryption->encode($this->common->getCategoryID($promotional['Offer']['item_id']));
            $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
            $dealsstr.= "<li>";
            if (!empty($promotional['Offer']['offerImage']) && file_exists(WWW_ROOT . '/Offer-Image/' . $promotional['Offer']['offerImage'])) {
                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/Offer-Image/" . $promotional['Offer']['offerImage'];
                $dealsstr.="'></a></div>";
            } elseif (!empty($promotional['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $promotional['Item']['image'])) {

                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/deals-images/" . $promotional['Item']['image'];
                $dealsstr.="'></a></div>";
            } else {
                $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src = '/img/no_images.jpg' alt = 'deals-img'></a></div>";
            }

            $dealsstr.="<div class='dasol-txt-frame'><h3>" . $this->Html->link(ucfirst($promotional['Item']['name']), $url) . "</h3>";
            $dealsstr.= "<small> - " . $this->Html->link($promotional['Offer']['description'], $url) . "</small>";
            $dealsstr.= $this->Html->link('Add to Cart', $url, array('class' => 'add-cart theme-bg-1 theme-border-1'));
            $dealsstr.="</div></li>";
        }
    }
    echo $dealsstr;
}
?>