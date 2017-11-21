<div class="container">
    <h3>Tabs</h3>
    <ul class="nav nav-tabs">
        <li class="active"><?php echo $this->Html->link('Coupons',array('controller'=>'deals','action'=>'deals'));?></li>
        <li><?php echo $this->Html->link('Promotions',array('controller'=>'offers','action'=>'addOffer'));?></li>
        <li><?php echo $this->Html->link('Extended Offers',array('controller'=>'itemOffers','action'=>'add'));?></li>
    </ul>   
    <br>
</div>