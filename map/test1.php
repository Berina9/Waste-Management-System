 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Road Map Example</title>
    Leaflet CSS 
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        /* Set the size of the map */
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
</head>
<body>

<h2>Leaflet.js Road Map Example</h2>
<div id="map"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-geometryutil"></script>
<script>
    // Initialize the map and set its view to a geographical location and zoom level
    var map = L.map('map').setView([27.7, 85.3], 7);  // Kathmandu, Nepal

    // Add a tile layer to the map (using OpenStreetMap tiles)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Example GeoJSON data with detailed road path (hypothetical)
    var geojsonFeature = {
        "type": "FeatureCollection",
        "features": [
            {
                "type": "Feature",
                "geometry": {
                    "type": "LineString",
                    "coordinates": [
                        [85.3240, 27.7172], // Starting point: Kathmandu
                        [85.3140, 27.7072],
                        [85.3050, 27.7052],
                        [85.2950, 27.7022],
                        [85.2850, 27.7052],
                        [85.2750, 27.7072],
                        [85.2650, 27.7102],
                        [85.2550, 27.7152],
                        [85.2450, 27.7202],
                        [85.2350, 27.7252],
                        [85.2250, 27.7302],
                        [83.9856, 28.2096] // End point: Pokhara
                    ]
                },
                "properties": {
                    "name": "Road from Kathmandu to Pokhara",
                    "popupContent": "This is the actual road path from Kathmandu to Pokhara."
                }
            }
        ]
    };

    // Add GeoJSON layer to the map and calculate the length of the LineString
    L.geoJSON(geojsonFeature, {
        onEachFeature: function (feature, layer) {
            if (feature.geometry.type === 'LineString') {
                var latlngs = feature.geometry.coordinates.map(function(coord) {
                    return L.latLng(coord[1], coord[0]);  // Reverse the order to [lat, lng]
                });

                // Calculate the length of the LineString in meters
                var length = L.GeometryUtil.length(latlngs);
                
                // Convert to kilometers
                var lengthInKm = (length / 1000).toFixed(2);

                // Add length information to the popup content
                layer.bindPopup(feature.properties.popupContent + '<br>Length: ' + lengthInKm + ' km');
            } else if (feature.properties && feature.properties.popupContent) {
                layer.bindPopup(feature.properties.popupContent);
            }
        }
    }).addTo(map);
</script>

</body>
</html>
