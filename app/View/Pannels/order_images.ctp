<?php
if (DESIGN == 1) {
    echo $this->element('design/aaron/gallery');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/gallery');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/gallery');
} elseif (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/order_images');
}
?>
<script>
    $(document).ready(function () {
        $('#test').on('click', function () {
            var pageNo = $("#pageId").val();
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'pannels', 'action' => 'orderImagesAjax')); ?>",
                type: "Post",
                dataType: 'html',
                data: {pageNo: pageNo},
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
                success: function (result) {
                    if (result == 1) {
                        $(".show-more-pic").remove();
                        return false;
                    }
                    $('.p-list').append(result);
                    var sum = parseInt($("#pageId").val()) + 1;
                    $("#pageId").val(sum);
                }
            });
        });
    });
</script>



