
    <title>Waste Management Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="side.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<body style="background-color:white">
<header>
    <?php include 'header.php'; ?>
   
</header>

<div class="index">

<?php include 'side.php'; ?>

<div class="container">
<h1>Waste Management Dashboard</h1>

    <main>
        <div class="dashboard">
            <div class="card">
                <h2>Solid Waste</h2>
                <canvas id="solidWasteChart"></canvas>
            </div>
            <div class="card">
                <h2>Organic Waste</h2>
                <canvas id="organicWasteChart"></canvas>
            </div>
            <div class="card">
                <h2>Hazardous Waste</h2>
                <canvas id="hazardousWasteChart"></canvas>
            </div>
        </div>
    </main>
</div>
</div>
<script src="script.js"></script>

