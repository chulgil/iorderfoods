<div class="panel panel-default">
    <div class="panel-heading active">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion1" href="#collapsesix">
                SPECIAL COMMENT
                <span class="arrow-down">
                    <i class="indicator fa fa-angle-up fa-2x" aria-hidden="true"></i>
                </span>
            </a>
        </h4>
    </div>
    <?php
    $guestUserDetail = $this->Session->check('GuestUser');
    $specialComment = $this->Session->check('Cart.comment');
    $userId = AuthComponent::User('id');
    if (!empty($userId) || !empty($guestUserDetail)) {
        ?>
        <div id="collapsesix" class="panel-collapse collapse">
            <div class="panel-body">
                <div id="flashSpecialComment"></div>
                <div class="comment-box">
                    <?php echo $this->Form->input('User.comment', array('type' => 'textarea', 'label' => false, 'class' => 'inbox', 'value' => $this->Session->read('Cart.comment'))); ?>
                </div>
                <div>
                    <button class="cont-btn btn pull-right saveComment theme-bg-1" type="button"><?php echo ($specialComment) ? 'UPDATE COMMENT' : 'SAVE COMMENT'; ?></button>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<script type="text/javascript">
    $(document).on('click', '.saveComment', function () {
        var specialComment = $('#UserComment').val();
        if (specialComment != '') {
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'payments', 'action' => 'saveSpecialComment')); ?>",
                data: {'specialComment': specialComment},
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
                success: function (successResult) {
                    data = JSON.parse(successResult);
                    $("#errorPop").modal('show');
                    $("#errorPopMsg").html(data.msg);
                    //$("#flashSpecialComment").html('<div class="message message-success alert alert-success" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="close">×</a> ' + data.msg + '</div>');
                }
            });
        }
    });
</script>