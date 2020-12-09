<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "boccob";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT featureID, mapID, latlonX, latlonY, description, title FROM openlayersmapfeature";
$return_arr = array();
$fetch = mysqli_query($conn,$sql); 
$numrows = mysqli_num_rows($fetch);
if($numrows > 0){
	while ($row = mysqli_fetch_assoc($fetch)) {
	    $row_array['featureID'] = $row['featureID'];
	    $row_array['mapID'] = $row['mapID'];
	    $row_array['latlonX'] = $row['latlonX'];
	    $row_array['latlonY'] = $row['latlonY'];
	    $row_array['description'] = $row['description'];
	    $row_array['title'] = $row['title'];
	
	    array_push($return_arr,$row_array);
	}
}
else 
{
	$return_arr[] = 'No Patients yet';
}

$conn->close();
?>


<html xmlns="http://www.w3.org/1999/xhtml">
		  <head>
		    <title>dt-coghKeyed.jpg</title>
		    <meta http-equiv='imagetoolbar' content='no'/>
		    <style type="text/css"> v\:fill {behavior:url(#default#VML);}
		        html, body { overflow: hidden; padding: 0; height: 100%; width: 100%; font-family: 'Lucida Grande',Geneva,Arial,Verdana,sans-serif; }
		        body { margin: 10px; background: #fff; }
		        h1 { width: 80%; margin: 0; padding: 0px; border:0; font-size: 100%; }
			#smaller { font-size: 35%; float: right;}
		        #header { width: 80% ; height: 20%; padding: 0; background-color: #eee; border: 1px solid #888; position: fixed; top: 0; left: 0;}
		        #subheader { font-size: 8px; color: #555;}
		        #map { height: 80%; width: 80%; border: 1px solid #888; position: fixed; left: 0; bottom: 0;}
		        #report { height: 70%; width: 19%; border: 1px solid #888; position: fixed; bottom: 0; right: 0;}
			#layerswitcher { height: 20%; width: 19%; text-align: left; margin-left: 10px; position: fixed;
				  font-size: 10px; background-color: #eee; top: 0; right: 0;
				}
		    </style>
		    <script src="http://www.openlayers.org/api/2.11/OpenLayers.js" type="text/javascript"></script>
		    <script type="text/javascript">

		        OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
		            defaultHandlerOptions: {
		                'single': true,
		                'double': false,
		                'pixelTolerance': 0,
		                'stopSingle': false,
		                'stopDouble': false
		            },

		            initialize: function (options) {
		                this.handlerOptions = OpenLayers.Util.extend(
	                        {}, this.defaultHandlerOptions
	                    );
		                OpenLayers.Control.prototype.initialize.apply(
	                        this, arguments
	                    );
		                this.handler = new OpenLayers.Handler.Click(
	                        this, {
	                            'click': this.trigger
	                        }, this.handlerOptions
	                    );
		            },

		            trigger: function (e) {
		                var lonlat = map.getLonLatFromPixel(e.xy);
		                var lon = lonlat.lon;
		                var lat = lonlat.lat;
		                document.getElementById("coords").value = lon + "," + lat;
		            }

		        });


		        var map;
		        var mapBounds = new OpenLayers.Bounds(-180.0, -450.0, 180.0, 90.0);
		        var mapMinZoom = 0;
		        var mapMaxZoom = 5;
		        var toggle = true;

		        // avoid pink tiles
		        OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
		        OpenLayers.Util.onImageLoadErrorColor = "transparent";

		        function init() {
		            var options = {
		                controls: [],
		                maxExtent: new OpenLayers.Bounds(-180.0, -450.0, 180.0, 90.0),
		                maxResolution: 3.600000,
		                numZoomLevels: 6
		            };
		            map = new OpenLayers.Map('map', options);

		            map.addControl(new OpenLayers.Control.LayerSwitcher({'div':OpenLayers.Util.getElement('layerswitcher')}));

		            var layer1 = new OpenLayers.Layer.TMS("coghKeyed", "../dt-coghKeyed/",
                        {
                            url: '',
                            serviceVersion: '.',
                            layername: 'layer1',
                            alpha: true,
                            type: 'png',
                            getURL: overlay_getTileURL
                        });
		            map.addLayer(layer1);

		            var layer2 = new OpenLayers.Layer.TMS("coghUndercity", "../cogh-undercity/",
                                    {
                                        url: '',
                                        serviceVersion: '.',
                                        layername: 'layer2',
                                        type: 'png',
                                        getURL: overlay_getTileURL,
                                        alpha: true,
                                        isBaseLayer: false
                                    });

		            var layer3 = new OpenLayers.Layer.TMS("coghStreets", "../cogh-streets/",
                                {
                                    url: '',
                                    serviceVersion: '.',
                                    layername: 'layer3',
                                    type: 'png',
                                    getURL: overlay_getTileURL,
                                    alpha: true,
                                    isBaseLayer: false
                                });

		            map.addLayers([layer1, layer2, layer3]);

		            layer1.setVisibility(true);

		            layer2.setVisibility(toggle);

		            layer2.setOpacity(0.50);

		            layer3.setVisibility(toggle);

		            layer3.setOpacity(0.50);

		            var markers = new OpenLayers.Layer.Vector("Markers");
		            map.addLayer(markers);

		            var size = new OpenLayers.Size(21, 25);
		            var offset = new OpenLayers.Pixel(-(size.w / 2), -size.h);
		            var icon = new OpenLayers.Icon('marker.png', size, offset);

                    
		            var dyString =  <?php echo json_encode($return_arr, JSON_PRETTY_PRINT) ?>;
		            var tyString = "Hello World!";//JSON.stringify(dyString);
		            var myFeatures = new Array();
		            
		            for (var l = 0; l < Object.keys(dyString).length; l++){
		                if(dyString[l].mapID = 1){
		                    myFeatures.push(new OpenLayers.Feature.Vector(
                                    new OpenLayers.Geometry.Point(dyString[l].latlonX , dyString[l].latlonY),
                                    { description:  ''},  //JSON.stringify(dyString[l].description)
                                    { externalGraphic: 
                                            'marker.png',  //JSON.stringify(dyString[l].externalGraphic), 
                                        graphicHeight: 25, 
                                        graphicWidth: 21, 
                                        graphicXOffset: -12, 
                                        graphicYOffset: -25 
                                    }
                                  )
                                )
		                    myFeatures[l].attributes.description = JSON.stringify(dyString[l].description);
		                    myFeatures[l].attributes.title = JSON.stringify(dyString[l].title);
		                    markers.addFeatures(myFeatures[l]);  
		                }
		            };
        

		            document.getElementById('report').innerHTML += tyString;



		            selectControl = new OpenLayers.Control.SelectFeature(markers, {       //create control for markers to check for mouse 'hover'
		                hover: true,
		                highlightOnly: true,
		                eventListeners: {
		                    featurehighlighted: onMarkerSelect,
		                    featureunhighlighted: onMarkerUnselect
		                }
		            });

		            var markerControl = new OpenLayers.Control.SelectFeature(markers, {       //create control for markers to check for mouse 'click'
		                hover: false,
		                eventListeners: { boxselectionstart: report },
		            });


		            map.addControl(selectControl);                                         //add the control to map
		            selectControl.activate();                                               //make the control active
		            map.addControl(markerControl);
		            markerControl.activate();

		            map.zoomToExtent(mapBounds);

		            var click = new OpenLayers.Control.Click();
		            map.addControl(click);
		            click.activate();

		            map.addControl(new OpenLayers.Control.PanZoomBar());
		            map.addControl(new OpenLayers.Control.MousePosition());
		            map.addControl(new OpenLayers.Control.MouseDefaults());
		            map.addControl(new OpenLayers.Control.KeyboardDefaults());
		            map.addControl(new OpenLayers.Control.TouchNavigation({ dragPanOptions: { interval: 0, enableKinetic: true } }));
		            map.addControl(new OpenLayers.Control.PinchZoom());

		            map.setLayerIndex(layer2, 3);
		            map.setLayerIndex(layer3, 4);
		            map.setLayerIndex(markers, 99);
		        }


		        function myToggle() {//Checking if select field is disabled
		            if (document.getElementById("btn").value == "View") {//Changing the select field state to enabled and changing the value of button to disable
		                toggle = true;
		                document.getElementById("btn").value = "Hide";
		            }
		            //Checking if select field is enabled
		            if (document.getElementById("btn").value == "Hide") {//Changing the select field state to disabled and changing the value of button to enable
		                toggle = false;
		                document.getElementById("btn").value = "View";
		            }
		        }

		        function onMarkerSelect(evt) {
		            var myMarker = evt.feature
		            var myHello = evt.feature.attributes.description
		            var myTitle = evt.feature.attributes.title
		            myPopup = new OpenLayers.Popup("HoverPop",
                                                 myMarker.geometry.getBounds().getCenterLonLat(),
                                                 null,
                                                 "<div style='font-size:.8em'>" + myTitle + "</div>",  //
                                                 false,
                                 onMarkerUnselect);                        //the guts of the popup
		            myPopup.backgroundColor = '#99CC99';
		            myPopup.autoSize = true;
		            myMarker.popup = myPopup;                                                   //assign to marker
		            map.addPopup(myPopup);                                                  //add popup to map
		            // create a new div element 
		            //var newDiv = document.createElement("div"); 
		            // and give it some content 
		            //var newContent = document.createTextNode("Hi there and greetings!"); 
		            // add the text node to the newly created div
		            //newDiv.appendChild(newContent);  

		            // add the newly created element and its content into the DOM 
		            //var currentDiv = document.getElementById("div1"); 
		            //document.body.insertBefore(newDiv, currentDiv); 

		            document.getElementById("report").innerHTML = "<div style='font-size:.8em'; padding: 2px; border: 1px solid #888;>" + myHello + "</div>";          //update report
		        }

		        function onMarkerUnselect(evt) {						//  *** this fires when you move off a marker ***
		            var myMarker = evt.feature
		            map.removePopup(myMarker.popup);                                        //remove popup from map
		            myMarker.popup.destroy();                                                 //remove from memory
		        }


		        function report(evt) {
		            var myMarker = evt.feature
		            document.getElementById("report").value = "Registered!!";
		        }


		        function goToCoords() {
		            var myCoords = document.getElementById("coords").value;
		            if (myCoords.indexOf(",") != -1) {
		                var values = myCoords.split(",");
		                var lon = parseFloat(parseInt(values[0]));
		                var lat = parseFloat(parseInt(values[1]));
		                var goLonLat = new OpenLayers.LonLat(lon, lat);
		                map.zoomTo(5);
		                map.panTo(goLonLat);
		            }
		            else { alert("Please enter a valid longitude and latitude pair separated by a comma: ex. 120,240"); }
		        }


		        function overlay_getTileURL(bounds) {
		            var res = this.map.getResolution();
		            var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
		            var y = Math.round((bounds.bottom - this.maxExtent.bottom) / (res * this.tileSize.h));
		            var z = this.map.getZoom();
		            if (x >= 0 && y >= 0) {
		                return this.url + z + "/" + x + "/" + y + "." + this.type;
		            } else {
		                return "http://www.maptiler.org/img/none.png";
		            }
		        }

		        function getWindowHeight() {
		            if (self.innerHeight) return self.innerHeight;
		            if (document.documentElement && document.documentElement.clientHeight)
		                return document.documentElement.clientHeight;
		            if (document.body) return document.body.clientHeight;
		            return 0;
		        }

		        function getWindowWidth() {
		            if (self.innerWidth) return self.innerWidth;
		            if (document.documentElement && document.documentElement.clientWidth)
		                return document.documentElement.clientWidth;
		            if (document.body) return document.body.clientWidth;
		            return 0;
		        }

		        function resize() {
		            var map = document.getElementById("map");
		            if (map.updateSize) { map.updateSize(); };
		        }

		        onresize = function () { resize(); };

            </script>
		  </head>
		  <body onload="init()">
			<div id="header">
				<h1 style="padding: 10px; font-size: 200%">The World of Greyhawk      <small id="smaller">Based on the Free City of Greyhawk according to <a href="http://melkot.com/locations/cogh/cogh.html">Maldin</a></small></h1>
			<table border="1">
			<tr>
				<td>
				<textarea id="coords" rows="1", cols="15" onBlur="goToCoords()">Click the Map</textarea>
				</td>
				<td>
				<input id="btn" onclick="goToCoords()" value="View" type="button"> 
				</td>
			</tr>
			</table>
				
			</div>
			<div id="subheader">
				<ul><li> Generated by <a href="http://www.maptiler.org/">MapTiler</a>/<a href="http://www.klokan.cz/projects/gdal2tiles/">GDAL2Tiles</a>, Copyright &copy; 2008 <a href="http://www.klokan.cz/">Klokan Petr Pridal</a>,  <a href="http://www.gdal.org/">GDAL</a> &amp; <a href="http://www.osgeo.org/">OSGeo</a> <a href="http://code.google.com/soc/">GSoC</a>
				<!-- PLEASE, LET THIS NOTE ABOUT AUTHOR AND PROJECT SOMEWHERE ON YOUR WEBSITE, OR AT LEAST IN THE COMMENT IN HTML. THANK YOU -->
			</li></ul></div>
                    <div id="layerswitcher" class="olControlLayerSwitcher"></div>
		    <div id="report">
            <ul>      
   			 <li>THE LINKS BELOW ARE CURRENTLY UNDER CONSTRUCTOIN</li>
			 <li><a href="../SwordCoast/SwordCoast.html">Storm King's Thunder!!</a></li>
			 <li><a href="../dt-coghKeyed/dtCoghKeyed.php">City of Greyhawk (High Level)</a></li>
			 <li><a href="../DiamondLake/DiamondLake.html">Diamond Lake</a></li>
			 <li><a href="../FlanaessFullMap2015rev1-6Part/openlayers.html">Anna Meyer Flanaess Map</a></li>
             <li>I just want to say <?php echo $numrows; ?> </li>
    		</ul>
            </div>
		    <div id="map"></div>
		    <script type="text/javascript" >resize()</script>
		  </body>
		</html>			