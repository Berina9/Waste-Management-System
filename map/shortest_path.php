<?php
header('Content-Type: application/json');

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

// Example graph (You can replace this with your actual graph)
$graph = [
    'Kathmandu' => ['Point1' => 1, 'Point2' => 4],
    'Point1' => ['Kathmandu' => 1, 'Point2' => 2, 'Point3' => 5],
    'Point2' => ['Kathmandu' => 4, 'Point1' => 2, 'Pokhara' => 3],
    'Point3' => ['Point1' => 5, 'Pokhara' => 1],
    'Pokhara' => ['Point2' => 3, 'Point3' => 1],
];

// Fetch start and end from the request
$start = $_GET['start'] ?? 'Kathmandu';
$end = $_GET['end'] ?? 'Pokhara';

$result = shortestPath($graph, $start, $end);

// Output the result as JSON
echo json_encode($result);
?>
