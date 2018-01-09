<?php
###############################
#Copyright 2015 Foxpost Zrt   #   
#2015.02.05 ToHR              #
#Foxpost Parcels Widget       #
#Orders Controllers           #
###############################

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');

if (JVM_VERSION === 3) {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'foxpost' . DS . 'foxpost' . DS . 'helpers' . DS . 'define.php');
} else {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'foxpost' . DS . 'helpers' . DS . 'define.php');
}

if (!class_exists ('vmPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmplugin.php');
}

require_once (JPATH_ROOT .'/plugins/vmshipment/foxpost' . DS . 'foxpost' . DS . 'helpers' . DS . 'foxpost_functions.php');
//require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'parameterparser.php');


       

/**
 * Orders Controller
 *
 * @package    VirtueMart
 * @author
 */
class VirtuemartControllerFOXPOST extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author
	 */

    protected $config;

	function __construct() {
		parent::__construct();

        //$parameters = new vmParameters('TableShipmentmethods', 'foxpost', 'plugin', 'vmshipment');
        //print_r($parameters);
        //foxpost_functions::setLang();
        $this->config = foxpost_functions::getParameters();
    }

	/*public function edit($layout='parcel'){

        $id = JRequest::getVar('id');
        $mainframe = Jfactory::getApplication();

        if (empty($id)) {
            vmError('Id is empty');
            $mainframe->redirect('index.php?option=com_virtuemart&view=foxpost');
        }

		parent::edit($layout);
	}*/

    public function massSendOrder(){
        $dbCFG = JFactory::getDBO();
        $qCFG = "SELECT shipment_params FROM #__virtuemart_shipmentmethods WHERE shipment_element='foxpost'";
        $dbCFG->setQuery($qCFG);
        $currentCFG= $dbCFG->loadObject();
        $currcfg=explode("|", $currentCFG->shipment_params);
      
        // U
        $AuthSplit=explode("=", $currcfg[2]);
        $AuthSplit[1]=str_replace('"', '', $AuthSplit[1]);
                       // P  
        $AuthSplit1=explode("=", $currcfg[3]);
        $AuthSplit1[1]=str_replace('"', '', $AuthSplit1[1]); 
          
        $Auth=urlencode($AuthSplit[1]).':'.urlencode($AuthSplit1[1]);
        $Authurl=explode("=", $currcfg[1]);
        $Authurl[1]=str_replace('"', '', $Authurl[1]); 
        $Authurl[1]=stripslashes($Authurl[1]); 
        
        
        
        $mainframe = Jfactory::getApplication();
        $model = VmModel::getModel();
        $parcelsIds = JRequest::getVar('cid',  0, '', 'array');
    
        foreach ($parcelsIds as $keyloc => $selected) {       
    
            $currCid='cid_'.$row->id;
                $currBC='barcodeToPost_'.$selected;
                $currDatas='send_datas_'.$selected;
                
               
                $string = (@$_POST[$currDatas]);
                // Check if already posted // 
                $db2 = JFactory::getDBO();
                $q2 = "SELECT parcel_detail FROM #__virtuemart_shipment_plg_foxpost WHERE virtuemart_order_id='".@$_POST[$currBC]."'";
           
                $db2->setQuery($q2);
                $currOrder=$db2->loadObject();
                $need_up=$currOrder->parcel_detail;
                $needs_check=json_decode($currOrder->parcel_detail);  
                
           
                if (!isset($needs_check->glob_barcode)) {        
      
                 $context = stream_context_create(array(
                    'http' => array(
                    'header'  => 'Authorization: Basic ' . base64_encode($Auth) . "\r\n" .
                        'Accept: application/vnd.cleveron+json; version=1.0' . "\r\n" .
                        'Content-Type: application/vnd.cleveron+json; version=1.0' . "\r\n" .
                        'Content-Length: ' . strlen($string) . "\r\n" .
                        'Accept-Encoding: gzip, deflate',
                        'method'=>'POST',
                        'content' => $string
                    )
                ));
            
               $data = file_get_contents($Authurl[1] . 'orders', false, $context);
               $datas=json_decode($data);
               $cglobal=$datas->barcode;
                if ((!isset($needs_check->stat)) AND ($needs_check->stat == 1))  {
               $add_string=' ,"glob_barcode": "'.$cglobal.'", "stat": 1 }';
               } else {
                  $add_string=' ,"glob_barcode": "'.$cglobal.'" }'; 
               }
               //echo "UPDATE #__virtuemart_shipment_plg_foxpost SET parcel_detail='".$need_up."' WHERE virtuemart_order_id='".@$_POST[$currBC]."' LIMIT 1";
               $need_up=substr($need_up, 0, -1).$add_string;  
               $dbUp = JFactory::getDBO();
               $qUp = "UPDATE #__virtuemart_shipment_plg_foxpost SET parcel_detail='".$need_up."' WHERE virtuemart_order_id='".@$_POST[$currBC]."' LIMIT 1";    
               $dbUp->setQuery($qUp);
               $dbUp->query();
               } 
            
            }
          $mainframe->redirect('index.php?option=com_virtuemart&view=foxpost');
         }

    public function massCSVcod(){
        

        
        $mainframe = Jfactory::getApplication();
        $model = VmModel::getModel();
        $parcelsIds = JRequest::getVar('cid',  0, '', 'array');
        
    
        $filename=date("Ymd_hms");
        $ourFileName = JPATH_ROOT."/plugins/vmshipment/foxpost/csv/$filename.csv";
       
        $whatPutIn=array( 'Vásárló neve', 'Telefonszáma', 'Email címe', 'Cél Terminál', 'Utánvét összege', 'Súly', 'Termékek');
        $whatPutIn = array_map("utf8_decode", $whatPutIn);
        file_put_contents($ourFileName,$whatPutIn);
        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
        fputcsv($ourFileHandle, $whatPutIn, ';');
        foreach ($parcelsIds as $keyloc => $selected) {  

        $mass_up = JFactory::getDBO();
        $massup = "SELECT a.parcel_detail, a.virtuemart_order_id, a.parcel_target_machine_id, b.order_currency,  a.order_weight, c.first_name, c.last_name, c.middle_name FROM #__virtuemart_shipment_plg_foxpost a "
                . "LEFT JOIN #__virtuemart_orders b ON a.order_number=b.order_number "
                . "LEFT JOIN #__virtuemart_userinfos c ON a.created_by=c.virtuemart_user_id WHERE a.virtuemart_order_id='".$selected."'";  
        $mass_up->setQuery($massup);
        $rows = $mass_up->loadAssocList(); 
       
        foreach( $rows as $row => $key ) { 
              $product_list1 = JFactory::getDBO();
              $p_list1 = "SELECT order_item_name FROM #__virtuemart_order_items WHERE virtuemart_order_id='".$key['virtuemart_order_id']."'";
              $product_list1->setQuery($p_list1);
              $result = $product_list1->loadObjectList();
           
               $mennyirow=count($result);
               $vr=1;
               foreach( $result as $resu => $keys ) {
               $actItem=$keys->order_item_name;  
               
            
               if ($vr==$mennyirow) {
                $itemlist.=$actItem.' ';
               } else {
                $itemlist.=$actItem.' \ '; 
               }
                 $vr++;
               }
         
        $itemlist = preg_replace('/(^|;)"([^"]+)";/','$1$2;',$itemlist);
        $currency_model = VmModel::getModel('currency');
	$displayCurrency = $currency_model->getCurrency($key['order_currency']);

          $rec_name=$key['first_name'].' '.$key['middle_name'].' '.$key['last_name']; 

          $det=json_decode($key['parcel_detail']);
            $list = array($rec_name, $det->receiver->phone, $det->receiver->email, $key['parcel_target_machine_id'], $det->cod_amount, $key['order_weight'], $itemlist);
            
            $array = array_map("utf8_decode", $list);
             fputcsv($ourFileHandle, $array, ';');
             $itemlist="";
                $need_up=$key['parcel_detail'];



               $dbUp = JFactory::getDBO();
               $qUp = "UPDATE #__virtuemart_shipment_plg_foxpost SET variables=1 WHERE virtuemart_order_id='".(int)$selected."' LIMIT 1";    
               $dbUp->setQuery($qUp);
               $dbUp->query();
     
         
        }
       }
        fclose($ourFileHandle); 
        
        //print $itemlist;
        $mainframe->redirect('index.php?option=com_virtuemart&view=foxpost&file='.$filename.'.csv');
    }
    
    public function massCSV(){
        
        ?>
       
        <?php
        $mainframe = Jfactory::getApplication();
        $model = VmModel::getModel();
        $parcelsIds = JRequest::getVar('cid',  0, '', 'array');

        
        $filename=date("Ymd_hms");
        $ourFileName = JPATH_ROOT."/plugins/vmshipment/foxpost/csv/$filename.csv";
       
        $whatPutIn=array( 'Vásárló neve', 'Telefonszáma', 'Email címe', 'Cél Terminál', 'Utánvét összege', 'Súly', 'Termékek');
        $whatPutIn = array_map("utf8_decode", $whatPutIn);
        file_put_contents($ourFileName,$whatPutIn);
        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
        fputcsv($ourFileHandle, $whatPutIn, ';');
        foreach ($parcelsIds as $keyloc => $selected) {  

        $mass_up = JFactory::getDBO();
        $massup = "SELECT a.parcel_detail, a.virtuemart_order_id, a.parcel_target_machine_id, b.order_currency,  a.order_weight, c.first_name, c.last_name, c.middle_name FROM #__virtuemart_shipment_plg_foxpost a "
                . "LEFT JOIN #__virtuemart_orders b ON a.order_number=b.order_number "
                . "LEFT JOIN #__virtuemart_userinfos c ON a.created_by=c.virtuemart_user_id WHERE a.virtuemart_order_id='".$selected."'";  
        $mass_up->setQuery($massup);
        $rows = $mass_up->loadAssocList(); 
       
        foreach( $rows as $row => $key ) { 
              $product_list1 = JFactory::getDBO();
              $p_list1 = "SELECT order_item_name FROM #__virtuemart_order_items WHERE virtuemart_order_id='".$key['virtuemart_order_id']."'";
              $product_list1->setQuery($p_list1);
              $result = $product_list1->loadObjectList();
           
               $mennyirow=count($result);
               $vr=1;
               foreach( $result as $resu => $keys ) {
               $actItem=$keys->order_item_name;  
               
            
               if ($vr==$mennyirow) {
                $itemlist.=$actItem.' ';
               } else {
                $itemlist.=$actItem.' \ '; 
               }
                 $vr++;
               }
         
        $itemlist = preg_replace('/(^|;)"([^"]+)";/','$1$2;',$itemlist);
        $currency_model = VmModel::getModel('currency');
	$displayCurrency = $currency_model->getCurrency($key['order_currency']);

          $rec_name=$key['first_name'].' '.$key['middle_name'].' '.$key['last_name']; 

          $det=json_decode($key['parcel_detail']);
            $list = array($rec_name, $det->receiver->phone, $det->receiver->email, $key['parcel_target_machine_id'], '', $key['order_weight'], $itemlist);
            
            $array = array_map("utf8_decode", $list);
             fputcsv($ourFileHandle, $array, ';');
             $itemlist='';
                $need_up=$key['parcel_detail'];

  
               $dbUp = JFactory::getDBO();
               $qUp = "UPDATE #__virtuemart_shipment_plg_foxpost SET variables=1 WHERE virtuemart_order_id='".(int)$selected."' LIMIT 1";    
               $dbUp->setQuery($qUp);
               $dbUp->query();
         
         
        }
       }
        fclose($ourFileHandle); 
        
        //print $itemlist;
        $mainframe->redirect('index.php?option=com_virtuemart&view=foxpost&file='.$filename.'.csv');
    }

    public function massCancel(){
        $mainframe = Jfactory::getApplication();
        $model = VmModel::getModel();
        $parcelsIds = JRequest::getVar('cid',  0, '', 'array');

        $countCancel = 0;
        $countNonCancel = 0;

        $parcelsCode = array();
        foreach ($parcelsIds as $key => $id) {
            $db = JFactory::getDBO();
            $q = "SELECT * FROM #__virtuemart_shipment_plg_foxpost WHERE id='".(int)$id."'";
            $db->setQuery($q);
            $result_db = $db->loadObject();

            if($result_db->parcel_id != ''){
                $parcelsCode[$id] = $result_db->parcel_id;
            }else{
                continue;
            }
        }

        if(empty($parcelsCode)){
            vmError('Parcel ID is empty');
        }else{
            foreach($parcelsCode as $id => $parcelId){
                $parcelApi = foxpost_functions::connect_foxpost_terminals(array(
                    'url' => $this->config['API_URL'].'parcels',
                    'token' => $this->config['API_KEY'],
                    'methodType' => 'PUT',
                    'params' => array(
                        'id' => $parcelId,
                        'status' => 'cancelled'
                    )
                ));

                if(@$parcelApi['info']['http_code'] != '204'){
                    $countNonCancel = count($parcelsIds);
                    if(!empty($parcelApi['result'])){
                        foreach(@$parcelApi['result'] as $key => $error){
                            if(is_array($error)){
                                foreach($error as $subKey => $subError){
                                    vmError('Parcel '.$parcelId.' '.$subError);
                                }
                            }else{
                                vmError('Parcel '.$parcelId.' '.$error);
                            }
                        }
                    }
                }else{
                    foreach (@$parcelApi['result'] as $parcel) {
                        $fields = array(
                            'parcel_status' => @$parcel->status
                        );
                        $db = JFactory::getDBO();

                        $q = "UPDATE #__virtuemart_shipment_plg_foxpost SET
                            parcel_status='".$fields['parcel_status']."'
                            WHERE parcel_id ='".@$parcel->id."'";
                        $db->setQuery($q);
                        $db->query();

                        $countCancel++;
                    }
                }
            }
        }

        if ($countNonCancel) {
            if ($countNonCancel) {
                vmError($countNonCancel.' '.JText::_ ('COM_VIRTUEMART_FOXPOST_MSG_PARCEL_4'));
            } else {
                vmError('COM_VIRTUEMART_FOXPOST_MSG_PARCEL_5');
            }
        }
        if ($countCancel) {
            vmInfo($countNonCancel.' '.JText::_ ('COM_VIRTUEMART_FOXPOST_MSG_PARCEL_6'));
        }

        $mainframe->redirect('index.php?option=com_virtuemart&view=foxpost');
    }

   

}
// pure php no closing tag

