<?php
##############################
#Copyright 2015 Foxpost Zrt  #   
#2015.02.05 ToHR             #
#Foxpost Parcels Widget Core #
#Orders Models               #
##############################
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

class VirtueMartModelFoxpost extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('foxpost');
        //$this->addvalidOrderingFieldName(array('parcel_id' ) );
    }

    public function getParcelsList($uid = 0, $noLimit = false)
    {
        $this->_noLimit = $noLimit;
        $select = '*';
        $from = $this->getParcelsListQuery();

        if ($search = JRequest::getString('search', false)){

            $search = '"%' . $this->_db->getEscaped( $search, true ) . '%"' ;

            $searchFields = array();
            $searchFields[] = 'parcel_target_machine_id';
            $searchFields[] = 'parcel_detail';
            $searchFields[] = 'parcel_target_machine_detail';
            $where[] = implode (' LIKE '.$search.' OR ', $searchFields) . ' LIKE '.$search.' ';
        }

       
        
        if ($parcel_status = JRequest::getString('parcel_status', false)){
            $where[] = ' parcel_status = "'.$parcel_status.'" ';
        } else {
            $where[]='1=1';
        
        }
        
         
        if (count ($where) > 0) {
            $whereString = ' WHERE (' . implode (' AND ', $where) . ') ';
        }
        else {
            $whereString = '';
        }

        if ( JRequest::getCmd('view') == 'foxpost') {
            $ordering = $this->_getOrdering();
        } else {
            $ordering = ' order by created_by DESC';
        }

        $this->_data = $this->exeSortSearchListQuery(0,$select,$from,$whereString,'',$ordering);

        return $this->_data ;
    }

    private function getParcelsListQuery()
    {
        return ' FROM #__virtuemart_shipment_plg_foxpost';
    }


}

// No closing tag
