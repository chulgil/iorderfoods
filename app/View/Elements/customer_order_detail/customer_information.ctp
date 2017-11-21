<!--******************Personal information area start here***********************-->
<div role="tabpanel" class="tab-pane active" id="personal">
    <table class="table table-bordered table-hover table-striped tablesorter">
        <thead>
            <tr>	    
                <th  class="th_checkbox">Name</th>
                <th  class="th_checkbox">Email</th>
                <th  class="th_checkbox">Contact No.</th>
            </tr>
        </thead>
        <tbody class="dyntable">
            <?php
            $i = 0;
            $class = ($i % 2 == 0) ? ' class="active"' : '';
            ?>
            <tr>	   
                <td>
                    <?php
                    if (!empty($userDetail['User']['fname'])) {
                        echo $userDetail['User']['fname'] . " " . $userDetail['User']['lname'];
                    } else {
                        echo "NA";
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if (!empty($userDetail['User']['email'])) {
                        echo $userDetail['User']['email'];
                    } else {
                        echo "NA";
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if (!empty($userDetail['User']['phone'])) {
                        echo $userDetail['User']['phone'];
                    } else {
                        echo "NA";
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>