<style>
    .date-format{
        font-size: 10px;
        font-style: italic;
        padding-top: 16px;
    }
</style>
<div class="row">
    <div class="sidebar">
        <div class="mini-submenu">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </div>
        <div class="list-group">
            <span class="list-group-item text-center">
                <h3>Recent Post</h3>
                <span class="pull-right" id="slide-submenu">
                    <i class="fa fa-times"></i>
                </span>
            </span>
            <?php
            if (!empty($merchantNewsletterRecentPost)) {
                $i = 0;
                foreach ($merchantNewsletterRecentPost as $rList) {
                    $name = $rList['MerchantNewsletter']['name'];
                    $active = "";
                    if ($this->Encryption->encode($rList['MerchantNewsletter']['id']) == @$this->params['url']['val']) {
                        $active = 'active';
                    }
                    $val = substr($name, 0, 20) .'<span class="pull-right date-format">' . date("M-d-Y", strtotime($rList['MerchantNewsletter']['created'])) . '</span>';
                    echo $this->Html->link($val, array('controller' => 'hqusers', 'action' => 'newsletter?val='.$this->Encryption->encode($rList['MerchantNewsletter']['id'])), array('escape' => false, 'class' => 'list-group-item ' . @$active));
                    $i++;
                    if ($i == 3)
                        break;
                }
            }
            ?>
        </div>
        <div class="list-group">
            <span class="list-group-item text-center">
                <h3>Archive</h3>
                <span class="pull-right" id="slide-submenu">
                    <i class="fa fa-times"></i>
                </span>
            </span>
            <?php
            if (!empty($merchantNewsletterArchive)) {
                foreach ($merchantNewsletterArchive as $aList) {
                    $monthName = $aList[0]['monthname'];
                    $month = $aList[0]['month'];
                    $year = date("Y", strtotime($aList['MerchantNewsletter']['created']));
                    $active = "";
                    if (($month == @$this->params->pass[0]) && ($year == @$this->params->pass[1])) {
                        $active = 'active';
                    }
                    $val = '<i class="fa fa-folder-open-o"></i>' . $monthName . ' ' . $year . '<span class="badge">' . @$aList[0]['count'] . '</span>';
                    echo $this->Html->link($val, array('controller' => 'hqusers', 'action' => 'newsletter', $month, $year), array('escape' => false, 'class' => 'list-group-item ' . @$active));
                }
            }
            ?>
        </div>
    </div>
</div>