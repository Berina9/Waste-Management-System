<?php
date_default_timezone_set('UTC');

$conn = new mysqli("localhost", "root", "", "waste");
if ($conn->connect_error) die("DB error: " . $conn->connect_error);

$dryRun = isset($_GET['dry_run']);

$sql = "SELECT bin_id, capacity_liters, avg_generation_rate, holding_cost_per_unit, trip_cost,
               current_fill_percent, last_calculated_date, last_collection_date
        FROM bin_data";
$res = $conn->query($sql);
if (!$res) die("Query error: " . $conn->error);

$upd = null;
if (!$dryRun) {
    $upd = $conn->prepare(
        "UPDATE bin_data
         SET current_fill_percent = ?, optimal_collection_interval = ?, last_calculated_date = CURDATE()
         WHERE bin_id = ?"
    );
    if (!$upd) die("Prepare error: " . $conn->error);
}

function parseSqlDate(?string $s): ?DateTimeImmutable {
    if (!$s) return null;
    $s = substr($s, 0, 10);
    if ($s === '0000-00-00') return null;
    $dt = DateTimeImmutable::createFromFormat('Y-m-d', $s);
    return $dt ?: null;
}

$today = new DateTimeImmutable('today');
$rows = 0;

// simple styles from admin table
echo "<link rel='stylesheet' href='../Admin/fetchcont.css'>";
echo "<h2>Bin fill update ".($dryRun ? '(dry run – nothing updated)' : '')."</h2>";

echo "<table border='1'><tr>
        <th>Bin</th>
        <th>Cap (L)</th>
        <th>Avg gen (L/day)</th>
        <th>Hold cost</th>
        <th>Trip cost</th>
        <th>Baseline</th>
        <th>Days</th>
        <th>Old %</th>
        <th>New %</th>
        <th>Opt interval (days)</th>
        <th>Status</th>
      </tr>";

while ($r = $res->fetch_assoc()) {
    $bin  = (int)$r['bin_id'];
    $cap  = max(0.0, (float)$r['capacity_liters']);
    $rate = max(0.0, (float)$r['avg_generation_rate']);         // liters/day
    $h    = max(0.0, (float)$r['holding_cost_per_unit']);       // cost per liter-day
    $C    = max(0.0, (float)$r['trip_cost']);                   // cost per trip
    $pct  = min(100.0, max(0.0, (float)$r['current_fill_percent']));

    // pick baseline date
    $baselineDate = parseSqlDate($r['last_calculated_date']) ?: parseSqlDate($r['last_collection_date']);
    $days = 0.0;
    $baselineStr = '-';
    if ($baselineDate) {
        $baselineStr = $baselineDate->format('Y-m-d');
        $days = max(0.0, (float)$today->diff($baselineDate)->format('%a'));
    }

    // liters -> project -> cap at capacity
    $currentLiters    = ($pct / 100.0) * $cap;
    $projectedLiters  = min($currentLiters + ($rate * $days), $cap);
    $newPct           = $cap > 0 ? round(($projectedLiters / $cap) * 100.0, 2) : 0.0;

    // EOQ-style interval
    $optDays = 0.0;
    if ($rate > 0.0 && $h > 0.0) {
        $optDays = round(sqrt((2.0 * $C) / ($h * $rate)), 2);
    }

    $status = 'preview';
    if (!$dryRun) {
        $upd->bind_param("ddi", $newPct, $optDays, $bin);
        $upd->execute();
        $status = $upd->affected_rows >= 0 ? 'updated' : 'skipped';
    }

    echo "<tr>
            <td>".htmlspecialchars((string)$bin)."</td>
            <td>".htmlspecialchars((string)$cap)."</td>
            <td>".htmlspecialchars((string)$rate)."</td>
            <td>".htmlspecialchars((string)$h)."</td>
            <td>".htmlspecialchars((string)$C)."</td>
            <td>".htmlspecialchars($baselineStr)."</td>
            <td>".htmlspecialchars((string)$days)."</td>
            <td>".htmlspecialchars((string)$pct)."</td>
            <td>".htmlspecialchars((string)$newPct)."</td>
            <td>".htmlspecialchars((string)$optDays)."</td>
            <td>".htmlspecialchars($status)."</td>
          </tr>";
    $rows++;
}

echo "</table>";

if ($upd) $upd->close();
$conn->close();

echo "<p>Processed $rows bin(s).</p>";