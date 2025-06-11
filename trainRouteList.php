<?php
/* Group 34: Devin Macomb and Kai Turner */
require_once "config.php";

// Fetch all trains with their routes
$sql = "
    SELECT 
        t.trid AS train_id,
        r.rid AS route_id,
        s1.name AS from_station,
        s2.name AS to_station
    FROM trains t
    JOIN trains_and_routes tr ON t.trid = tr.trid
    JOIN routes r ON tr.rid = r.rid
    JOIN stations s1 ON r.station1 = s1.sid
    JOIN stations s2 ON r.station2 = s2.sid
    ORDER BY t.trid, r.rid
";

$result = mysqli_query($link, $sql);

$trainRoutes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $trainRoutes[$row['train_id']][] = [
        'route_id' => $row['route_id'],
        'from' => $row['from_station'],
        'to' => $row['to_station']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Train Routes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<body>
<div class="container">
    <h2>Routes by Train</h2>
    <?php if (empty($trainRoutes)): ?>
        <p>No route assignments found.</p>
    <?php else: ?>
        <?php foreach ($trainRoutes as $trid => $routes): ?>
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Train ID: <?= $trid ?></strong></div>
                <div class="panel-body">
                    <ul>
                        <?php foreach ($routes as $route): ?>
                            <li>
                                Route <?= $route['route_id'] ?>:
                                <?= htmlspecialchars($route['from']) ?> → <?= htmlspecialchars($route['to']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <a href="index.php" class="btn btn-default">Back to Main</a>
</div>
</body>
</html>
