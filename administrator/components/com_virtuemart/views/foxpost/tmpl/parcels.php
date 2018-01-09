<?php
##############################
#Copyright 2015 Foxpost Zrt  #   
#2015.02.05 ToHR             #
#Foxpost Parcels Widget Core #
#Orders Parcels              #
##############################
defined ('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea($this);
JPluginHelper::importPlugin('vmpayment');

 require (JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'foxpost' . DS . 'tmpl' . DS . 'foxpost_admin_functions.php');
        
?>
<script type="text/javascript">
function checkAll()
{
     var checkboxes = document.getElementsByTagName('input'), val = null;    
     for (var i = 0; i < checkboxes.length; i++)
     {
         if (checkboxes[i].type == 'checkbox')
         {
             if (val === null) val = checkboxes[i].checked;
             checkboxes[i].checked = val;
         }
     }
 }
<?php if (!empty($_GET['file'])) { $url=JUri::root().'plugins/vmshipment/foxpost/csv/'.$_GET['file']; ?>
           var url = '<?php echo $url; ?>';
            window.open(url, '_blank');   
            
            window.location.href = ('index.php?option=com_virtuemart&view=foxpost');
    
<?php } ?>
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="mass" value="1">
    <div id="header">
        <div id="filterbox" style="margin-left: 10px;">
            <table >
                <tr>
                    <td align="right" width="100%" >
                      <!--  <?php echo $this->displayDefaultViewSearch ('name'); ?> -->
                       <!-- <?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_PARCEL_STATUS') . ':' . $this->lists['state_list']; ?> -->
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=""><?php 
                       if ($countedRows > 0) {

                      //echo  JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=foxpost&task=foxpost_missing_products_details'), JText::_ ('COM_VIRTUEMART_FOXPOST_PRODUCT_DETAILS').' ( '.$countedRows.' )' , array('title' => JText::_ ('COM_VIRTUEMART_FOXPOST_PRODUCT_DETAILS')));

                       }
                       ?>
                       </a>

                    </td>
                </tr>
            </table>
        </div>
        <div id="resultscounter" style="margin-left: 10px;"><?php echo $this->pagination->getResultsCounter (); ?>
         <?php
                         $model = VmModel::getModel('paymentmethod');
                         $payments = $model->getPayments();
                         $opts='<option value="0">'.JText::_ ('COM_VIRTUEMART_FOXPOST_SELECT_PAYMENT_METHOD').'</option>';
                         foreach($payments as $payment) {

                            $opts.='<option value="'.$payment->virtuemart_paymentmethod_id.'">'.$payment->payment_name.'</option>';
                            
                         }
     
                        ?>
                        
                        &nbsp;&nbsp;&nbsp;&nbsp;<select name="filter" id="filter" width="200" onchange="javascript:window.top.location=('index.php?option=com_virtuemart&view=foxpost&paym='+jQuery('#filter').val());">
                            <?php print $opts; ?>
                        </select> &nbsp;&nbsp;&nbsp;&nbsp; <?php echo '<span style="color: red;">'.JText::_ ('COM_VIRTUEMART_CHECK_ORDER_COD').'</span>' ?>
        
        
        </div>
    </div>
    <table class="adminlist" cellspacing="10" cellpadding="10" style="margin-left: 10px; margin-top: 10px;">
        <thead>
        <tr>
  
            <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count ($this->parcelslist); ?>')"/></th>
            <td width="30"><b><?php echo $this->sort ('id', 'ID')  ?></b></td>
            <td width="150"><b><?php echo $this->sort ('virtuemart_order_id', 'COM_VIRTUEMART_FOXPOST_VIEW_ORDER_ID')  ?></b></td>
            <td width="100"><b><?php echo $this->sort ('parcel_target_machine_id', 'COM_VIRTUEMART_FOXPOST_VIEW_MACHINE_ID')  ?></b></td>
            <td width="100"><b><?php echo $this->sort ('sticker_creation_date', 'COM_VIRTUEMART_FOXPOST_VIEW_STICKER_CREATION_DATE')  ?></b></td>
            <td width="100"><b><?php echo $this->sort ('created_on', 'COM_VIRTUEMART_FOXPOST_VIEW_CREATION_DATE')  ?></b></td>
            <td width="100"><b><?php echo $this->sort ('payment', 'COM_VIRTUEMART_VIEW_PAYMENT')  ?></b></td>
            <td width="400"><b><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_ACTIONS')  ?></b></td>
        </tr>
        </thead>
        <tbody>
        <?php
               
        if (count ($this->parcelslist) > 0) {
 
            $i = 0;
            $k = 0;
            $keyword = JRequest::getWord ('keyword');

            foreach ($this->parcelslist as $key => $parcel) {
     
                     
                 $db3 = JFactory::getDBO();
                 $q3 = "SELECT virtuemart_paymentmethod_id FROM #__virtuemart_orders WHERE virtuemart_order_id='".$parcel->virtuemart_order_id."'";
                 $db3->setQuery($q3);
                 $currPay=$db3->loadObject();
              
            if (($currPay->virtuemart_paymentmethod_id == $_GET['paym']) OR (!is_numeric($_GET['paym'])) OR ($_GET['paym']==0)) {  
                
                  $db2 = JFactory::getDBO();
                 $q2 = "SELECT parcel_detail, variables FROM #__virtuemart_shipment_plg_foxpost WHERE virtuemart_order_id='".$parcel->virtuemart_order_id."'";
                 $db2->setQuery($q2);
                 $currOrder=$db2->loadObject();
                 $need_up=$currOrder->parcel_detail;
                 $variables=$currOrder->variables;
                 $needs_check=json_decode($currOrder->parcel_detail);   
                
                 $checked = JHTML::_ ('grid.id', $i, $parcel->virtuemart_order_id);
                ?>
            <tr class="row<?php echo $k; ?>">
       
                <!-- Checkbox -->
                <?php if ($variables==0) { ?>
                <td><?php echo $checked; ?></td>
                <?php
                } else {
                 echo '<td></td>';
                }
                ?>
                <td align="center"><?php echo $parcel->id; ?>
                <!-- Order id -->
                <?php
                $link_order = 'index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=' . $parcel->virtuemart_order_id;
                ?>
                <td width="30"><?php echo JHTML::_ ('link', JRoute::_ ($link_order), $parcel->order_number, array('title' => JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_EDIT_ORDER') . ' ' . $parcel->order_number)); ?></td>
                <!-- Parcel id -->
                <?php
                $link_parcel = 'index.php?option=com_virtuemart&view=foxpost&task=edit&id=' . $parcel->id;
                 $currentparcel=json_decode($parcel->parcel_target_machine_detail);
                 //print_r ($parcel);
                ?>
                
                <td width="200">
                 <?php  
                    $terminal=$currentparcel->name.'<br>'.$currentparcel->address;
                    echo $terminal; ?>
                    <?php if ($needs_check->glob_barcode==2) { ?>   
                    <?php
                      $ch = curl_init($Authurl[1]."/orders/".$needs_check->glob_barcode);
                      curl_setopt($ch, CURLOPT_HEADER, 0);
                      curl_setopt($ch, CURLOPT_HEADER,"Accept:application/vnd.cleveron+json; version=1.0");
                      curl_setopt($ch, CURLOPT_HEADER,"Content-Type:application/vnd.cleveron+xml");
                      curl_setopt($ch, CURLOPT_USERPWD, $Auth);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                      $res =curl_exec($ch);  
                      $res=json_decode($res);
                      
                      $ts=str_replace ("T", " ", $res->timestamp);
                      $ts=str_replace ("Z", " ", $ts);
                     ?>
                    
                    <td width="200"><?php echo $ts ?></td>
                  <?php } else { ?>
                     <td width="200">0000-00-00 00:00:00</td>
                  <?php } ?>
                <td width="200"><?php echo $parcel->created_on; ?>
                <td width="200">
                   <?php
                    $paymentid = JFactory::getDBO();
                    $p1 = "SELECT virtuemart_paymentmethod_id FROM #__virtuemart_orders WHERE order_number='".$parcel->order_number."'";
                    $paymentid->setQuery($p1);
                    $currentpay= $paymentid->loadObject();
                    
		    $model = VmModel::getModel('paymentmethod');
                    
		    $payments = $model->getPayments();
                    foreach($payments as $payment) {
                        
                        if($payment->virtuemart_paymentmethod_id == $currentpay->virtuemart_paymentmethod_id) echo $payment->payment_name;
                    }
		   ?>

                </td>    
                <!-- Actions -->
                <?php
                if($parcel->parcel_id == '0'){
                    $link_name = JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_CREATE_PARCEL');
                }else{
                    $link_name = JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_EDIT_PARCEL');
                }
  
                ?>
             
                <td>   
                 
                      <?php
                       $currentDetail=json_decode($parcel->parcel_detail);
                       //print_r ($currentDetail);
                           // print_r ($parcel);
                       $timestamps=date('Y-m-d H:i:s');
                       
                        $db1 = JFactory::getDBO();
                        $q1 = "SELECT title, last_name, first_name, middle_name FROM #__virtuemart_userinfos WHERE virtuemart_user_id='".$parcel->modified_by."'";
                        $db1->setQuery($q1);
                        $currentUserInfo= $db1->loadObject();
                
                        $currUser=$currentUserInfo->title.' '.$currentUserInfo->first_name.' '.$currentUserInfo->middle_name.' '.$currentUserInfo->last_name;
                      
                        //print $currUser;
                        $trace_url=$Authurl[1]."trace/".$parcel->order_number;
                       
                        $jsonOrderCreateTomb = array (
      
                          "place_id"=>$currentparcel->id,
                          "name" => $currUser, 
                          "phone" => $currentDetail->receiver->phone, 
                          "email" => $currentDetail->receiver->email,
        
                       
                 
                         );                       
                         $jsonOrderCreateTomb=json_encode($jsonOrderCreateTomb);
         
     
                         ?>
                      
                      <input type="hidden"  name="send_data<?php echo $parcel->id; ?>" value="1">
                      <input type="hidden" name="barcodeToPost_<?php echo $parcel->id; ?>" value="<?php echo $parcel->virtuemart_order_id; ?>">
                      <textarea style="display:none;"  name="send_datas_<?php echo $parcel->id; ?>"><?php echo $jsonOrderCreateTomb; ?></textarea>  
                      <?php if ($variables==0) {  $currFrom='post_data_this_'.$parcel->id; } else {?>
                      </form>
                      <form id="<?php echo $currFrom; ?>" method="POST" >
                      <input type="hidden"  name="send_data_this" value="1">    
                      <input type="hidden" name="barcodeToPostThis" value="<?php echo $parcel->virtuemart_order_id; ?>">
                      <textarea style="display:none;"  name="send_datas_this"><?php echo $jsonOrderCreateTomb; ?></textarea>      
                      <button onclick="document.getElementById('<?php echo $currFrom; ?>').submit();"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_ACTIVATE_ORDER')  ?></button>
                      </form>
                      <?php } ?>
            
                </td>
            </tr>
                <?php
                $k = 1 - $k;
                $i++;
                
            }
          }
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="11">
                <?php echo $this->pagination->getListFooter (); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <!-- Hidden Fields -->
    <?php echo $this->addStandardHiddenToForm (); ?>

    <?php AdminUIHelper::endAdminArea (); ?>
<script type="text/javascript">
    <!--

        jQuery('.show_comment').click(function() {
        jQuery(this).prev('.element-hidden').show();
        return false
        });

        jQuery('.element-hidden').mouseleave(function() {
        jQuery(this).hide();
        });
        jQuery('.element-hidden').mouseout(function() {
        jQuery(this).hide();
        });
        
                
        
        
        jQuery('#checkAll').change(function() {
            var checkboxes = jQuery(this).closest('form').find(':checkbox');
                if(jQuery(this).is(':checked')) {
                    checkboxes.attr('checked', 'checked');
                } else {
                    checkboxes.removeAttr('checked');
                }
            });
        -->
</script>
