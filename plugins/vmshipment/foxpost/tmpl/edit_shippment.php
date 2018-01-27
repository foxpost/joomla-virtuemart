<?php 
###############################
#Copyright 2015 Foxpost Zrt   #   
#2015.02.05 ToHR              #
#Foxpost Parcels Widget TMPL  #
###############################

   $potedData=(@$_SESSION['fox']);
   $carts=VirtueMartCart::getCart(); 
   
  
   
?>
<table id="foxpost_detail" width="350">
    <tr>
        <td>
            <br>

            <select id="shipping_foxpost" width="300" name="shipping_foxpost[parcel_target_machine_id]">
                 <?php foreach(@$viewData['foxpost']['parcelTargetAllMachinesId'] as $key => $parcelTargetMachineId): ?>
                       <option value='<?php echo $key ?>' <?php if(@$potedData['parcel_target_machine_id'] == $key){ echo "selected=selected";} ?>><?php echo @$parcelTargetMachineId;?></option>
                 <?php endforeach; ?>
           
            </select>
     
                <?php $v=0; ?>
                 <?php foreach(@$viewData['foxpost']['parcelTargetAllMachinesDetail'] as $key): ?>  
                       <input type="hidden" value="<?php echo $key['geolat'].", ".$key['geolng'];?>" name="<?php echo 'geoloc_'.$key['id']; ?>" id="<?php echo 'geoloc_'.$key['id']; ?>">
                       <?php $v++ ?>
                 <?php endforeach; ?>   
     
      
            <input type="hidden" id="box_machine_town" name="box_machine_town" disabled="disabled" />
            <input type="hidden" id="address" name="address" disabled="disabled" /> 
            <input type="hidden" id="address" name="address" disabled="disabled" />    
            <input maxlength="9" type='hidden' onKeyUp="checkPhone();"  name='shipping_foxpost[receiver_phone]' id="foxpost_phone" title="<?php echo JText::_ ('COM_VIRTUEMART_FOXPOST_VIEW_MOB_TITLE'); ?>" value="<?php print_r ($carts->BT['phone_1']); ?>" />
            <br> 
            <script type="text/javascript">
                
                 function user_function(value) {
                    var address = value.split(';');
                    //document.getElementById('town').value=address[1];
                    //document.getElementById('street').value=address[2]+address[3];
                    var box_machine_name = document.getElementById('name').value;
                    var box_machine_town = document.value=address[1];
                    var box_machine_street = document.value=address[2];


                    var is_value = 0;
                    document.getElementById('shipping_foxpost').value = box_machine_name;
                    var shipping_foxpost = document.getElementById('shipping_foxpost');

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

                jQuery(document).ready(function(){
                   
                            //alert('all machines');
                            var machines = {
                           
                            <?php foreach($viewData['foxpost']['parcelTargetAllMachinesId'] as $key => $parcelTargetAllMachineId): ?>
                                '<?php echo $key ?>' : '<?php echo addslashes($parcelTargetAllMachineId) ?>',
                                <?php endforeach; ?>
                            };
                  

                        jQuery('#shipping_foxpost option').remove();
                        jQuery.each(machines, function(val, text) {
                            jQuery('#shipping_foxpost').append(
                                    jQuery('<option></option>').val(val).html(text)
                            );
                        });

         

                    jQuery("#foxpost_detail").hide();
                    if(jQuery('#<?php echo 'shipment_id_'.$viewData['foxpost']['radio_id'];?>').is(':checked')) {
                        jQuery("#foxpost_detail").show();
                    }
                   
                    jQuery('input[type="radio"][name="virtuemart_shipmentmethod_id"]').click(function(){
                        if(jQuery('#<?php echo 'shipment_id_'.$viewData['foxpost']['radio_id'];?>').is(':checked')) {
                            jQuery("#foxpost_detail").show();
							initialize();
                
                        }else{
                            jQuery("#foxpost_detail").hide();
                  
                        }
                    });

                  });      
 
            </script>

                  
          <?php 
           // print_r ($viewData['foxpost']['parcelTargetAllMachinesDetail']);

            $maxrow=count($viewData['foxpost']['parcelTargetAllMachinesDetail'])-1;
     
            $jsOut="";
            $i=0;
            foreach($viewData['foxpost']['parcelTargetAllMachinesDetail'] as $key) {

                 $currArray = $viewData['foxpost']['parcelTargetAllMachinesDetail'][$key['id']];
              
                 $flat=floatval($currArray['geolat']);
                 $flng=floatval($currArray['geolng']);
                 $currname=$currArray['name'];
                 $currId=$currArray['id'];
                 $currDesc="$currname <br>".$currArray['open'];
                  $jsOut.="['$currname', $flat, $flng, $currId, '$currDesc' ]";
                                  
                  if ($maxrow > $i) {
                      $jsOut.=", \n";  
                  } else {
                      $jsOut.="\n"; 
                  }

                  $i++;
               }
       
                    $marker_red ='"'.JUri::root().'plugins/vmshipment/foxpost/images/marker.png"';
                    $marker_green ='"'.JUri::root().'plugins/vmshipment/foxpost/images/marker_green.png"';
          
               
          
              
                    $address=$carts->BT['zip'].' '.$carts->BT['city'].' '.$carts->BT['address_1'];

                    
           ?>
            
      





         <div id="map_canvas" style="width: 600px; height: 500px;"></div>


        </td>
    </tr>
</table>


<script type="text/javascript">
    jQuery(document).ready(function(){ 
       jQuery('#checkoutFormSubmit').click(function() {
        
        var SM = jQuery('#shipping_foxpost').val(); 
        var SP = jQuery('#foxpost_phone').val(); 
       
      // alert (SP);
        
        jQuery.ajax({
            url: "cart.php",
            type: "POST",
            data: {sm:SM, sp:SP},
        success: function(){
           // alert("success");
           // jQuery("#result").html('Submitted successfully');
        },
        error:function(){
            //alert("failure");
            //jQuery("#result").html('There is error while submit');
        }
        });
      });   
     });
</script>


                  
            <!-- GOOGLE GEOMAP --> 
       <script src="https://maps.google.com/maps/api/js?key=AIzaSyAtOKO-7tK8ovS9O46Oh0C6oWQaP1Mhiog&libraries=geometry,places,drawing&ext=.js&callback=initialize" async defer></script>
       <script type="text/javascript"> 
           
           var directionsDisplay;
           var directionsService;
           var map; 
       
                    initialize = function() {
                       directionsDisplay = new google.maps.DirectionsRenderer();
                       directionsService = new google.maps.DirectionsService();
                        var mapOptions = {
                            zoom: 11,
							center: {lat: 47.462363, lng: 19.062181},
                            mapTypeId: google.maps.MapTypeId.ROADMAP,
                            
                        }

                        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

                            var geocoder= new google.maps.Geocoder();
                             
                              geocoder.geocode({ 'address':'<?php echo $address; ?>'},function(results, status){
                              if (status == google.maps.GeocoderStatus.OK) {
                                   var myLatLong = results[0].geometry.location.toUrlValue();
                                   var a = myLatLong.split(","), s = a[0], d = a[1];
                                   showLoc(s, d);         
                                }
                            });
                    }
           
                   
                   function showLoc(currLat, currLng) {
                                destinationGenerate(currLat, currLng);
                               
							   
                                
                                jQuery("#shipping_foxpost").change(function(){
                                    var changeable=jQuery(this).val();
                                    var input_id = '#geoloc_'+changeable;
                                    var geotag = jQuery(input_id).val();
                                    var i = geotag.split(","), j = i[0], k = i[1];
                                    GenerateRoute(currLat, currLng, j, k);
                                  });

                                  var locations = [
                                      <?php print $jsOut; ?>
                                  ];
								  
                                       for (i = 0; i < locations.length; i++) { 
                                        var loan = ""; 
                                        marker = new google.maps.Marker({
                                          position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                          map: map,
                                          content: loan,
                                          icon: <?php echo $marker_red; ?>,
                                          zIndex: 5000
                                        });
                                       <?php if (!isset($potedData)) { ?> 
                                        
                                        find_closest_marker(currLat, currLng);
                                        
										
                                       <?php } else { ?>
                                           jQuery('select option[value="'+<?php print_r($_SESSION['fox']['parcel_target_machine_id']) ;?>+'"]').attr("selected",true);
                                           destinationGenerate(currLat, currLng)
                                        <?php  } ?>
										
                                        google.maps.event.addListener(marker, 'click', function(e) {
                                          map.setZoom(6);
                                          
                                        for (z = 0; z < locations.length; z++) {  
                                          var trig1 = (locations[z][1]);
                                          var trig2 = (locations[z][2]);
                                          var trig3 = (locations[z][3]);
                                          var trig = "("+trig1+", "+trig2+")";
                                          var currlng = e.latLng.lng().toFixed(6);
                                          var currlat = e.latLng.lat().toFixed(6);
                                          var currtrig= "("+currlat+", "+currlng+")";
                                          if (currtrig == trig) {
                                           
                                            jQuery('select option[value="'+trig3+'"]').attr("selected",true);
                                            var bounds = null;
                                           
                                            var infowindow = new google.maps.InfoWindow();

                                            infowindow.setContent(locations[z][4]);
                                            infowindow.open(map, this);
                                            destinationGenerate(currLat, currLng);
                                
                                          }
                                        }
                                      });
                                     };  
                                    }; 
                                    
                       function find_closest_marker(currLat, currLng) {
                             var locations = [
                                  <?php print $jsOut; ?>
                             ];
                             var pi = Math.PI;
                                    var R = 6371; //equatorial radius
                                    var distances = [];
                                    var closest = -1;

                                    for( t=0;t<locations.length; t++ ) {  
                                        var lat2 = locations[t][1];
                                        var lon2 = locations[t][2];

                                        var chLat = lat2-currLat;
                                        var chLon = lon2-currLng;

                                        var dLat = chLat*(pi/180);
                                        var dLon = chLon*(pi/180);

                                        var rLat1 = currLat*(pi/180);
                                        var rLat2 = currLng*(pi/180);

                                        var a = Math.sin(dLat/2) * Math.sin(dLat/2) + 
                                                    Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(rLat1) * Math.cos(rLat2); 
                                        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                                        var d = R * c;

                                        distances[t] = d;
                                        if ( closest == -1 || d < distances[closest] ) {
                                            closest = t;
                                        }
                                    }

                                    // (debug) The closest marker is:
                                    var trig3 = (locations[closest][3]);
                                    jQuery('select option[value="'+trig3+'"]').attr("selected",true);
                                    destinationGenerate(currLat, currLng)
                                }             
                                   
                                    
                       function destinationGenerate(currLat, currLng) {
                                                              
                                  if (typeof changeable == 'undefined') {
                                     var defaultTerminal = jQuery('#shipping_foxpost').val(); 
                                     var input_id = '#geoloc_'+defaultTerminal;
                                     var geotag = jQuery(input_id).val();
                                     var i = geotag.split(","), j = i[0], k = i[1];
                                     GenerateRoute(currLat, currLng, j, k);
                                  }
                                  
                                  
                                  
                        }               
            
                        function GenerateRoute(currLat, currLng, j, k){
                                var start = new google.maps.LatLng(parseFloat(currLat).toFixed(6), parseFloat(currLng).toFixed(6));
                                var end = new google.maps.LatLng(parseFloat(j).toFixed(6), parseFloat(k).toFixed(6));
                                var startMarker = new google.maps.Marker({
                                    position: start,
                                    map: map,
                                    draggable: false,
                                    icon: <?php echo $marker_green?>,
                                    zIndex: 5000
                                  
                                });
                         
                                var endMarker = new google.maps.Marker({
                                    position: end,
                                    map: map,
                                    draggable: false,
                                    center: end
                                });
          
                                var bounds = new google.maps.LatLngBounds();
                                bounds.extend(start);
                                bounds.extend(end);
                                map.fitBounds(bounds);
                                var request = {
                                    origin: start,
                                    destination: end,
                                    travelMode: google.maps.TravelMode.DRIVING
                                };
								
                                var centerLoc = new google.maps.LatLng(parseFloat(currLat).toFixed(6), parseFloat(currLng).toFixed(6)); 
                                 
								 map.setCenter(end);
                                 
                                 directionsService.route(request, function (response, status) {
                                    if (status == google.maps.DirectionsStatus.OK) {
                                        directionsDisplay.setDirections(response);
                                        directionsDisplay.setMap(map);
                                        map.setCenter(end);
                                        var markers = [];

                                        var currCenter = end;
                                        google.maps.event.trigger(map, 'resize');
                                        map.setCenter(currCenter);
                                    }
                                    
                                });
                            
                            }
                               
                       
                        
                    
             
       //jQuery(document).ready(function(e) { initialize() });
	   //google.maps.event.addDomListener(window, "load", initialize);

        
     
        </script>

