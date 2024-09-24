<script>
function dijkstra(graph, start, end) {
    let distances = {};
    let previous = {};
    let nodes = new PriorityQueue();
    
    // Initialize distances and nodes
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
    
    return []; // No path found
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
