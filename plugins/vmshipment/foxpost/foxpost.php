<?php
##############################
#Copyright 2015 Foxpost Zrt  #
#2015.02.05 ToHR             #
#Foxpost Parcels Widget Core #
##############################

defined ('_JEXEC') or die('Restricted access');


// Define languages
        $jlang = JFactory::getLanguage();
        $jlang->load('com_vmshipment_foxpost', JPATH_PLUGINS.'/vmshipment/foxpost', 'hu-HU', true);
        $jlang->load('com_vmshipment_foxpost', JPATH_PLUGINS.'/vmshipment/foxpost', $jlang->getDefault(), true);
        $jlang->load('com_vmshipment_foxpost', JPATH_PLUGINS.'/vmshipment/foxpost', null, true);


if (!class_exists ('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');


}


require_once (JPATH_ROOT .'/plugins/vmshipment/foxpost' . DS . 'foxpost' . DS . 'helpers' . DS . 'foxpost_functions.php');

//foxpost_functions::createFlags();

class plgVmShipmentFoxpost extends vmPSPlugin {

    public static $_this = FALSE;


    /**
* @param object $subject
* @param array $config
*/
    function __construct (& $subject, $config) {

            parent::__construct ($subject, $config);
                $this->_loggable = TRUE;
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		$this->tableFields = array_keys ($this->getTableSQLFields ());
		$varsToPush = $this->getVarsToPush ();
		$this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);

    }

    public function getVmPluginCreateTableSQL () {
        return $this->createTableSQL ('Shipment Weight Countries Table');
    }


 // Create table for plugin
    function getTableSQLFields () {
            $SQLfields = array(
                'id' => 'int(1) UNSIGNED NOT NULL AUTO_INCREMENT',
                'virtuemart_order_id' => 'int(11) UNSIGNED',
                'parcel_id' => 'varchar(200)',
                'parcel_status' => 'varchar(200)',
                'parcel_detail' => 'text',
                'parcel_target_machine_id' => 'varchar(200)',
                'parcel_target_machine_detail' => 'text',
                'sticker_creation_date' => 'timestamp',
                'api_source' => 'varchar(3)',
                'variables' => 'text',
                    'order_number' => 'char(32)',
                    'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
                    'shipment_name' => 'varchar(5000)',
                    'order_weight' => 'decimal(10,4)',
                    'shipment_weight_unit' => 'char(3) DEFAULT \'KG\'',
                    'shipment_cost' => 'decimal(10,2)',
                    'shipment_package_fee' => 'decimal(10,2)',
                    'tax_id' => 'smallint(1)'
                );
    return $SQLfields;
    }

	public function plgVmOnShowOrderFEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id, &$shipment_name) {


		$this->onShowOrderFE ($virtuemart_order_id, $virtuemart_shipmentmethod_id, $shipment_name);
	}
// Calculate Prices
        function getCosts (VirtueMartCart $cart, $method, $cart_prices) {


            //print_r ($cart_prices);
		if ($method->shipment_cost_free_limit <  $cart_prices['basePriceWithTax']) {
                    //print 'asd';
                    return 0;

		} else {
			return (int)$method->shipment_cost + (int)$method->package_fee;
		}
	}
