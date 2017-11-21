<footer class="footer">
    <?php if (!empty($this->params['action']) && ($this->params['action'] != 'merchant')) { ?>
        <div class="click_order">
            <div class="container">
                <div class="col-sm-12">
                    <ul class="click-order-info">
                        <li><span class="c-info">Click, Order, Enjoy</span></li>
                        <li><span><i class="fa fa-phone"></i></span>
                            <span class="contact-info">Call us @ <?php echo $phone; ?></span></li>
<!--                        <li><a href="#!" class="touch-btn only-border">GET IN TOUCH</a></li>-->
                    </ul>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="footer-top">
        <div class="container">
            <div class="row">
                 <div class="col-xs-12">
                     <?php if (!empty($merchantList)) { 
                         
                         ?>
                          <ul>  
                                    <?php
                                    $i=0;
                                    foreach ($merchantList as $content) {
                                        
                                        if ($content['MerchantContent']['page_position'] == 2) {
                                            if($i==0){?>
                                                Site Link
                                            <?php }
                                            ?>
                                <div class="quick-links">><?php echo $this->Html->link(strtoupper($content['MerchantContent']['name']), array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?></div>
                                            <?php
                                            $i++;
                                        }
                                        
                                    }
                                    ?>
                                </ul>
                            
                            <?php
                        }
                        ?>
                 </div>
                <div class="col-xs-12 text-center">
                    <ul class="list-style-none">
                        <?php
                        if ($socialLinks) {
                            if (!empty($socialLinks['SocialMedia']['facebook'])) {
                                ?>
                                <li><a href="<?php echo $socialLinks['SocialMedia']['facebook']; ?>" target="_blank"><i class="fa fa-facebook-f" aria-hidden="true"></i></a></li>
                            <?php } if (!empty($socialLinks['SocialMedia']['twitter'])) { ?>
                                <li><a href="<?php echo $socialLinks['SocialMedia']['twitter']; ?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                            <?php } if (!empty($socialLinks['SocialMedia']['instagram'])) { ?>
                                <li><a href="<?php echo $socialLinks['SocialMedia']['instagram']; ?>" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                            <?php } if (!empty($socialLinks['SocialMedia']['yolo'])) { ?>
                                <li><a href="<?php echo $socialLinks['SocialMedia']['yolo']; ?>" target="_blank"><i class="fa fa-yelp" aria-hidden="true"></i></a></li>
                            <?php } if (!empty($socialLinks['SocialMedia']['google'])) { ?>
                                <li><a href="<?php echo $socialLinks['SocialMedia']['google']; ?>" target="_blank"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
                            <?php } if (!empty($socialLinks['SocialMedia']['pinterest'])) { ?>
                                <li><a href="<?php echo $socialLinks['SocialMedia']['pinterest']; ?>" target="_blank"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                    <div class="quick-links">
                        <a href="javascript:void(0);" class="termAndPolicy" data-name="Term">+Terms &amp; Conditions</a> <a href="javascript:void(0);" class="termAndPolicy" data-name="Policy">+Privacy Policy</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <p class="copright">&copy; BankCard Services, Inc. All Rights Reserved</p>
</footer>
<div class="modal fade" id="tAndPModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content" id="tAndPContent">
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', ".termAndPolicy", function () {
        var type = $(this).data('name');
        $.ajax({
            type: 'post',
            url: "<?php echo $this->Html->url(array('controller' => 'hqusers', 'action' => 'getTermsAndPolicyData')); ?>",
            data: {'type': type},
            beforeSend: function () {
                $.blockUI({css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }});
            },
            complete: function () {
                $.unblockUI();
            },
            success: function (response) {
                $("#tAndPContent").html(response);
                $("#tAndPModal").modal('show');
            }
        });
    });
</script>
