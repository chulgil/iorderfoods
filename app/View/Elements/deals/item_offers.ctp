<?php /*  if (!empty($itemOfferData)) { ?>
  <div class="row">
  <div class="col-sm-12 col-xs-12">
  <?php
  foreach ($itemOfferData as $data) {
  $EncryptStoreID = $this->Encryption->encode($data['ItemOffer']['store_id']);
  $EncryptMerchantID = $this->Encryption->encode($data['ItemOffer']['merchant_id']);
  $EncryptItemId = $this->Encryption->encode($data['ItemOffer']['item_id']);
  $EncryptCatId = $this->Encryption->encode($data['ItemOffer']['category_id']);
  $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
  ?>
  <div class="deal-wrap">
  <a href="<?php echo $url; ?>">
  <div class="common-title-deal">
  <h3><?php echo substr($data['Item']['name'], 0, 70); ?></h3>
  </div>
  <div class="deal-img-wrap">
  <?php if (!empty($data['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/deals-images/' . $data['Item']['image'])) { ?>
  <img src="/MenuItem-Image/deals-images/<?php echo $data['Item']['image']; ?>" alt="deals-img">
  <?php
  } else {
  ?>
  <img src = "img/no_images.jpg" alt = "deals-img">
  <?php }
  ?>
  </div>
  <div class="deals-detail clearfix">
  <?php
  $numSurfix = $this->Common->addOrdinalNumberSuffix($data['ItemOffer']['unit_counter']);
  if (!empty($numSurfix)) {
  echo "<p>Buy " . ($data['ItemOffer']['unit_counter'] - 1) . " unit and get the " . $numSurfix . " Item free on " . $data['Item']['name'] . '.</p>';
  } else {
  echo "<p>Buy " . ($data['ItemOffer']['unit_counter'] - 1) . " get 1 free.</p>";
  }
  ?>

  <!--                            <p><?php echo 'On the Purchase of ' . ($data['ItemOffer']['unit_counter'] - 1) . ' units of ' . $data['Item']['name'] . ' , 1 unit will be free of cost.'; ?></p>-->
  </div>
  </div>
  </a>
  <?php }
  ?>
  </div>
  </div>
  <?php //} */
?>
<?php
if (!empty($itemOfferData)) {
    $EncryptStoreID = $this->Encryption->encode($store_data_app['Store']['id']);
    $EncryptMerchantID = $this->Encryption->encode($store_data_app['Store']['merchant_id']);
    $dealsstr = '';
    foreach ($itemOfferData as $itemOffer) {
        $EncryptItemId = $this->Encryption->encode($itemOffer['ItemOffer'] ['item_id']);
        $EncryptCatId = $this->Encryption->encode($itemOffer['ItemOffer']['category_id']);
        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID, $EncryptItemId, $EncryptItemId, $EncryptCatId), true);
        $dealsstr.= "<li>";
        if (!empty($itemOffer['Item']['image']) && file_exists(WWW_ROOT . '/MenuItem-Image/' . $itemOffer['Item']['image'])) {
            $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/MenuItem-Image/" . $itemOffer['Item']['image'];
            $dealsstr.="'></a></div>";
        } else {
            $dealsstr.="<div class='dasol-img-frame'><a href='" . $url . "' class='img-item'><img src='/img/no_images.jpg'></a></div>";
        }
        $dealsstr.="<div class='dasol-txt-frame'><h3>" . $this->Html->link(ucfirst($itemOffer['Item']['name']), $url) . "</h3>";
        $numSurfix = $this->Common->addOrdinalNumberSuffix($itemOffer['ItemOffer']['unit_counter']);
        if (!empty($numSurfix)) {
            $dealsstr.= "<small>" . $this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter'] - 1) . " unit and get the " . $numSurfix . " Item free on " . $itemOffer['Item']['name'] . '.', $url) . "</small>";
            $dealsstr.= $this->Html->link('Add to Cart', $url, array('class' => 'add-cart theme-bg-1 theme-border-1'));
        } else {
            $dealsstr.= "<small>" . $this->Html->link("Buy " . ($itemOffer['ItemOffer']['unit_counter'] - 1) . " get 1 free.", $url) . "</small>";
            $dealsstr.= $this->Html->link('Add to Cart', $url, array('class' => 'add-cart theme-bg-1 theme-border-1'));
        }
        $dealsstr.="</div></li>";
    }
    echo $dealsstr;
}
?>