<?php echo $this->Html->script('lightbox/lightbox.js');?>
<?php echo $this->Html->css('lightbox/lightbox.css');?>

<?php $i=0;foreach ($imgArr as $arrImg) {
    ?>
    <?php if (file_exists(WWW_ROOT . '/storeReviewImage/' . $arrImg)) {
        ?>
        <?php echo $this->Html->link($this->Html->image('/storeReviewImage/' . $arrImg, array('class' => '')), '/storeReviewImage/' . $arrImg, array('class' => '', 'data-lightbox' => 'gallery', 'id' => 'gallery_img_' . $i, 'escape' => false, 'data-title' => '')); ?>
    <?php }
    ?>
<?php $i++;}
?>