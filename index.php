<?php
require_once('config.php');
require_once(CLIENT);
session_start();

// Setup API client and get access token
$oauth = PodioOAuth::instance();
$baseAPI = PodioBaseAPI::instance(CLIENT_ID, CLIENT_SECRET);

// Obtain access token and init API class
if (!isset($_SESSION['access_token'])) {
  $oauth->getAccessToken('password', array('username' => USERNAME, 'password' => PASSWORD));
  $api = new PodioAPI();
  $_SESSION['access_token'] = $oauth->access_token;
  $_SESSION['refresh_token'] = $oauth->refresh_token;
}
else {
  $oauth->access_token = $_SESSION['access_token'];
  $oauth->refresh_token = $_SESSION['refresh_token'];
  $api = new PodioAPI();
}

// Get all items in Location Scouting app.
// Find the address and convert to JSON for outputting
// in the JavaScript below.
$items = $api->item->getItems(APP_ID, 100, 0, 'title', 0);
$markers = array();
foreach ($items['items'] as $item) {
  $marker = array(
    'title' => $item['title'],
  );
  foreach ($item['fields'] as $field) {
    if ($field['type'] == 'location') {
      $marker['address'] = $field['values'][0]['value'];
    }
    if ($field['type'] == 'state') {
      $marker['type'] = $field['values'][0]['value'];
      switch ($marker['type']) {
        case 'Event location':
          $marker['icon'] = 'public/conference.png';
          break;
        case 'Accomodation':
          $marker['icon'] = 'public/hotel.png';
          break;
        case 'Transportation hub':
          $marker['icon'] = 'public/train.png';
          break;
        default:
          $marker['icon'] = 'public/sight.png';
          break;
      }
    }
    if ($field['type'] == 'text') {
      $marker['description'] = $field['values'][0]['value'];
    }
  }
  $markers[] = $marker;
}

?><!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px; font-family: Arial, sans-serif; font-size: 12px; }
  h1 { font-size: 1.5em; margin: 0 0 0.3em 0; }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
  var map;
  var geocoder;
  var podio_markers = <?php print json_encode($markers); ?>;
  
  function dropPin() {
    if (podio_markers.length > 0) {
      geocoder.geocode( { 'address': podio_markers[0]['address']}, function(results, status) {
        var current_marker = podio_markers.shift();
        if (status == google.maps.GeocoderStatus.OK) {
          if (podio_markers.length == 0) {
            map.setCenter(results[0].geometry.location);
          }
          
          // Add marker
          var marker = new google.maps.Marker({
            map: map, 
            position: results[0].geometry.location,
            title: current_marker['title'],
            icon: current_marker['icon']
          });
          
          // Add info window
          var infowindow = new google.maps.InfoWindow({
            content: '<div id="content"><h1 id="firstHeading" class="firstHeading">'+current_marker['title'] +'</h1>'+ current_marker['description']+'</div>',
            maxWidth: 300
          });
          google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map,marker);
          });
          
        } else {
          console.log("Geocode was not successful for the following reason: " + status);
        }
        dropPin();
      });
    }
  }
  
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(55.705015,12.556601);
    var myOptions = {
      zoom: 14,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);
        
    dropPin();
  }

</script>
</head>
<body onload="initialize()">
  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>