// Processing confirmed order
        function plgVmConfirmedOrder (VirtueMartCart $cart, $order) {
            self::plgVmOnSelectCheckShipment($cart);
		if (!($method = $this->getVmPluginMethod ($order['details']['BT']->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->shipment_element)) {
			return FALSE;
		}

        $order_id = $order['details']['BT']->virtuemart_order_id;

        $parcel_detail = array(
            'description' => JText::_ ('COM_VIRTUEMART_FOXPOST_ORDER').$order_id,
            'receiver' => array(
                'email' => $_SESSION['foxpost']['user_email'],
                'phone' => $_SESSION['foxpost']['shipping_foxpost']['receiver_phone'],
            ),
            'size' => @$_SESSION['foxpost']['parcel_size'],
            'tmp_id' => foxpost_functions::generate(4, 15),
            'target_machine' => $_SESSION['foxpost']['shipping_foxpost']['parcel_target_machine_id']
        );

        switch (foxpost_functions::getCurrentApi()){

            case 'HU':
                $parcel_detail['cod_amount'] = ($order['details']['BT']->virtuemart_paymentmethod_id == 1)? sprintf("%.2f" ,$order['details']['BT']->order_total) : '';
            break;


        }

        $parcel_target_machine_id = $_SESSION['foxpost']['shipping_foxpost']['parcel_target_machine_id'];
        $parcel_target_machine_detail = @$_SESSION['foxpost']['parcelTargetAllMachinesDetail'][$parcel_target_machine_id];
        $values['virtuemart_order_id'] = $order_id;
        $values['parcel_detail'] = json_encode($parcel_detail);
        $values['parcel_target_machine_id'] = $parcel_target_machine_id;
        $values['parcel_target_machine_detail'] = json_encode($parcel_target_machine_detail);
        $values['api_source'] = foxpost_functions::getCurrentApi();
        $values['order_number'] = $order['details']['BT']->order_number;
        $values['virtuemart_shipmentmethod_id'] = $order['details']['BT']->virtuemart_shipmentmethod_id;
        $values['shipment_name'] = $this->renderPluginName ($method);
        $values['order_weight'] = $this->getOrderWeight ($cart, $method->weight_unit);
        $values['shipment_weight_unit'] = $method->weight_unit;
        $values['shipment_cost'] = $method->cost;
        $values['shipment_package_fee'] = $method->package_fee;
        $values['tax_id'] = $method->tax_id;
        $this->storePSPluginInternalData ($values);

        unset($_SESSION['foxpost']);
        unset ($_SESSION['fox']);
        //unset($cart->ST);
        return TRUE;
	}

	public function plgVmOnShowOrderBEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id) {


            if (!($this->selectedThisByMethodId ($virtuemart_shipmentmethod_id))) {
			return NULL;
		}
		$html = $this->getOrderShipmentHtml ($virtuemart_order_id);

		return $html;
	}

	/**
	 * @param $virtuemart_order_id
	 * @return string
	 */
	function getOrderShipmentHtml ($virtuemart_order_id) {

		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `' . $this->_tablename . '` '
			. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
		$db->setQuery ($q);
		if (!($shipinfo = $db->loadObject ())) {
			vmWarn (500, $q . " " . $db->getErrorMsg ());
			return '';
		}

		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}

                $parcelDetail = json_decode($shipinfo->parcel_detail);

		$currency = CurrencyDisplay::getInstance ();
		/*$tax = ShopFunctions::getTaxByID ($shipinfo->tax_id);
		$taxDisplay = is_array ($tax) ? $tax['calc_value'] . ' ' . $tax['calc_value_mathop'] : $shipinfo->tax_id;
		$taxDisplay = ($taxDisplay == -1) ? JText::_ ('COM_VIRTUEMART_PRODUCT_TAX_NONE') : $taxDisplay;
		*/
                $html = '<table class="adminlist">' . "\n";
		$html .= $this->getHtmlHeaderBE ();
		$html .= $this->getHtmlRowBE ('COM_VIRTUEMART_FOXPOST_SHIPPING_NAME', "$shipinfo->shipment_name");
                $html .= $this->getHtmlRowBE ('COM_VIRTUEMART_FOXPOST_MACHINE', $shipinfo->parcel_target_machine_id);
                $html .= $this->getHtmlRowBE ('COM_VIRTUEMART_FOXPOST_MOBILE', $parcelDetail->receiver->phone);
                $html .= $this->getHtmlRowBE ('COM_VIRTUEMART_FOXPOST_WEIGHT', $shipinfo->order_weight . ' ' . ShopFunctions::renderWeightUnit ($shipinfo->shipment_weight_unit));
		$html .= $this->getHtmlRowBE ('COM_VIRTUEMART_FOXPOST_COST', $currency->priceDisplay ($shipinfo->shipment_cost));
		$html .= $this->getHtmlRowBE ('COM_VIRTUEMART_FOXPOST_PACKAGE_FEE', $currency->priceDisplay ($shipinfo->shipment_package_fee));
		//$html .= $this->getHtmlRowBE ('COM_VIRTUEMART_FOXPOST_TAX', $taxDisplay);
		$html .= '</table>' . "\n";

		return $html;
	}


        public function displayListFE (VirtueMartCart $cart, $selected = 0, &$htmlIn) {
        self::plgVmOnSelectCheckShipment($cart);
        if ($this->getPluginMethods ($cart->vendorId) === 0) {
               if (empty($this->_name)) {

                vmAdminInfo ('displayListFE cartVendorId=' . $cart->vendorId);
                $app = JFactory::getApplication ();
                $app->enqueueMessage (JText::_ ('COM_VIRTUEMART_CART_NO_' . strtoupper ($this->_psType)));
                return FALSE;
            } else {
               return FALSE;
            }
        }

        $html = array();
        $method_name = $this->_psType . '_name';

        foreach ($this->methods as $method) {

          if ($method->CheckData == 1) {
            if ($this->checkConditions ($cart, $method, $cart->pricesUnformatted) === 1) {

               $hibastermek=2;
            } else {

               $hibastermek=0;
            }
          } else {
              //print $this->checkConditions ($cart, $method, $cart->pricesUnformatted);

              if ($this->checkConditions ($cart, $method, $cart->pricesUnformatted) == 1) {
                  $hibastermek=0;
              } else {
                  $hibastermek=$this->checkConditions ($cart, $method, $cart->pricesUnformatted);
              }
          }
                $methodSalesPrice = $this->setCartPrices($cart, $cart->pricesUnformatted,$method);

                $method->$method_name = $this->renderPluginName ($method);

                    $html [] = $this->getPluginHtmlFoxpost($cart, $method, $selected, $methodSalesPrice, $hibastermek);

        }

        if (!empty($html)) {
            $htmlIn[] = $html;
            return TRUE;
        }

        return true;
    }

    public function onSelectedCalculatePrice (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {

        $id = $this->_idName;
        if (!($method = $this->selectedThisByMethodId ($cart->$id))) {
            return NULL; // Another method was selected, do nothing
        }

        if (!($method = $this->getVmPluginMethod ($cart->$id))) {
            return NULL;
        }

        $cart_prices_name = '';
        //$cart_prices[$this->_psType . '_tax_id'] = 0;
        $cart_prices['cost'] = $method->shipment_cost;
        $method->cost = $method->shipment_cost;

        if (!$this->checkConditions ($cart, $method, $cart_prices)) {
            return FALSE;

        }
        $paramsName = $this->_psType . '_params';
        $cart_prices_name = $this->renderPluginName ($method);

        $this->setCartPrices($cart, $cart_prices, $method);

        return TRUE;
    }






    protected function getPluginHtmlFoxpost($cart, $plugin, $selectedPlugin, $pluginSalesPrice, $hibastermek) {


        $pluginmethod_id = $this->_idName;
        //print $this->virtuemart_shipmentmethod_id;
        $pluginName = $this->_psType . '_name';
        $currbox="shipment_id_".$plugin->$pluginmethod_id;
        //print $pluginmethod_id;



        if ($hibastermek==0) {

             if ($selectedPlugin == $plugin->$pluginmethod_id) {
                $checked = 'checked="checked"';
                } else {
                $checked = '';
              }

            $disabled='';
        } else {
            $checked='';
            ?>
            <script type="text/javascript">
                jQuery('#<?php print $currbox; ?>').prop('checked', false);
            </script>
            <?php

            $disabled='disabled="disabled"';
        }

        if (!class_exists ('CurrencyDisplay')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
        }
        $currency = CurrencyDisplay::getInstance ();
        $costDisplay = "";

        if ($pluginSalesPrice) {
               $costDisplay = $currency->priceDisplay ($pluginSalesPrice);
               $costDisplay = '<span class="' . $this->_type . '_cost"> (' . JText::_ ('COM_VIRTUEMART_PLUGIN_COST_DISPLAY') . $costDisplay . ")</span>";
         }


            if (sizeof($cart->products) > 0){

                $content = $this->renderByLayout('edit_shippment',$this->prepareData($cart, $this->methods[0], $this->_psType . '_id_' . $plugin->$pluginmethod_id, $plugin->$pluginmethod_id), 'foxpost', 'vmshipment');

                $html = '<input type="radio" class="foxpost_shippment_plugin" name="' . $pluginmethod_id . '" value="' . $plugin->$pluginmethod_id . '" id="'. $this->_psType.'_id_'. $plugin->$pluginmethod_id.'"  ' . $checked . " ". $disabled .">\n"
                 . '<label for="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '">' . '<span class="' . $this->_type . '">' . $plugin->$pluginName .' '.$costDisplay." </span></label>
                 $content
                 \n";


            } else {
                $html="";
            }

        return $html;
    }



    protected function prepareData($cart, $method, $method_id, $radio_id) {

        $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

        $foxpost = array();
        $foxpost['radio_id'] = $radio_id;
        $foxpost['address'] = $address;
        $foxpost['user_email'] = $cart->BT['email'];
        $foxpost['user_phone'] = $address['phone_2'];
        if(!preg_match('/^[1-9]{1}\d{8}$/', $foxpost['user_phone'])){
            $foxpost['user_phone'] = null;
        }


        $machine_params = array();
        switch(foxpost_functions::getCurrentApi()){
            case 'HU':
                $machine_params['payment_available'] = true;
                break;
            case 'UK':
                break;
        }

        $allMachines = foxpost_functions::connect_foxpost_terminals(
            array(
                'url' => $method->api_url.'',
                'token' => $method->api_username.':'.$method->api_password,
                'methodType' => 'GET',
                'params' => $machine_params
            )
        );



        $parcelTargetAllMachinesId = array();
        $parcelTargetAllMachinesDetail = array();
        $machines = array();

        if(is_array(@$allMachines['result']) && !empty($allMachines['result'])){
            $k=0;
            foreach($allMachines['result'] as $key => $machine){

                $parcelTargetAllMachinesId[$machine->place_id] = addslashes(@$machine->name.', '.@$machine->address);
                $parcelTargetAllMachinesDetail[$machine->place_id] = array(
                    'id' =>$machine->place_id,
                    'name' => @$machine->name,
                    'address' => @$machine->address,
                    'city' => @$machine->group,
                    'geolat' => @$machine->geolat,
                    'geolng' => @$machine->geolng,
                    'open' => @$machine->open
                );

               if($machine->group == $address['city']){

                    $machines[$key] = $machine;
               }

			   
			   asort($parcelTargetAllMachinesId);

                $foxpost['parcelTargetAllMachinesId'] = $parcelTargetAllMachinesId;
                $foxpost['parcelTargetAllMachinesDetail'] = $parcelTargetAllMachinesDetail;



             $k++;
             }

        }


        $parcelTargetMachinesId = array();
        $parcelTargetMachinesDetail = array();
        $foxpost['defaultSelect'] = JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_SELECT_MACHINE');
        if(is_array($machines) && !empty($machines)){

                $k=0;
            foreach($machines as $key => $machine){

                $parcelTargetMachinesId[$machine->place_id] = addslashes(@$machine->name.', '.@$machine->address);
                $parcelTargetMachinesDetail[$machine->place_id] = array(
                    'id' => $machine->place_id,
                    'name' => @$machine->name,
                    'address' => @$machine->address,
                    'city' => @$machine->group,
                    'geolat' => @$machine->geolat,
                    'geolng' => @$machine->geolng,
                    'open' => @$machine->open


                );

                $k++;
            }

            $foxpost['parcelTargetMachinesId'] = $parcelTargetMachinesId;
        }else{
            $foxpost['defaultSelect'] = JText::_ ('COM_VIRTUEMART_FOXPOST_DEFAULT_SELECT');
        }
        $foxpost['parcelTargetMachinesId'] = $parcelTargetMachinesId;


        $_SESSION['foxpost'] = $foxpost;
        return array('foxpost' => $foxpost);


    }

    protected function checkConditions ($cart, $method, $cart_prices) {

         // check countries
        $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
        if(!in_array($address['virtuemart_country_id'], $method->allowed_country)){

            $hibastermek=2;
            return $hibastermek;

        }

        if ($method->CheckData == 1) {
            if ($this->getOrderWeight($cart, $method->weight_unit) > $method->max_weight) {

            $hibastermek=2;
            return $hibastermek;
            }
        }

        if ($method->CheckData == 1) {
            foreach ($cart->products as $product) {
                $product_dimensions[] = (float)$product->product_width.'x'.(float)$product->product_height.'x'.(float)$product->product_length;
            }

            $calculateDimension = foxpost_functions::calculateDimensions(@$product_dimensions,
                 array(
                    'MAX_DIMENSION_A' => $method->max_dimension_a,
                    'MAX_DIMENSION_B' => $method->max_dimension_b,
                    'MAX_DIMENSION_C' => $method->max_dimension_c,
                    'MAX_DIMENSION_D' => $method->max_dimension_d,
                    'MAX_DIMENSION_E' => $method->max_dimension_e
                 )
            );

            if(!$calculateDimension['isDimension']){


            $hibastermek=2;
            return $hibastermek;
            }

            $_SESSION['foxpost']['parcel_size'] = $calculateDimension['parcelSize'];
        }

       if ($method->CheckData == 1) {

        if ($hibastermek == 2) {
            return $hibastermek;
        } else {
            return true;
        }
       } else {

           return true;
       }
    }



    function plgVmOnStoreInstallShipmentPluginTable ($jplugin_id) {



        $db = JFactory::getDBO ();
        $db->setQuery ("SELECT count(id) as count FROM #__virtuemart_adminmenuentries WHERE name='FOXPOST'");

        if($db->loadResult() <= 0){
            $query = "INSERT INTO #__virtuemart_adminmenuentries (id, module_id, parent_id, name, link, depends, icon_class, ordering, published, tooltip, view, task) VALUES (null, 2, 0, 'FOXPOST', '', '', 'vmicon vmicon-16-page_white_stack', 1, 1, '', 'foxpost', '')";
            $db->setQuery ($query);
            $db->query();
        }

	    return $this->onStoreInstallPluginTable ($jplugin_id);;
    }

	/**
	 * @param VirtueMartCart $cart
	 * @return null
	 */
	public function plgVmOnSelectCheckShipment (VirtueMartCart &$cart) {

        $id = $this->_idName;

       if(@$_POST['shipping_foxpost']['parcel_target_machine_id'] == ''){
        if (($method = $this->selectedThisByMethodId($cart->$id))) {

            return NULL; // Another method was selected, do nothing
        }
       }
        if(@$_POST['shipping_foxpost']['parcel_target_machine_id'] == ''){
            vmError('', 'COM_VIRTUEMART_FOXPOST_VALID_SELECT');
            return false;
        }


        if ((isset($_POST['shipping_foxpost'])) AND (!empty($_POST['shipping_foxpost']))) {
            $_SESSION['foxpost']['shipping_foxpost'] = $_POST['shipping_foxpost'];
        }


        if (isset($_SESSION['foxpost'])) {
            if($cart->ST == 0){
             $cart->ST = @$cart->BT;
            }
        }

        $shipping = $_SESSION['foxpost']['parcelTargetAllMachinesDetail'][@$_POST['shipping_foxpost']['parcel_target_machine_id']];



        if (isset($_SESSION['foxpost'])) {

        $cart->ST['address_2'] = $shipping['city'].' '.$shipping['address'];

        }

        return $this->OnSelectCheck($cart);
	}

       public function plgVmOnCheckoutCheckDataShipment($cart) {

          self::plgVmOnSelectCheckShipment($cart);
           //print_r ($_SESSION['foxpost']['shipping_foxpost']);

           $Arr=@$_SESSION['foxpost']['shipping_foxpost'];
           //$Arr=array_push($Arr, "true");
            //print_r ($Arr);

            @$_SESSION['fox']=$Arr;

          if (isset($_SESSION['fox'])) {
            return $_SESSION['fox'];
          } else {
              return false;
          }

       }



        public function plgVmDisplayListFEShipment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {

		return $this->displayListFE ($cart, $selected, $htmlIn);
	}

	public function plgVmOnSelectedCalculatePriceShipment (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {

		return $this->onSelectedCalculatePrice ($cart, $cart_prices, $cart_prices_name);
	}

	function plgVmOnCheckAutomaticSelectedShipment (VirtueMartCart $cart, array $cart_prices = array(), &$shipCounter) {

		if ($shipCounter > 1) {
			return 0;
		}

		return $this->onCheckAutomaticSelected ($cart, $cart_prices, $shipCounter);
	}




    function plgVmonShowOrderPrint ($order_number, $method_id) {
	return $this->onShowOrderPrint ($order_number, $method_id);
    }

    function plgVmDeclarePluginParamsShipment ($name, $id, &$dataOld) {

        return $this->declarePluginParams ('shipment', $name, $id, $dataOld);
    }

    function plgVmDeclarePluginParamsShipmentVM3 (&$data) {
	return $this->declarePluginParams ('shipment', $data);
    }



   function plgVmSetOnTablePluginShipment(&$data,&$table){
        $name = $data['shipment_element'];
        $id = $data['shipment_jplugin_id'];
        return $this->setOnTablePluginParams ($name, $id, $table);
    }



    /* CURRENTAPI GETPARAMTERES FUNCTION HERE */


}
?>
