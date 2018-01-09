<?php
##############################
#Copyright 2015 Foxpost Zrt  #   
#2015.02.05 ToHR             #
#Foxpost Parcels Widget Core #
#Order TMPL                  #
##############################
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea($this);

?>
<script type="text/javascript">
    function user_function(value) {

        var address = value.split(';');
        var openIndex = address[4];
        var sufix = '';

        if(openIndex == 'source_machine') {
            sufix = '_source';
        }

        //document.getElementById('town').value=address[1];
        //document.getElementById('street').value=address[2]+address[3];
        var box_machine_name = document.getElementById('name').value;
        var box_machine_town = document.value=address[1];
        var box_machine_street = document.value=address[2];


        var is_value = 0;
        document.getElementById('shipping_foxpost'+sufix).value = box_machine_name;
        var shipping_foxpost = document.getElementById('shipping_foxpost'+sufix);

        for(i=0;i<shipping_foxpost.length;i++){
            if(shipping_foxpost.options[i].value == document.getElementById('name').value){
                shipping_foxpost.selectedIndex = i;
                is_value = 1;
            }
        }

        if (is_value == 0){
            shipping_foxpost.options[shipping_foxpost.options.length] = new Option(box_machine_name+','+box_machine_town+','+box_machine_street, box_machine_name);
            shipping_foxpost.selectedIndex = shipping_foxpost.length-1;
        }
    }
</script>
<br><br>

<form name='adminForm' id="adminForm" method="POST">
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="foxpost" />
    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="parcel_id" value="<?php echo $this->foxpostData['parcel_id']; ?>" />
    <input type="hidden" name="id" value="<?php echo $this->foxpostData['id']; ?>" />




<table class="adminlist" style="table-layout: fixed;">


    <?php if(in_array($this->parcel->api_source, array('HU'))){ ?>
    <tr>
        <td width="150" align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_COD_AMOUNT'); ?>:</td>
        <td><input name="parcel_cod_amount" value="<?php echo $this->foxpostData['parcel_cod_amount']; ?>" <?php echo $this->disabledCodAmount; ?> <?php ?>/></td>
    </tr>
    <?php } ?>

    <tr>
        <td width="150" align="right" valign="top"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_DESCRIPTION'); ?>:</td>
        <td><textarea name="parcel_description" rows="10" cols="10" <?php echo $this->disabledDescription; ?>><?php echo $this->foxpostData['parcel_description']; ?></textarea></td>
    </tr>

    <?php if(in_array($this->parcel->api_source, array('HU'))) { ?>
    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_INSURANCE_AMOUNT'); ?>:</td>
        <td>
            <select id="parcel_size" name="parcel_insurance_amount" <?php echo $this->disabledInsuranceAmount; ?>>
                <option value='' <?php if(@$this->foxpostData['parcel_insurance_amount'] == ''){ echo "selected=selected";} ?>><?php echo $this->defaultInsuranceAmount; ?></option>
                <?php foreach($this->parcelInsurancesAmount as $key => $parcelInsuranceAmount){ ?>
                <option value='<?php echo $key; ?>' <?php if($this->foxpostData['parcel_insurance_amount'] == $key){ echo "selected=selected";} ?>><?php echo $parcelInsuranceAmount;?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <?php } ?>

    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_RECEIVER_PHONE'); ?>:</td>
        <td><input name="parcel_receiver_phone" value="<?php echo $this->foxpostData['parcel_receiver_phone']; ?>" <?php echo $this->disabledReceiverPhone; ?>/></td>
    </tr>

    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_RECEIVER_EMAIL'); ?>:</td>
        <td><input name="parcel_receiver_email" value="<?php echo $this->foxpostData['parcel_receiver_email']; ?>" <?php echo $this->disabledReceiverEmail; ?>/></td>
    </tr>

    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE'); ?>:</td>
        <td>
            <select id="parcel_size" name="parcel_size" <?php echo $this->disabledParcelSize; ?>>
                <option value='' <?php if($this->foxpostData['parcel_size'] == ''){ echo "selected=selected";} ?>><?php echo $this->defaultParcelSize;?></option>
                <option value='<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_A'); ?>' <?php if($this->foxpostData['parcel_size'] == JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_A')){ echo "selected=selected";} ?>><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_A'); ?></option>
                <option value='<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_B'); ?>' <?php if($this->foxpostData['parcel_size'] == JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_B')){ echo "selected=selected";} ?>><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_B'); ?></option>
                <option value='<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_C'); ?>' <?php if($this->foxpostData['parcel_size'] == JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_C')){ echo "selected=selected";} ?>><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_C'); ?></option>
                <option value='<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_D'); ?>' <?php if($this->foxpostData['parcel_size'] == JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_D')){ echo "selected=selected";} ?>><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_D'); ?></option>
                <option value='<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_E'); ?>' <?php if($this->foxpostData['parcel_size'] == JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_E')){ echo "selected=selected";} ?>><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SIZE_E'); ?></option>
            </select>
        </td>
    </tr>
