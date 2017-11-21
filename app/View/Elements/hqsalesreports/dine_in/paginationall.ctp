<div class="row">
    <div class="col-lg-12">
        <?php
        if(!empty($dineInData)){ echo $this->element('show_pagination_count'); }
        ?>
        <table class="table table-bordered table-hover table-striped tablesorter">
            <thead>
		<?php
                $url=array();
                if(isset($paginationdata)){				
                      @$url=$paginationdata;
                      echo $url;
                }
		?>
                <tr>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name',array('url'=>@$url));?></th> 
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.reservation_date', 'Reservation Date',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.reservation_date', 'Reservation Time',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.number_person', 'No. of Persons',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.special_request', 'Special Request',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.phone', 'Phone #',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('BookingStatus.id', 'Status',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.created', 'Created Date',array('url'=>@$url));?></th>
                </tr>
            </thead>

            <tbody class="dyntable">
            <?php  
            if(!empty($dineInData)){
                $i = 0;
                foreach($dineInData as $key => $data){
                    $class = ($i%2 == 0) ? ' class="active"' : '';
                ?>
                <tr>
                    <td>
                        <?php
                        if(isset($data['User']))
                        {
                            if(isset($data['User']['fname'])){
                                echo $data['User']['fname'] ." ".$data['User']['lname'];
                            } else {
                                echo '-';
                            }
                        }
			?>
                    </td>
                    <td><?php
			echo $this->Dateform->us_format($data['Booking']['reservation_date']);
			?>
                    </td>
                    <td>
                        <?php
			echo date('h:i A', strtotime($data['Booking']['reservation_date']));
			?>
                    </td>
                    <td>
                        <?php
                        echo (isset($data['Booking']['number_person']) ? $data['Booking']['number_person'] : '0');
                        ?>
                    </td>
                    <td>
                        <?php
                        $comments = (strlen($data['Booking']['special_request']) > 30 ? substr($data['Booking']['special_request'], 0, 30) . '...' : $data['Booking']['special_request']);
                        ?>
                        <p title="<?php echo $data['Booking']['special_request'];?>" data-toggle="tooltip"><?php echo $comments;?></p>
                    </td>
                    <td>
                        <?php
			if(isset($data['User']['phone'])){
                            echo  $data['User']['phone'];	
			} else {
                            echo '-';
                        }
			?>
                    </td>
                    <td>
                        <?php
                            echo (!empty($data['User']['email']))?$data['User']['email']:'' ;
                        ?>
                    </td>
                    <td><?php  echo @$data['BookingStatus']['name'] ; ?></td>
                    <td><?php  echo $this->Dateform->us_format($data['Booking']['created']) ; ?></td>
                </tr>
		<?php 
                $i++;
                }
            }else{
                ?>
                <tr>
                    <td colspan="13" style="text-align: center;">
                        No record available
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <div class="paginator paging_full_numbers page-all-paginator" id="example_paginate" style="padding-top:10px">
        <?php
        $paginationParam = $this->Paginator->params();
        $pageCount = (isset($paginationParam['pageCount']) ? $paginationParam['pageCount'] : 0);
        if($pageCount > 1){
            $url=array();
            if(isset($paginationdata)){				
                  $url = $paginationdata;
            }
            echo $this->Paginator->first('First',array('url'=>$url));
            // Shows the next and previous links
            echo $this->Paginator->prev('Previous',array('url'=>$url));
            // Shows the page numbers
            echo $this->Paginator->numbers(array('url'=>$url));
            echo $this->Paginator->next('Next',array('url'=>$url));
            // prints X of Y, where X is current page and Y is number of pages
            //echo $this->Paginator->counter();
            echo $this->Paginator->last('Last',array('url'=>$url));
        }
        ?>
        </div>
    </div>
</div>
</div>
<?php echo $this->Html->css('pagination'); ?>
<?php
$page = $this->Paginator->current();
$defaultPage = (isset($page) && $page != '' && $page != 0 ? $page : 1);
?>
<script>
    var defaultPage = '<?php echo $defaultPage;?>';
    $(document).ready(function(){
        $(".page-all-paginator a").click(function(e){
            e.preventDefault();
            var page = $.urlParam(this.href,'/');
            var page = $.urlParam(page,':');
            
            fetchPaginationAllData(page);
            return false;
        });
        
        
        $(".th_checkbox a").click(function(e){
            e.preventDefault();
            var sort = $.urlParam(this.href,'/','2');
            var sort = $.urlParam(sort,':','1');
            
            var sort_direction = $.urlParam(this.href,'/','1');
            var sort_direction = $.urlParam(sort_direction,':','1');
            //console.log(sort + '======' + sort_direction)
            fetchPaginationAllData(defaultPage, sort, sort_direction);
            return false;
        });
    });
    
    
    $.urlParam = function(url,delimeter, c = 1){
        var param = '';
        if(url.length > 0)
        {
            param = url.split(delimeter);
            if(param.length > 0){
                return param[param.length-c];
            }
        }
    }
</script>
<style>
#example_paginate span:nth-last-child(2) {padding: 2px 8px;}
</style>