<?php
$protocol = 'http';
if (isset($_SERVER['HTTPS'])) {
    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
        $protocol = 'https';
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h3>Store Printer List</h3>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <div class="row"></br></div>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox">Merchant name</th>
                        <th  class="th_checkbox">Store Name</th>
                        <th  class="th_checkbox">Machine Name</th>
                        <th  class="th_checkbox">Printer Version</th>
                        <th  class="th_checkbox">Online On/Off</th>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptStoreID = $this->Encryption->encode($data['Store']['id']);
                            ?>
                            <tr>
                                <td><?php echo $data['Merchant']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td><?php echo $data['StorePrinterStatus']['machine_name']; ?></td>
                                <td><?php echo $data['StorePrinterStatus']['current_version']; ?></td>
                                <td>
                                    <?php
                                    if ($data['StorePrinterStatus']['is_active']) {
                                        echo '<span style="color:blue;">On</span>';
                                    } else {
                                        echo '<span style="color:red;">Off</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
