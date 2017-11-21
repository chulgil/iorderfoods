<div class="row">
    <div class="col-lg-12">
        <?php
            $order = $orderAllData;
        if(!empty($order)){ echo $this->element('show_pagination_count'); }
        //pr($order);
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
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_number', 'Order No.',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name',array('url'=>@$url));?></th> 
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name',array('url'=>$url));?></th>
<!--                        <th  class="th_checkbox">Store Name</th>-->
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Item.name', 'Items',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.amount', 'Amount',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderItem.tax_price', 'Tax ($)',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.tip', 'Tip ($)',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.phone', 'Phone',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.address', 'Address',array('url'=>@$url));?></th>			
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email',array('url'=>@$url));?></th>
                    <!--<th  class="th_checkbox">Delivery/Pickup Time</th>-->
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Segment.name', 'Order Type',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.pickup_time', 'Order Date',array('url'=>@$url));?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.created', 'Created',array('url'=>@$url));?></th>
                </tr>
            </thead>

            <tbody class="dyntable">
            <?php  
            if(!empty($order)){
                $i = 0;
                foreach($order as $key => $data){        
                    $class = ($i%2 == 0) ? ' class="active"' : '';
                    $EncryptOrderID=$this->Encryption->encode($data['Order']['id']); 
                ?>
                <tr>  
                    <td>
                        <?php echo  $this->Html->link($data['Order']['order_number'],array('controller'=>'hqreports','action'=>'orderDetail',$EncryptOrderID)); ?>

                    </td>
                    <td>
                        <?php
                        if(isset($data['DeliveryAddress']))
                        {
                            if(isset($data['DeliveryAddress']['name_on_bell']) && !empty($data['DeliveryAddress']['name_on_bell'])){
                                echo $data['DeliveryAddress']['name_on_bell'];
                            }
                        }
                        else if(isset($data['User']))
                        {
                            if(isset($data['User']['fname'])){
                                echo $data['User']['fname'] ." ".$data['User']['lname'];
                            } else {
                                echo '-';
                            }
                        } else {
                            if(isset($data['Order']['DeliveryAddress']['name_on_bell']) && !empty($data['Order']['DeliveryAddress']['name_on_bell'])){
                                echo $data['Order']['DeliveryAddress']['name_on_bell'];
                            }else if(isset($data['Order']['User']['fname'])){
                                echo $data['Order']['User']['fname'] ." ".$data['Order']['User']['lname'];
                            } else {
                                echo '-';
                            }
                        }
			?>
                    </td>
                    <td>
                        <?php echo (isset($data['Order']['Store']['store_name']) ? $data['Order']['Store']['store_name'] : '-');?>
                    </td>
                    <td><?php
			$i=0;
			$items="-";
                        if(isset($data['Order']['OrderItem']))
                        {
                            foreach($data['Order']['OrderItem'] as $key => $item){
                                if($i==0){
                                        $items=$item['Item']['name'];
                                }else{
                                        $items.=", ".$item['Item']['name'];	
                                }
                                $i++;
                            }
                        }
			echo "<span title='".$items."'>".wordwrap($items)."</span>";
			?>
                    </td>
                    <td>
                        <?php
			if($data['Order']['coupon_discount'] >0){
                            $total_amount =  $data['Order']['amount'] - $data['Order']['coupon_discount'];
                            echo $this->Common->amount_format($total_amount);
			}else{	
                            echo $this->Common->amount_format($data['Order']['amount']);
			}
			?>
                    </td>
                    <td>
                        <?php
                        $taxPrice = 0;
                        if(isset($data['Order']['OrderItem']))
                        {
                            foreach ($data['Order']['OrderItem'] as $dataOrderItem)
                            {
                                $taxPrice += $dataOrderItem['tax_price'];
                            }
                        }
                        $taxPrice = (isset($taxPrice) ? $this->Common->amount_format($taxPrice) : $this->Common->amount_format(0));
                        echo $taxPrice;
                        ?>
                    </td>
                    <td>
                        <?php echo (isset($data['Order']['tip']) ? $this->Common->amount_format($data['Order']['tip']) : $this->Common->amount_format(0)); ?>
                    </td>
                    <td>
                        <?php
			if(isset($data['Order']['DeliveryAddress']['phone']) && !empty($data['Order']['DeliveryAddress']['phone'])){
                            echo $data['Order']['DeliveryAddress']['phone'] ;
			} else if(isset($data['Order']['User']['phone'])){
                            echo  $data['Order']['User']['phone'];	
			} else {
                            echo '-';
                        }
			?>
                    </td>
                    <td>
                        <?php
			if(!empty($data['Order']['Segment']['id']) && $data['Order']['Segment']['id'] == 2){
                            echo @$data['Order']['Segment']['name'];
			}else{
                            if(!empty($data['Order']['DeliveryAddress']['address'])){
                                echo @$data['Order']['DeliveryAddress']['address'] ;
                            }else{
                                echo (!empty($data['Order']['User']['address']))?$data['Order']['User']['address']:'';	
                            }	
			}
			?>
                    </td>
                    <td>
                        <?php
			if(!empty($data['Order']['DeliveryAddress']['email'])){
                            echo $data['Order']['DeliveryAddress']['email'] ;
			}else{
                            echo (!empty($data['Order']['User']['email']))?$data['Order']['User']['email']:'' ;	
			}
                        ?>
                    </td>
                    <td><?php  echo @$data['Order']['Segment']['name'] ; ?></td>
                    <td>
			<?php
			if($data['Order']['seqment_id'] == 2){
                            $pickupTime=$this->Dateform->us_format($data['Order']['pickup_time']);
		echo ($data['Order']['pickup_time']!='0000-00-00 00:00:00' && $data['Order']['pickup_time']!='')?$pickupTime:"-";
			}
			if($data['Order']['seqment_id'] == 3){
                            if($data['Order']['is_pre_order'] == 0){
                                $deliveryTime = $this->Dateform->us_format($data['Order']['created']);
                                echo ($data['Order']['created']!='0000-00-00 00:00:00' && $data['Order']['created']!='')?$deliveryTime:"-";
                            }else{
                                echo ($data['Order']['pickup_time']!='0000-00-00 00:00:00' && $data['Order']['pickup_time']!='')?$this->Dateform->us_format($data['Order']['pickup_time']):"-";	
                            }
			}
			?>
                    </td>
                    <td><?php  echo $this->Dateform->us_format($data['Order']['created']) ; ?></td>
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