<?php
function dijkstra($graph, $start) {
    $distances = [];
    $previous = [];
    $queue = [];

    foreach ($graph as $vertex => $neighbors) {
        if ($vertex === $start) {
            $distances[$vertex] = 0;
        } else {
            $distances[$vertex] = INF;
        }
        $previous[$vertex] = null;
        $queue[$vertex] = $distances[$vertex];
    }

    while (!empty($queue)) {
        $minVertex = array_search(min($queue), $queue);
        unset($queue[$minVertex]);

        if ($distances[$minVertex] === INF) {
            break;
        }

        foreach ($graph[$minVertex] as $neighbor => $cost) {
            $alt = $distances[$minVertex] + $cost;
            if ($alt < $distances[$neighbor]) {
                $distances[$neighbor] = $alt;
                $previous[$neighbor] = $minVertex;
                $queue[$neighbor] = $alt;
            }
        }
    }

    return ['distances' => $distances, 'previous' => $previous];
}

function shortestPath($graph, $start, $end) {
    $dijkstraResult = dijkstra($graph, $start);
    $distances = $dijkstraResult['distances'];
    $previous = $dijkstraResult['previous'];

    $path = [];
    $current = $end;

    while ($current !== null) {
        array_unshift($path, $current);
        $current = $previous[$current];
    }

    if ($path[0] === $start) {
        return ['distance' => $distances[$end], 'path' => $path];
    } else {
        return ['distance' => INF, 'path' => []];
    }
}

// Example graph represented as an adjacency list
$graph = [
    'A' => ['B' => 1, 'C' => 4],
    'B' => ['A' => 1, 'C' => 2, 'D' => 5],
    'C' => ['A' => 4, 'B' => 2, 'D' => 1],
    'D' => ['B' => 5, 'C' => 1],
];

 
$start = 'A';
$end = 'D';
$result = shortestPath($graph, $start, $end);

echo "Shortest distance from $start to $end is: " . $result['distance'] . "\n";
echo "Path: " . implode(' -> ', $result['path']) . "\n";
?>
