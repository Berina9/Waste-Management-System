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

    var graph = {}; // Initialize with your graph data if available
    var coordinates = {};

    // Fetch coordinates from PHP script
    function fetchCoordinates() {
        fetch('getPlusCodes.php') // Adjust the path as needed
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error fetching coordinates:', data.error);
                    return;
                }

                // Debug log to check fetched data
                console.log('Fetched Data:', data);

                // Process container coordinates
                data.containerCoordinates.forEach((container, index) => {
                    if (container && container.location) {
                        var location = container.location.trim(); // Remove any extraneous whitespace or newline
                        if (location) {
                            var [lat, lng] = location.split(',').map(Number);
                            if (isValidCoordinate(lat, lng)) {
                                coordinates['container' + index] = [lat, lng];
                                addMarker(lat, lng, 'container'); // Using container coordinates for markers
                            } else {
                                console.error('Invalid container coordinate:', location);
                            }
                        } else {
                            console.error('Empty location field for container:', container);
                        }
                    } else {
                        console.error('Invalid container coordinate data:', container);
                    }
                });

                // Process admin coordinates
                data.adminCoordinates.forEach((coord, index) => {
                    if (coord) {
                        var location = coord.trim(); // Remove any extraneous whitespace or newline
                        if (location) {
                            var [lat, lng] = location.split(',').map(Number);
                            if (isValidCoordinate(lat, lng)) {
                                coordinates['admin' + index] = [lat, lng];
                                addCircleMarker(lat, lng, 'admin'); // Using admin coordinates for circles
                            } else {
                                console.error('Invalid admin coordinate:', location);
                            }
                        } else {
                            console.error('Empty location field for admin coordinate:', coord);
                        }
                    }
                });

                // Process driver coordinates
                data.driverCoordinates.forEach((coord, index) => {
                    if (coord) {
                        var location = coord.trim(); // Remove any extraneous whitespace or newline
                        if (location) {
                            var [lat, lng] = location.split(',').map(Number);
                            if (isValidCoordinate(lat, lng)) {
                                coordinates['driver' + index] = [lat, lng];
                                addMarker(lat, lng, 'driver'); // Using driver coordinates for green markers
                            } else {
                                console.error('Invalid driver coordinate:', location);
                            }
                        } else {
                            console.error('Empty location field for driver coordinate:', coord);
                        }
                    }
                });

                data.drivercomCoordinates.forEach((coord, index) => {
                    if (coord) {
                        var location = coord.trim(); // Remove any extraneous whitespace or newline
                        if (location) {
                            var [lat, lng] = location.split(',').map(Number);
                            if (isValidCoordinate(lat, lng)) {
                                coordinates['driver' + index] = [lat, lng];
                                addMarker(lat, lng, 'drivercom'); // Using driver coordinates for green markers
                            } else {
                                console.error('Invalid driver coordinate:', location);
                            }
                        } else {
                            console.error('Empty location field for driver coordinate:', coord);
                        }
                    }
                });

                // Example graph data
                graph = {
                    'container0': { 'node1': 10, 'node2': 15 },
                    'node1': { 'container0': 10, 'node2': 12, 'node3': 5 },
                    'node2': { 'container0': 15, 'node1': 12, 'node3': 8 },
                    'node3': { 'node1': 5, 'node2': 8, 'driver0': 10 },
                    'driver0': { 'node3': 10 }
                };

                // Find shortest path from a container to a driver
                var start = 'container0'; // Update this based on actual data
                var end = 'driver0'; // Update this based on actual data
                var shortestPath = dijkstra(graph, start, end);
                plotShortestPath(shortestPath);
            })
            .catch(error => console.error('Error fetching coordinates:', error));
    }

    // Function to add a basic marker
    function addMarker(lat, lng, type) {
        if (!isValidCoordinate(lat, lng)) return; // Ensure valid coordinates before adding a marker
        
        var latlng = [lat, lng];
        var marker;

        if (type === 'driver') {
            marker = L.marker(latlng, { icon: L.divIcon({ className: 'custom-marker', html: '<div style="background-color: green; width: 12px; height: 12px; border-radius: 50%;"></div>' }) }).addTo(map);
        } 
        else if(type === 'drivercom') {
            marker = L.marker(latlng, { icon: L.divIcon({ className: 'custom-marker', html: '<div style="background-color: red; width: 12px; height: 12px; border-radius: 50%;"></div>' }) }).addTo(map);
        }
        else{
            marker = L.marker(latlng).addTo(map);

        }

        markers.push({ marker: marker, type: type });
    }

    // Function to add a circle marker
    function addCircleMarker(lat, lng, type) {
        if (!isValidCoordinate(lat, lng)) return; // Ensure valid coordinates before adding a circle marker
        
        var latlng = [lat, lng];
        var color = 'blue'; // You can define different colors for different types if needed
        var marker = L.circleMarker(latlng, { color: color, radius: 8 }).addTo(map);

        markers.push({ marker: marker, type: type });
    }

    // Function to plot the shortest path on the map
    function plotShortestPath(path) {
        var latlngs = path.map(node => coordinates[node]);

        // Check if all latlngs are defined
        if (latlngs.some(coord => coord === undefined)) {
            console.error('One or more coordinates are undefined:', latlngs);
            return;
        }

        if (line) {
            map.removeLayer(line);
        }
        line = L.polyline(latlngs, { color: 'blue' }).addTo(map);

        // Optionally, zoom the map to fit the path
        map.fitBounds(line.getBounds());
    }

    // Function to calculate shortest path using Dijkstra's algorithm
    function dijkstra(graph, start, end) {
        let distances = {};
        let previous = {};
        let nodes = new PriorityQueue();

        for (let node in graph) {
            if (node === start) {
                distances[node] = 0;
                nodes.enqueue(node, 0);
            } else {
                distances[node] = Infinity;
                nodes.enqueue(node, Infinity);
            }
            previous[node] = null;
        }

        while (!nodes.isEmpty()) {
            let smallest = nodes.dequeue();
            if (smallest === end) {
                let path = [];
                while (previous[smallest]) {
                    path.push(smallest);
                    smallest = previous[smallest];
                }
                path.push(start);
                return path.reverse();
            }

            if (distances[smallest] === Infinity) {
                break;
            }

            for (let neighbor in graph[smallest]) {
                let alt = distances[smallest] + graph[smallest][neighbor];
                if (alt < distances[neighbor]) {
                    distances[neighbor] = alt;
                    previous[neighbor] = smallest;
                    nodes.enqueue(neighbor, alt);
                }
            }
        }
        return [];
    }

    // Priority Queue implementation
    /*class PriorityQueue {
        constructor() {
            this.queue = [];
        }

        enqueue(element, priority) {
            this.queue.push({ element, priority });
            this.queue.sort((a, b) => a.priority - b.priority);
        }

        dequeue() {
            return this.queue.shift().element;
        }

        isEmpty() {
            return this.queue.length === 0;
        }
    }*/

    // Validate if coordinates are valid
    function isValidCoordinate(lat, lng) {
        return !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0;
    }

    // Clear all markers and lines from the map
    document.getElementById('clearButton').addEventListener('click', () => {
        markers.forEach(markerObj => map.removeLayer(markerObj.marker));
        markers = [];
        if (line) {
            map.removeLayer(line);
            line = null;
        }
    });

    // Initialize fetching coordinates
    fetchCoordinates();
</script>

</body>
</html>
