<?php
##############################
#Copyright 2015 Foxpost Zrt  #   
#2015.02.05 ToHR             #
#Foxpost Parcels Widget Core #
#Orders Missing product det. #
##############################
defined ('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea($this);

    
// create table for missing values

  ?>  
<table cellspacig="10" cellpadding="10" border="0" width="600" style="margin-left: 10px;">
    <tr>
       <td><?php echo JTEXT::_ ('ID') ?></td>
       <td><?php echo JTEXT::_ ('COM_FOXPOST_PRODUCT_NAME') ?></td>
       <td><?php echo JTEXT::_ ('COM_FOXPOST_PRODUCT_MISSING_PARAMETER') ?></td>

    </tr>
    <?php
        $product_list = JFactory::getDBO();
        $p_list = "SELECT virtuemart_product_id, product_weight, product_length, product_height, product_width FROM #__virtuemart_products WHERE product_weight = '' OR product_width = '' OR product_length = '' OR product_height = ''";
        $product_list->setQuery($p_list);
        $rows1 = $product_list->loadObjectList(); 
        
        foreach( $rows1 as $row1 ) {
            
            $db2 = JFactory::getDBO();
                 $q2 = "SELECT product_name FROM #__virtuemart_products_en_gb WHERE virtuemart_product_id='".$row1->virtuemart_product_id."'";
                 $db2->setQuery($q2);
                 $currName=$db2->loadObject();
            
        
          echo '<tr>';
          echo '<td>'.$row1->virtuemart_product_id.'</td>';
          echo '<td> <a href="index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$row1->virtuemart_product_id.'">'.$currName->product_name.'</td>';
          $missing='';
          if ($row1->product_weight == 0) {
     
              $missing.=JText::_ ("COM_FOXPOST_MISSING_WEIGHT");
              $missing.=JText::_ (", ");   
       
          }
          
          if ($row1->product_length == 0) {
              $missing.=JText::_ ("COM_FOXPOST_MISSING_LENGHT");
              $missing.=JText::_ (", "); 
          }
          
          if ($row1->product_height == 0) {
              $missing.=JText::_ ("COM_FOXPOST_MISSING_HEIGHT");
              $missing.=JText::_ (", "); 
          }
          
          if ($row1->product_width == 0) {
              $missing.=JText::_ ("COM_FOXPOST_MISSING_WIDTH");
          }
          ?>  
          <td><?php echo $missing; ?></td>
         <?php   echo '</tr>';
          
        } 
    
    ?>


    














<?php
// we done
?>
</table>