<!--  -->


    <?php if(@$this->foxpostData['parcel_status'] != '0'){ ?>
    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_STATUS'); ?>:</td>
        <td><input name="parcel_status" value="<?php echo $this->foxpostData['parcel_status']; ?>" <?php echo $this->disabledParcelStatus; ?>/></td>
    </tr>
    <?php } ?>

    <?php if(in_array($this->parcel->api_source, array('HU'))) { ?>
    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SOURCE_MACHINE'); ?>:</td>
        <td>

            <select id="shipping_foxpost_source" name="parcel_source_machine_id" <?php echo $this->disabledSourceMachine; ?>>
                <option value='' <?php if(@$this->foxpostData['parcel_source_machine_id'] == ''){ echo "selected=selected";} ?>><?php echo $this->defaultSourceMachine;?></option>
                <?php foreach($this->parcelSourceMachinesId as $key => $parcelSourceMachine){ ?>
              
                <option value='<?php echo $key ?>' <?php if($this->foxpostData['parcel_source_machine_id'] == $key){ echo "selected=selected";} ?>><?php echo $parcelSourceMachine;?></option>
                <?php } ?>
            </select>
            <input type="hidden" id="name_source" name="name_source" disabled="disabled" />
            <input type="hidden" id="box_machine_town_source" name="box_machine_town_source" disabled="disabled" />
            <input type="hidden" id="address_source" name="address_source" disabled="disabled" />
            <a href="#" onclick="openMap('source_machine'); return false;"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_MAP'); ?></a>
            &nbsp|&nbsp<input type="checkbox" name="show_all_machines_source" <?php echo $this->disabledSourceMachine; ?>> <?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SHOW_TERMINAL'); ?>
        
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_TMP_ID'); ?>:</td>
        <td><input name="parcel_tmp_id" value="<?php echo $this->foxpostData['parcel_tmp_id']; ?>" <?php echo $this->disabledTmpId; ?>/></td>
    </tr>

    <tr>
        <td align="right"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_TARGET_MACHINE'); ?>:</td>
        <td>
            <select id="shipping_foxpost" name="parcel_target_machine_id" <?php echo $this->disabledTargetMachine; ?>>
                <option value='' <?php if(@$this->foxpostData['parcel_target_machine_id'] == ''){ echo "selected=selected";} ?>><?php echo $this->defaultTargetMachine;?></option>
                <?php foreach($this->parcelTargetMachinesId as $key => $parcelTargetMachine) { ?>
                <option value='<?php echo $key; ?>' <?php if($this->foxpostData['parcel_target_machine_id'] == $key){ echo "selected=selected";} ?>><?php echo $parcelTargetMachine;?></option>
                <?php } ?>
            </select>
            <input type="hidden" id="name" name="name" disabled="disabled" />
            <input type="hidden" id="box_machine_town" name="box_machine_town" disabled="disabled" />
            <input type="hidden" id="address" name="address" disabled="disabled" />
            <a href="#" onclick="openMap('target_machine'); return false;"><?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_MAP'); ?></a>
            &nbsp|&nbsp<input type="checkbox" name="show_all_machines" <?php echo $this->disabledTargetMachine; ?>> <?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SHOW_TERMINAL'); ?>
        </td>
    </tr>

</table>

<!-- -->

</form>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); 
?>


<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('input[type="checkbox"][name="show_all_machines"]').click(function(){
            var machines_list_type = jQuery(this).is(':checked');

            if(machines_list_type == true){
                //alert('all machines');
                var machines = {
                    '' : '<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SELECT_MACHINE..');?>',
                <?php foreach($this->parcelTargetAllMachinesId as $key => $parcelTargetAllMachineId){ ?>
                    '<?php echo $key; ?>' : '<?php echo addslashes($parcelTargetAllMachineId) ?>',
                <?php } ?>
                };
            }else{
                //alert('criteria machines');
                var machines = {
                    '' : '<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SELECT_MACHINE..');?>',
                <?php foreach($this->parcelTargetMachinesId as $key => $parcelTargetMachineId) { ?>
                    '<?php echo $key; ?>' : '<?php echo addslashes($parcelTargetMachineId) ?>',
                 <?php } ?>
                };
            }

            jQuery('#shipping_foxpost option').remove();
            jQuery.each(machines, function(val, text) {
                jQuery('#shipping_foxpost').append(
                        jQuery('<option></option>').val(val).html(text)
                );
            });
        });
        
        jQuery('input[type="checkbox"][name="show_all_machines_source"]').click(function(){
            var machines_list_type = jQuery(this).is(':checked');

            if(machines_list_type == true){
                //alert('all machines');
                var machines = {
                    '' : '<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SELECT_MACHINE..');?>',
                <?php foreach($this->parcelSourceAllMachinesId as $key => $parcelSourceAllMachineId): ?>
                    '<?php echo $key; ?>' : '<?php echo addslashes($parcelSourceAllMachineId) ?>',
                    <?php endforeach; ?>
                };
            }else{
                //alert('criteria machines');
                var machines = {
                    '' : '<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SELECT_MACHINE..');?>',
                <?php foreach($this->parcelSourceMachinesId as $key => $parcelSourceMachineId): ?>
                    '<?php echo $key; ?>' : '<?php echo addslashes($parcelSourceMachineId); ?>',
                    <?php endforeach; ?>
                };
            }

            jQuery('#shipping_foxpost_source option').remove();
            jQuery.each(machines, function(val, text) {
                jQuery('#shipping_foxpost_source').append(
                        jQuery('<option></option>').val(val).html(text)
                );
            });
        });
    });
</script>


