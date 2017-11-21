<!--<style>
    .openImage {
        margin-left: 358px;
        width: 53%;
    }
    .item{
        width: 53%;
        height: 40%;
    }
</style>-->
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
    <!-- Wrapper for slides -->
    <div class="carousel-inner">
        <?php
        $i = 1;
        foreach ($imgArr as $arrImg) {
            ?>
            <div class="item <?php echo($i) ? "active" : ""; ?>">
                <div class="img-sz-ps" style="height:260px; display: table; width:100%">
                    <div style="height:100%; text-align: center; display: table-cell; vertical-align: middle; width:100%">
                <?php echo $this->Html->image('/storeReviewImage/' . $arrImg, array("class" => "openImage")); ?>
            </div>
                </div>
            </div>
            <?php
            $i = 0;
        }
        ?>
    </div>
    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
    </a>
</div>


<style>
.img-sz-ps img{max-height: 260px;max-width: 100%}    
</style>