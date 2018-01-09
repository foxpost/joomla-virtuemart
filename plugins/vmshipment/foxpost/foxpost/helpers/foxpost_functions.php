<?php

defined ('_JEXEC') or die('Restricted access');

/**
 * @version $Id: klarnahandler.php 6480 2012-09-28 11:46:33Z alatak $
 *
 * @author Valérie Isaksen
 * @package VirtueMart
 * @copyright Copyright (C) 2012 iStraxx - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class foxpost_functions {

    public static function test(){
        return 'test';        
    }

    public static function connect_foxpost_terminals($params = array()){

        $params = array_merge(
            array(
                'url' => $params['url'],
                'token' => $params['token'],
                'ds' => '?',
                'methodType' => $params['methodType'],
                'params' => $params['params']
            ),
            $params
                
                
        );

        
        
        $ch = curl_init($params['url'].'/places');
        curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_HEADER,"Accept:application/vnd.cleveron+json; version=1.0");
        curl_setopt($ch,CURLOPT_HEADER,"Content-Type:application/vnd.cleveron+xml");
        curl_setopt($ch,CURLOPT_USERPWD, $params['token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res =curl_exec($ch);
        return array(
            'result' => json_decode(curl_exec($ch)),
            'info' => curl_getinfo($ch),
            'errno' => curl_errno($ch),
            'error' => curl_error($ch)
        );
          $res =curl_exec($ch);
          curl_close($ch);
          
          
    }
    
  

    public static function generate($type = 1, $length){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";

        if($type == 1){
            # AZaz09
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        }elseif($type == 2){
            # az09
            $chars = "abcdefghijklmnopqrstuvwxyz1234567890";
        }elseif($type == 3){
            # AZ
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }elseif($type == 4){
            # 09
            $chars = "0123456789";
        }

        $token = "";
            for ($i = 0; $i < $length; $i++) {
                $j = rand(0, strlen($chars) - 1);
                if($i==0 && $j == 0){
                    $j = rand(2,9);
                }
                $token .= $chars[$j];
            }
        return $token;
    }

    public static function getParcelStatus(){
        return array(
            'Created' => 'Created',
            'Prepared' => 'Prepared'
        );
    }

    public static function calculateDimensions($product_dimensions = array(), $config = array()){
        $parcelSize = 'A';
        $is_dimension = true;

        if(!empty($product_dimensions)){
            // DIMENSION A 
            $maxDimensionFromConfigSizeA = explode('x', strtolower(trim($config['MAX_DIMENSION_A'])));
            $maxWidthFromConfigSizeA = (float)trim(@$maxDimensionFromConfigSizeA[0]);
            $maxHeightFromConfigSizeA = (float)trim(@$maxDimensionFromConfigSizeA[1]);
            $maxDepthFromConfigSizeA = (float)trim(@$maxDimensionFromConfigSizeA[2]);
            // flattening to one dimension
            $maxSumDimensionFromConfigSizeA = $maxWidthFromConfigSizeA + $maxHeightFromConfigSizeA + $maxDepthFromConfigSizeA;
            // DIMENSION B 
            $maxDimensionFromConfigSizeB = explode('x', strtolower(trim($config['MAX_DIMENSION_B'])));
            $maxWidthFromConfigSizeB = (float)trim(@$maxDimensionFromConfigSizeB[0]);
            $maxHeightFromConfigSizeB = (float)trim(@$maxDimensionFromConfigSizeB[1]);
            $maxDepthFromConfigSizeB = (float)trim(@$maxDimensionFromConfigSizeB[2]);
            // flattening to one dimension
            $maxSumDimensionFromConfigSizeB = $maxWidthFromConfigSizeB + $maxHeightFromConfigSizeB + $maxDepthFromConfigSizeB;
            // DIMENSION C
            $maxDimensionFromConfigSizeC = explode('x', strtolower(trim($config['MAX_DIMENSION_C'])));
            $maxWidthFromConfigSizeC = (float)trim(@$maxDimensionFromConfigSizeC[0]);
            $maxHeightFromConfigSizeC = (float)trim(@$maxDimensionFromConfigSizeC[1]);
            $maxDepthFromConfigSizeC = (float)trim(@$maxDimensionFromConfigSizeC[2]);

            $maxSumDimensionFromConfigSizeC = $maxWidthFromConfigSizeC + $maxHeightFromConfigSizeC + $maxDepthFromConfigSizeC;
           
            // DIMENSION D
            $maxDimensionFromConfigSizeD = explode('x', strtolower(trim($config['MAX_DIMENSION_D'])));
            $maxWidthFromConfigSizeD = (float)trim(@$maxDimensionFromConfigSizeD[0]);
            $maxHeightFromConfigSizeD = (float)trim(@$maxDimensionFromConfigSizeD[1]);
            $maxDepthFromConfigSizeD = (float)trim(@$maxDimensionFromConfigSizeD[2]);

            $maxSumDimensionFromConfigSizeD = $maxWidthFromConfigSizeD + $maxHeightFromConfigSizeD + $maxDepthFromConfigSizeD;
            
            // DIMENSION E
            $maxDimensionFromConfigSizeE = explode('x', strtolower(trim($config['MAX_DIMENSION_E'])));
            $maxWidthFromConfigSizeE = (float)trim(@$maxDimensionFromConfigSizeE[0]);
            $maxHeightFromConfigSizeE = (float)trim(@$maxDimensionFromConfigSizeE[1]);
            $maxDepthFromConfigSizeE = (float)trim(@$maxDimensionFromConfigSizeE[2]);
            
            if($maxWidthFromConfigSizeE == 0 || $maxHeightFromConfigSizeE == 0 || $maxDepthFromConfigSizeE == 0){
                // bad format in admin configuration
                $is_dimension = false;
            }
            // flattening to one dimension
            $maxSumDimensionFromConfigSizeE = $maxWidthFromConfigSizeE + $maxHeightFromConfigSizeE + $maxDepthFromConfigSizeE;
            $maxSumDimensionsFromProducts = 0;
            
            
            foreach($product_dimensions as $product_dimension){
                $dimension = explode('x', $product_dimension);
                $width = trim(@$dimension[0]);
                $height = trim(@$dimension[1]);
                $depth = trim(@$dimension[2]);
                if($width == 0 || $height == 0 || $depth){
                    // empty dimension for product
                    continue;
                }

                if(
                    $width > $maxWidthFromConfigSizeE ||
                    $height > $maxHeightFromConfigSizeE ||
                    $depth > $maxDepthFromConfigSizeE
                ){
                    $is_dimension = false;
                }

                $maxSumDimensionsFromProducts = $maxSumDimensionsFromProducts + $width + $height + $depth;
                if($maxSumDimensionsFromProducts > $maxSumDimensionFromConfigSizeE){
                    $is_dimension = false;
                }
            }
            if($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeA){
                $parcelSize = 'A';
            }elseif($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeB){
                $parcelSize = 'B';
            }elseif($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeC){
                $parcelSize = 'C';
            }elseif($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeD){
                $parcelSize = 'D';
            }elseif($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeE){
                $parcelSize = 'E';
            }
        }

        $parcelSizeRemap = array(
            'UK' => array(
                'A' => 'S',
                'B' => 'M',
                'C' => 'L',
                'D' => 'XL',
                'E' => 'XXL'
            )
        );

        return array(
           
            'parcelSize' => $parcelSize,
            'isDimension' => $is_dimension
        );
    }

    public static function getCurrentApi(){

        $currentApi = 'HU';
        $config = self::getParameters();
        $db = JFactory::getDBO();
        $q = 'SELECT `country_2_code` FROM `#__virtuemart_countries` WHERE `virtuemart_country_id`="'.$config['allowed_country'].'"';
        $db->setQuery($q);
        
        $country = $db->loadResult();
        //print_r ($config['ALLOWED_COUNTRY']);
        
        if($config['allowed_country'] && !is_array($config['allowed_country'])){
            $currentApi = $country;
           
            if($currentApi == 'HU'){
                $currentApi = 'HU';
            }
        }
        return $currentApi;
    }

     public static function getParameters(){
        $db = JFactory::getDBO();
        $q = 'SELECT `shipment_params` FROM `#__virtuemart_shipmentmethods` WHERE `shipment_element`="foxpost"';
        $db->setQuery($q);
        
        $shipment_params = $db->loadResult();
        $shipment_params=explode("|", stripcslashes($shipment_params)); 
        
      
        foreach($shipment_params as $value){
           // print $value.'<br>';
            $ex=explode("=", $value);
            $ex1=str_replace('"', '', @$ex['1']);
            //print $ex1.'<br>';
            
            $config[$ex[0]] = $ex1;
        }
        
       // print_r ($config);
             
        $config['allowed_country'] = str_replace('[', '', $config['allowed_country']);
        $config['allowed_country'] = str_replace(']', '', $config['allowed_country']);
        return $config;

     }
    public static function getVersion(){
        return '1.0.0';
    }
    

    public static function createFlags() {
        
        // Before run the script we make a database update to check for products paramteres if the table is esixts.
           
        $checktable = JFactory::getDBO();
        $check = "CREATE TABLE IF NOT EXISTS #__virtuemart_shipment_plg_foxpost_missing_products (
                          ID int(11) AUTO_INCREMENT,
                          product_id text NOT NULL,
                          PRIMARY KEY  (ID)
                          )";
        $checktable->setQuery($check);
        $checktable->query();
        
       $exitsrow=jFactory::getDBO();
       $ex="SELECT ID FROM #__virtuemart_shipment_plg_foxpost_missing_products" ;
       $exitsrow->setQuery($ex); 
        $rows =$exitsrow->loadObjectList(); 
        $my_count=count($rows);
       
       //print $my_count;    
        
        $product_list = JFactory::getDBO();
        $p_list = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_weight = '' OR product_width = '' OR product_length = '' OR product_height = '' ";
        $product_list->setQuery($p_list);
        $rows1 = $product_list->loadObjectList(); 
        $a='';
        
        foreach( $rows1 as $row1 ) {
         $a.=$row1->virtuemart_product_id.', ';            
        } 
        
       if ($my_count == 0) {
          $ins = JFactory::getDBO();
          $insert = "INSERT INTO #__virtuemart_shipment_plg_foxpost_missing_products VALUES ('', '".$a."')";
          $ins->setQuery($insert);
          $ins->query();
       } else {
          $ins = JFactory::getDBO();
          $insert = "UPDATE #__virtuemart_shipment_plg_foxpost_missing_products SET product_id='".$a."' WHERE id='1' LIMIT 1";
          $ins->setQuery($insert);
          $ins->query();  
       }


        //
        
    
    }
    
    
   }

