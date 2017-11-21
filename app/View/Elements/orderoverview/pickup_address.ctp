<div class="store-contact-info-ele hidden">
    <?php $store_data = $this->Common->getStoreDetail($this->Session->read('store_id'));
    ?>
    <h3>Pickup Address</h3>
    <p><?php echo ucfirst($store_data['Store']['address']); ?></p>
    <p><?php echo ucfirst($store_data['Store']['city']) . ', ' . ucfirst($store_data['Store']['state']) . ' ' . $store_data['Store']['zipcode']; ?></p>
    <p><?php echo "Tel: " . $store_data['Store']['phone']; ?></p>
    <p><?php
        if (!empty($store_data['Store']['display_fax'])) {
            $fno = $store_data['Store']['display_fax'];
            //$str1 = substr($fno, 0, -10);
            $str2 = sprintf("(%s) %s-%s", substr($fno, 0, 3), substr($fno, 3, 3), substr($fno, 6));
            echo "Fax: " . $str2;
        }
        ?>
    </p>
    <p><?php
        if (!empty($store_data['Store']['display_email'])) {
            echo $store_data['Store']['display_email'];
        }
        ?>
    </p>
</div>