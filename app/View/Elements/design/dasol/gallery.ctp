<div class="title-bar">
    Gallery
</div>
<div class="main-container ">
    <div class="inner-wrap  photos ">
        <div class="photo-gallery clearfix">
            <ul class="p-list">
                <?php
                if (!empty($allReviewImages)) {
                    foreach ($allReviewImages as $image) {
                        if (!empty($image['StoreReviewImage']['image']) && file_exists(WWW_ROOT . '/storeReviewImage/thumb/' . $image['StoreReviewImage']['image'])) {
                            ?>
                            <li>
                                <div class="expand-img">
                                     <a href="javascript:void(0)" class="pop">
                                    <img class="example-image" src="/storeReviewImage/thumb/<?php echo $image['StoreReviewImage']['image']; ?>" alt="image-1" /></a>
                                    <span>
                                        <a class="pop" href="/storeReviewImage/<?php echo $image['StoreReviewImage']['image']; ?>" data-lightbox="example-1"></a>
                                    </span>                                
                                </div>
                            </li>
                            <?php
                        }
                    }
                }
                ?>
            </ul>
        </div>
        <div class="show-more-pic">
            <input type='hidden' id="pageId" value="2"/>
            <a class="no-color show-more-btn" id="test"> Show more result</a>
        </div>
    </div>      	
</div>