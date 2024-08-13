<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Road Map Example</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
        #controls {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Leaflet.js Road Map Example</h2>
<div id="map"></div>
<div id="controls">
    <p>Click on two points on the map to calculate the distance between them.</p>
    <p id="distance"></p>
    <button id="clearButton">Clear Markers</button>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-geometryutil"></script>
<script>
    var map = L.map('map').setView([27.7, 85.3], 7);
    var markers = [];
    var line;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Click event to place markers on the map
    map.on('click', function(e) {
        if (markers.length < 4) {
            console.log(markers)
            var marker = L.marker(e.latlng).addTo(map);
            markers.push(marker);

            // When two points are selected
            if (markers.length === 2) {
                calculateDistance();
            }
        }
    });

    // Function to calculate distance between two points
    function calculateDistance() {
        var latlngs = markers.map(function(marker) {
            return marker.getLatLng();
        });

        // Draw a line between the two points
        if (line) {
            map.removeLayer(line);
        }
        line = L.polyline(latlngs, { color: 'red' }).addTo(map);

        // Calculate the distance between the points
        var distance = latlngs[0].distanceTo(latlngs[1]); // Distance in meters
        var distanceInKm = (distance / 1000).toFixed(2);

        document.getElementById('distance').innerText = `Distance: ${distanceInKm} km`;
    }

    // Clear markers and reset the map
    function clearMarkers() {
        markers.forEach(function(marker) {
            map.removeLayer(marker);
        });
        markers = [];

        if (line) {
            map.removeLayer(line);
        }

        document.getElementById('distance').innerText = '';
    }

    // Attach clear function to the Clear button
    document.getElementById('clearButton').addEventListener('click', clearMarkers);
</script>

</body>
</html>
