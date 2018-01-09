<?php
##############################
#Copyright 2015 Foxpost Zrt  #   
#2015.02.05 ToHR             #
#Foxpost Parcels Widget Core #
#Orders View                 #
##############################


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

        $jlang = JFactory::getLanguage();
        $jlang->load('com_vmshipment_foxpost', JPATH_PLUGINS.'/vmshipment/foxpost', 'hu-HU', true);
        $jlang->load('com_vmshipment_foxpost', JPATH_PLUGINS.'/vmshipment/foxpost', $jlang->getDefault(), true);
        $jlang->load('com_vmshipment_foxpost', JPATH_PLUGINS.'/vmshipment/foxpost', null, true);  

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmviewadmin.php');

if (JVM_VERSION === 2) {
    require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'foxpost' . DS . 'foxpost' . DS . 'helpers' . DS . 'define.php');
} else {
     require (JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'foxpost' . DS . 'foxpost' . DS . 'helpers' . DS . 'define.php');
}

require_once (JPATH_VMFOXPOSTPLUGIN . DS . 'foxpost' . DS . 'foxpost' . DS . 'helpers' . DS . 'foxpost_functions.php');


/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewFoxpost extends VmViewAdmin {

	function display($tpl = null) {

        $this->loadHelper('html');

        if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');

        $curTask = JRequest::getWord('task');

        if($curTask == 'edit'){
            $this->setLayout('parcel');
            $id = JRequest::getVar('id');

          
        }else{
            $this->setLayout('parcels');

            $model = VmModel::getModel();
            $this->addStandardDefaultViewLists($model,'created_on');
            $this->lists['state_list'] = $this->renderParcelstatesList();
            $parcelslist = $model->getParcelsList();

         

            JToolBarHelper::save('massCSVcod', JText::_('COM_VIRTUEMART_FOXPOST_EXCEL_EXPORT'));
            JToolBarHelper::save('massCSV', JText::_('COM_VIRTUEMART_FOXPOST_EXCEL_EXPORT_WITHOUT_COD'));
            
            JToolBarHelper::title(JText::_('VMSHIPMENT_FOXPOST_PLUGIN_DESC'));
          
           
            $model = VmModel::getModel('paymentmethod');
            $payments = $model->getPayments();
            /* Assign the data */
            $this->assignRef('parcelslist', $parcelslist);
            $this->assignRef('payments', $payments);

            $pagination = $model->getPagination();
            $this->assignRef('pagination', $pagination);
        }
        
        
        if($curTask == 'foxpost_missing_products_details'){
            
            $this->setLayout('foxpost_missing_products_details');
        }

		parent::display();
	}

    public function renderParcelstatesList() {
        $parcelstates = JRequest::getWord('parcel_status','');
        return VmHTML::select( 'parcel_status', foxpost_functions::getParcelStatus(),  $parcelstates,'class="inputbox" onchange="this.form.submit();"');
    }

}