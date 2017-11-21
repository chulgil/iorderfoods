<style type="text/css">
    .borderLine{
        border: 1px solid #000;
        padding: 20px;
        text-align: center;
        cursor: pointer;
    }
    .lPadding{
        padding-top: 15px;
        text-align: left;
    }
    .activeLayout{
        background-color: #FF4D4D;
    }
    .processLayout{
        background-color: #337ab7;
    }
    span.label{
        cursor: pointer;
        color: #ffffff;
    }
    span.label a{
        color: #ffffff;
    }
</style>
<div class="row">
    <div class="col-lg-6">
        <h3>Layout</h3> 
    </div> 
    <div class="col-lg-6">
        <?php
        echo $this->Form->button('Back', array('type' => 'button', 'onclick' => "window.location.href='/hqconfigurations/htmlModule/".$this->Encryption->encode($masterContent['MasterContent']['merchant_content_id'])."'", 'class' => 'btn btn-default pull-right'));
        ?>  
    </div>
</div>   
<div class="row">
    <div class="col-lg-6">
        <?php echo $this->Session->flash(); ?>   
    </div>
</div>
<hr>
<div class="row">
    <?php if (!empty($cLayout)) { ?>
        <div class="col-xs-12">
            <?php
            foreach ($cLayout as $cLayoutData) {
                ?>
                <div class="col-xs-6 clearfix">
                    <div class="lPadding">
                        <b><?php echo $cLayoutData['ContentLayout']['name']; ?></b>
                    </div>
                    <?php
                    if (!empty($cLayoutData['LayoutBox'])) {
                        //pr($cLayoutData);
                        $PROCESSFLAG = $finalSubmit = 0;
                        foreach ($cLayoutData['LayoutBox'] as $lBox) {
                            if ($lBox['ratio'] == 100.00) {
                                $class = 'col-xs-12 borderLine';
                                $dVal = '1';
                            } elseif ($lBox['ratio'] == 50.00) {
                                $class = "col-xs-6 borderLine";
                                $dVal = '1/2';
                            } elseif ($lBox['ratio'] == 33.33) {
                                $class = "col-xs-4 borderLine";
                                $dVal = '1/3';
                            } elseif ($lBox['ratio'] == 25.00) {
                                $class = "col-xs-3 borderLine";
                                $dVal = '1/4';
                            } elseif ($lBox['ratio'] == 66.66) {
                                $class = "col-xs-8 borderLine";
                                $dVal = '2/3';
                            } elseif ($lBox['ratio'] == 75.00) {
                                $class = "col-xs-9 borderLine";
                                $dVal = '3/4';
                            }
                            if (!empty($activeData) && in_array($lBox['id'], $activeData)) {
                                $PROCESSFLAG = 1;
                                $class .= ' activeLayout';
                                $finalSubmit = 1;
                            }
                            if (!empty($processData) && in_array($lBox['id'], $processData)) {
                                $PROCESSFLAG = 1;
                                $class .= ' processLayout';
                            }
                            ?>
                            <div class="<?php echo $class; ?>" data-merchant-content-id="<?php echo $masterContent['MasterContent']['merchant_content_id'] ?>" data-id="<?php echo $lBox['id']; ?>" data-content-id="<?php echo $lBox['content_layout_id']; ?>" id="<?php echo 'cl' . $lBox['id']; ?>"><?php echo $dVal; ?></div>
                            <?php
                        }
                        if ($PROCESSFLAG) {
                            ?>
                            <span class="label label-primary"><?php echo $this->Html->link('Preview', array('controller' => 'hqconfigurations', 'action' => 'previewLayout', $this->params->query['content'], $cLayoutData['ContentLayout']['id']), array('target' => "_blank", 'escape' => false)); ?></span>
                            <?php if (empty($finalSubmit)) { ?>
                                <span class="label label-success"><?php echo $this->Html->link('Activate', array('controller' => 'hqconfigurations', 'action' => 'finalSubmit', $this->params->query['content'], $cLayoutData['ContentLayout']['id'], 1), array('confirm' => 'Are you sure?', 'escape' => false)); ?></span>
                            <?php } else {
                                ?>
                                <span class="label label-danger"><?php echo $this->Html->link('Deactivate', array('controller' => 'hqconfigurations', 'action' => 'finalSubmit', $this->params->query['content'], $cLayoutData['ContentLayout']['id'], 0), array('confirm' => 'Are you sure?', 'escape' => false)); ?></span>
                                <?php
                            }
                        } else {
                            ?>
                            <span>&nbsp</span>
                            <?php
                        }
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <!--        <div class="col-xs-12">
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center">Layout 1</div>
                    <div class="col-xs-12 borderLine">1</div>
                </div>
                <div class="col-xs-2"></div>
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 2</div>
                    <div class="col-xs-6 borderLine">1/2</div>
                    <div class="col-xs-6 borderLine">1/2</div>
                </div>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12">
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 3</div>
                    <div class="col-xs-4 borderLine">1/3</div>
                    <div class="col-xs-4 borderLine">1/3</div>
                    <div class="col-xs-4 borderLine">1/3</div>
                </div>
                <div class="col-xs-2"></div>
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 4</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                </div>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12">
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 5</div>
                    <div class="col-xs-8 borderLine">2/3</div>
                    <div class="col-xs-4 borderLine">1/3</div>
                </div>
                <div class="col-xs-2"></div>
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 6</div>
                    <div class="col-xs-4 borderLine">2/3</div>
                    <div class="col-xs-8 borderLine">1/3</div>
                </div>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12">
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 7</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-9 borderLine">3/4</div>
                </div>
                <div class="col-xs-2"></div>
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 8</div>
                    <div class="col-xs-9 borderLine">3/4</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                </div>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12">
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 9</div>
                    <div class="col-xs-6 borderLine">1/2</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                </div>
                <div class="col-xs-2"></div>
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 10</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-6 borderLine">1/2</div>
                </div>
            </div>
            <div class="col-xs-12"></div>
            <div class="col-xs-12">
                <div class="col-xs-5">
                    <div class="col-xs-12 text-center"> Layout 11</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                    <div class="col-xs-6 borderLine">1/2</div>
                    <div class="col-xs-3 borderLine">1/4</div>
                </div>
            </div>-->
</div><!-- /.row -->
<div class="modal fade" id="layoutModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">

        </div>

    </div>
</div>
<script type="text/javascript">
    $('#layoutModal').on('hidden.bs.modal', function () {
        location.reload();
    });
    $('.borderLine').click(function () {
        var layoutBoxId = $(this).data('id');
        var merchantContentId = $(this).data('merchant-content-id');
        var contentLayoutId = $(this).data('content-id');
        var masterContentId = "<?php echo $this->params->query['content']; ?>";
        $.ajax({
            type: 'post',
            url: '/hqconfigurations/getContentModal',
            data: {'layoutBoxId': layoutBoxId, 'contentLayoutId': contentLayoutId, 'masterContentId': masterContentId, 'merchantContentId': merchantContentId},
            async: false,
            success: function (result) {
                $(".modal-content").html(result);
                $('#layoutModal').modal('show');
            }
        });
    });
    $(document).on('click', '#saveContent', function (e) {
        e.stopImmediatePropagation();
        var active = $('#HomeContentLayoutBoxId').val();
        var con = $("#HomeContentGetContentModalForm iframe").contents().find("body").html();
        if (con) {
            $("#HomeContentContent").val(editor.getData());
            var formData = $("#HomeContentGetContentModalForm").serialize();
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'hqconfigurations', 'action' => 'saveLayoutContent')); ?>",
                async: false,
                data: {'formData': formData},
                beforeSend: function () {
                }, success: function (successResult) {
                    if (successResult) {
                        $('#layoutModal').modal('hide');
                        $('#cl' + active).addClass('processLayout');
                    }
                }, complete: function () {
                }
            });
        }
        e.preventDefault();
    });
</script>