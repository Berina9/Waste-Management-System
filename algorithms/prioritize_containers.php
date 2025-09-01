<?php
$conn = new mysqli("localhost", "root", "", "waste");
if ($conn->connect_error) die("DB error: " . $conn->connect_error);

$q = "SELECT Id, Area, Locality, Landmark,
             COALESCE(wastequantity, 0) AS wastequantity,
             COALESCE(Totalcapacity, 0) AS Totalcapacity
      FROM tbl_container";
$res = $conn->query($q);
if (!$res) die("Query error: " . $conn->error);

$data = [];
while ($r = $res->fetch_assoc()) {
    $cap = (float)$r['Totalcapacity'];
    $wq  = (float)$r['wastequantity'];
    $ratio = ($cap > 0) ? ($wq / $cap) : 0.0; // 0..1
    $priority = ($ratio >= 0.8) ? 'HIGH' : (($ratio >= 0.5) ? 'MEDIUM' : 'LOW');

    $r['fill_ratio'] = round($ratio * 100, 2);
    $r['priority'] = $priority;
    $data[] = $r;
}

// sort by ratio desc
usort($data, function($a, $b) { return $b['fill_ratio'] <=> $a['fill_ratio']; });
$conn->close();

// simple HTML table
echo "<link rel='stylesheet' href='../Admin/fetchcont.css'>";
echo "<h2>Container Pickup Priority</h2>";
echo "<table border='1'><tr>
        <th>Id</th><th>Area</th><th>Locality</th><th>Landmark</th>
        <th>Waste Qty</th><th>Total Cap</th><th>Fill %</th><th>Priority</th>
      </tr>";
foreach ($data as $r) {
    echo "<tr>
            <td>".htmlspecialchars($r['Id'])."</td>
            <td>".htmlspecialchars($r['Area'])."</td>
            <td>".htmlspecialchars($r['Locality'])."</td>
            <td>".htmlspecialchars($r['Landmark'])."</td>
            <td>".htmlspecialchars($r['wastequantity'])."</td>
            <td>".htmlspecialchars($r['Totalcapacity'])."</td>
            <td>".$r['fill_ratio']."</td>
            <td>".$r['priority']."</td>
          </tr>";
}
echo "</table>";