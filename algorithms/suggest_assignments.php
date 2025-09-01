<?php
$conn = new mysqli("localhost", "root", "", "waste");
if ($conn->connect_error) die("DB error: " . $conn->connect_error);

// load pending containers
$cq = "SELECT Id, Area, Locality, Landmark FROM tbl_container";
$containers = $conn->query($cq);
if (!$containers) die("Query error: " . $conn->error);

// load drivers
$dq = "SELECT Id, COALESCE(username,'') AS username, COALESCE(address,'') AS address FROM tbl_driver";
$driversRes = $conn->query($dq);
$drivers = [];
while ($d = $driversRes->fetch_assoc()) {
    $drivers[] = $d;
}

// precompute driver workload (all-time + per-area)
$workAll = [];        // Driverid => total count
$workByArea = [];     // Driverid => [Area => count]
$wa = $conn->query("SELECT Driverid, Area, COUNT(*) AS cnt FROM tbl_container_archive GROUP BY Driverid, Area");
while ($w = $wa->fetch_assoc()) {
    $did = (int)$w['Driverid'];
    $area = (string)$w['Area'];
    $cnt = (int)$w['cnt'];
    if (!isset($workAll[$did])) $workAll[$did] = 0;
    $workAll[$did] += $cnt;
    if (!isset($workByArea[$did])) $workByArea[$did] = [];
    $workByArea[$did][$area] = $cnt;
}

// helper: pick least busy from candidates by area; fallback to global
function pickDriver(array $candidates, string $area, array $workByArea, array $workAll): ?array {
    if (empty($candidates)) return null;

    // first by area workload
    usort($candidates, function($a, $b) use ($area, $workByArea, $workAll) {
        $wa = $workByArea[$a['Id']][$area] ?? 0;
        $wb = $workByArea[$b['Id']][$area] ?? 0;
        if ($wa === $wb) {
            $ta = $workAll[$a['Id']] ?? 0;
            $tb = $workAll[$b['Id']] ?? 0;
            return $ta <=> $tb;
        }
        return $wa <=> $wb;
    });
    return $candidates[0];
}

echo "<link rel='stylesheet' href='../Admin/fetchcont.css'>";
echo "<h2>Suggested Driver Assignments (by Area, least workload first)</h2>";
echo "<table border='1'><tr>
        <th>Container Id</th><th>Area</th><th>Locality</th><th>Landmark</th>
        <th>Suggested Driver</th><th>Driver Address</th><th>Driver Past Jobs (Area / Total)</th>
      </tr>";

while ($c = $containers->fetch_assoc()) {
    $area = (string)$c['Area'];

    // drivers whose address mentions the Area (simple text match)
    $candidates = array_values(array_filter($drivers, function($d) use ($area) {
        $addr = mb_strtolower($d['address']);
        $ar   = mb_strtolower($area);
        return $ar !== '' && $addr !== '' && str_contains($addr, $ar);
    }));

    // if none match by area, consider all drivers
    if (empty($candidates)) $candidates = $drivers;

    $chosen = pickDriver($candidates, $area, $workByArea, $workAll);

    $did = $chosen['Id'] ?? null;
    $dname = $chosen['username'] ?? '-';
    $daddr = $chosen['address'] ?? '-';
    $areaCnt = ($did !== null) ? ($workByArea[$did][$area] ?? 0) : 0;
    $totCnt  = ($did !== null) ? ($workAll[$did] ?? 0) : 0;

    echo "<tr>
            <td>".htmlspecialchars($c['Id'])."</td>
            <td>".htmlspecialchars($c['Area'])."</td>
            <td>".htmlspecialchars($c['Locality'])."</td>
            <td>".htmlspecialchars($c['Landmark'])."</td>
            <td>".htmlspecialchars($dname)."</td>
            <td>".htmlspecialchars($daddr)."</td>
            <td>".(int)$areaCnt." / ".(int)$totCnt."</td>
          </tr>";
}
echo "</table>";

$conn->close();