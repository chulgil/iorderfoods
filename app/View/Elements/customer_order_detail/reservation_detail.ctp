<!--******************Reservation area start here***********************-->
<div role="tabpanel" class="tab-pane" id="reservation">
    <div class="row">
        <div class="col-sm-12">
            <?php echo $this->element('pagination'); ?>
        </div>
    </div>
    <?php echo $this->element('show_pagination_count'); ?>
    <table class="table table-bordered table-hover table-striped tablesorter">
        <thead>
            <tr>	    
                <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.number_person', 'No. of person'); ?></th>
                <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.reservation_date', 'Reservation Date'); ?></th>
                <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.special_request', 'Special Request'); ?></th>
                <th  class="th_checkbox">Status</th>
            </tr>
        </thead>
        <tbody class="dyntable">
            <?php
            $i = 0;
            $class = ($i % 2 == 0) ? ' class="active"' : '';
            ?>
            <?php
            if (!empty($myBookings)) {
                foreach ($myBookings as $book) {
                    ?>
                    <tr >	    
                        <td>
                            <?php echo $book['Booking']['number_person']; ?>
                        </td>
                        <td>
                            <?php 
                            
                            if(!empty($book['Booking']['reservation_date'])){
                             echo $this->Dateform->us_format($this->Hq->storeTimezone(null, $book['Booking']['reservation_date'], null, $book['Booking']['store_id'])); 
                        }else{
                             echo $this->Dateform->us_format($book['Booking']['reservation_date']);
                        }
                            
                            
                            ?>
                        </td>
                        <td>
                            <?php
                            if (empty($book['Booking']['special_request'])) {
                                echo "--";
                            } else {
                                echo ucfirst($book['Booking']['special_request']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo $book['BookingStatus']['name']; ?>
                        </td>

                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td class="text-center" colspan="5">' . __('No reservation request found') . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>

<!--******************Reservation area end here***********************-->