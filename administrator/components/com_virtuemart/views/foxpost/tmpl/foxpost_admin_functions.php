<?php
// SEND 1 ORDER TO THE SERVER
        
        

    
       if ((@$_POST['send_data_this'] == 1) AND (@$_POST['mass']) != 1) {
            $string = (@$_POST['send_datas_this']);
            // Check if already posted // 
            $db2 = JFactory::getDBO();
            $q2 = "SELECT parcel_detail FROM #__virtuemart_shipment_plg_foxpost WHERE virtuemart_order_id='".@$_POST['barcodeToPostThis']."'";
            $db2->setQuery($q2);
            $currOrder=$db2->loadObject();
            $need_up=$currOrder->parcel_detail;
            $needs_check=json_decode($currOrder->parcel_detail);              
           
     


               $need_up=substr($need_up, 0, -1).$add_string;  
                $dbUp = JFactory::getDBO();
                $qUp = "UPDATE #__virtuemart_shipment_plg_foxpost SET variables=0 WHERE virtuemart_order_id='".@$_POST['barcodeToPostThis']."' LIMIT 1";    
                $dbUp->setQuery($qUp);
                $dbUp->query();
 

       
            }

 ?>
