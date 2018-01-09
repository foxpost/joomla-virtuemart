<?php
##############################
#Copyright 2015 Foxpost Zrt  #   
#2015.02.05 ToHR             #
#Foxpost Parcels Widget Core #
#Orders Tables               #
##############################

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Orders table class
 * The class is is used to manage the orders in the shop.
 *
 * @package	VirtueMart
 * @author RolandD
 * @author Max Milbers
 */
class TableFoxpost extends VmTable {

	/** @var int Primary key */
	var $id = 0;
	/** @var int Order ID */
	var $virtuemart_order_id = 0;
    /** @var varchar Parcel ID */
    var $parcel_id = NULL;
    /** @var varchar Parcel status */
    var $parcel_status = NULL;
    /** @var text Parcel detail */
    var $parcel_detail = NULL;
    /** @var varchar Parcel target machine */
    var $parcel_target_machine_id = NULL;
    /** @var text Parcel target machine detail*/
    var $parcel_target_machine_detail = NULL;
    /** @var timestamp Sticker creation date*/
    var $sticker_creation_date = NULL;
    /** @var int Order number */
    var $order_number = NULL;
    /** @var timestamp created on*/
    var $created_on = NULL;


	/**
	 *
	 * @author Max Milbers
	 * @param $db Class constructor; connect to the database
	 *
	 */
	function __construct($db) {
		parent::__construct('#__virtuemart_shipment_plg_foxpost', 'id', $db);

		$this->setUniqueName('order_number');
		$this->setLoggable();

		//$this->setTableShortCut('o');
	}

	function check(){
		return parent::check();
	}


}

