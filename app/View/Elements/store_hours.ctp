<style>
        
        
        
        .theme-one .openhours { background-color:#e05b5e; }
        .theme-two .openhours { background-color:#ff3300; }
        .theme-three .openhours{ background-color: #45342c; }
        .theme-four .openhours{ background-color:#5f3711; }
        .theme-five .openhours{ background-color: #31241d; }
        .theme-six .openhours{ background-color: #e8e8e8; }
        .theme-seven .openhours{ background-color: #daa520; }
        .theme-eight .openhours{ background-color: #e8e8e8; }
        .theme-night .openhours{ background-color: #717f3e; }
        .theme-ten .openhours{ background-color: #f3f3f3; }        
        
        
        .theme-one .storehours { color:#fff; }        
        .theme-two .storehours { color:#fff; }
        .theme-three .storehours { color:#fff; }
        .theme-four .storehours { color:#fff; }
        .theme-five .storehours { color:#fcebd6; }
        .theme-six .storehours { color:#1b1b1b; }
        .theme-seven .storehours { color:#000; }
        .theme-eight .storehours { color:#000; }
        .theme-night .storehours { color:#fff; }
        .theme-ten .storehours { color:#000; }        
        
        .theme-one .openhead { color:#fff;padding:10px;font-size:17px; }        
        .theme-two .openhead { color:#fff;padding:10px;font-size:17px; }
        .theme-three .openhead { color:#fff;padding:10px;font-size:17px; }
        .theme-four .openhead { color:#fff;padding:10px;font-size:17px; }
        .theme-five .openhead { color:#fcebd6;padding:10px;font-size:17px; }
        .theme-six .openhead { color:#1b1b1b;padding:10px;font-size:17px; }
        .theme-seven .openhead { color:#000;padding:10px;font-size:17px; }
        .theme-eight .openhead { color:#000;padding:10px;font-size:17px; }
        .theme-night .openhead { color:#fff;padding:10px;font-size:17px; }
        .theme-ten .openhead { color:#000;padding:10px;font-size:17px; }
        
        
        .call-us{
           margin-top: 10px;
        }
        
        .openhours {
            margin-top: 10px;
        }
        
        .openhours ul {
            padding: 10px;
        }
        .openhours ul li {
            border-bottom-style: solid;
            border-bottom-width: 1px;
            text-transform: uppercase;
            border-color: rgba(155, 155, 155, 0.4);            
            padding:2px;
        }
        .openhours div{
            display: block;
        }
/*        .openhours div label{
            font-size:12px;
            font-weight:bold;
            width:30px;
            vertical-align:top;
        } */
/*        .openhours div span{
            font-size:12px;
            float:right;
            width:85%;
        }*/
        .storehours{
            padding:2px !important;
            min-height:25px;
        }
        .storehours span{
            font-size:12px;
            font-weight:bold;
/*            width:30px;*/
            vertical-align:top;
            float: left;
            width: 15%;
        }
        
        .storehours p{
            display: inline-block;
            font-size: 12px;
            vertical-align: top;
            padding-left: 5px;
        }
        
    </style>
    
    <?php if(isset($availabilityInfo) && !empty($availabilityInfo)) { ?>
    <div class="openhours">
        <div class="openhead">OPEN HOURS</div>
        <ul class="clearfix">
        
        <?php
            $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');            
            foreach($days as $key =>$value){
        ?>
                <li><div href="#" class="storehours">
                        <span><?php echo $value; ?></span>
                        <p>
                        <?php
                            if($availabilityInfo[$key]['StoreAvailability']['is_closed']==1){
                                echo "Closed";
                            }else{
                                echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['start_time'])." - ";
                                if($store_break_data['Store']['is_break_time']==1){
                                    if($store_break_data['Store']['is_break1']==1){
                                        if($availabilityInfo[$key]['StoreBreak']['break1_start_time']!=$availabilityInfo[$key]['StoreBreak']['break1_end_time']){
                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_start_time']);echo "<br>";
                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_end_time'])." - ";
                                        }
                                    }
                                    if($store_break_data['Store']['is_break2']==1){
                                        if($availabilityInfo[$key]['StoreBreak']['break2_start_time']!=$availabilityInfo[$key]['StoreBreak']['break2_end_time']){
                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_start_time']);echo "<br>";
                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_end_time'])." - ";
                                        }
                                    }  
                                }
                                echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['end_time']);
                            }
                        ?>
                                        </p>

                    </div></li>
         <?php } ?>
        
        </ul>
    </div>
<?php } ?>