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

    // Graph data and coordinates
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

                // Process admin coordinates
                data.adminCoordinates.forEach((coord, index) => {
                    var [lat, lng] = coord.split(',').map(Number);
                    coordinates['admin' + index] = [lat, lng];
                    addMarker(lat, lng, 'admin');
                });

                // Process driver coordinates
                data.driverCoordinates.forEach((coord, index) => {
                    var [lat, lng] = coord.split(',').map(Number);
                    coordinates['driver' + index] = [lat, lng];
                    addMarker(lat, lng, 'driver');
                });

                // Example graph data (you will need actual data from your DB)
                graph = {
                    'admin0': { 'node1': 10, 'node2': 15 },
                    'node1': { 'admin0': 10, 'node2': 12, 'node3': 5 },
                    'node2': { 'admin0': 15, 'node1': 12, 'node3': 8 },
                    'node3': { 'node1': 5, 'node2': 8, 'driver0': 10 },
                    'driver0': { 'node3': 10 }
                };

                // Find shortest path from an admin to a driver
                var start = 'admin0'; // Update this based on actual data
                var end = 'driver0'; // Update this based on actual data
                var shortestPath = dijkstra(graph, start, end);
                plotShortestPath(shortestPath);
            })
            .catch(error => console.error('Error fetching coordinates:', error));
    }

    // Function to add a marker using coordinates
    function addMarker(lat, lng, type) {
        var latlng = [lat, lng];
        var marker = L.marker(latlng).addTo(map);
        markers.push({ marker: marker, type: type });
    }

    // Function to plot the shortest path on the map
    function plotShortestPath(path) {
        var latlngs = path.map(node => coordinates[node]);

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
                    nodes.enqueue(neighbor, distances[neighbor]);
                }
            }
        }

        return [];
    }

    // Priority Queue implementation
    class PriorityQueue {
        constructor() {
            this.collection = [];
        }

        enqueue(element, priority) {
            this.collection.push({ element, priority });
            this.collection.sort((a, b) => a.priority - b.priority);
        }

        dequeue() {
            return this.collection.shift().element;
        }

        isEmpty() {
            return this.collection.length === 0;
        }
    }

    // Clear markers and reset the map
    function clearMarkers() {
        markers.forEach(function(item) {
            map.removeLayer(item.marker);
        });
        markers = [];

        if (line) {
            map.removeLayer(line);
        }

        document.getElementById('distance').innerText = '';
    }

    // Attach clear function to the Clear button
    document.getElementById('clearButton').addEventListener('click', clearMarkers);

    // Fetch coordinates when the page loads
    fetchCoordinates();
</script>

</body>
</html>
