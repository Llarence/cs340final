<?php
/* Group 34: Devin Macomb and Kai Turner */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

// --- FUNCTIONS ---
function readStations($link)
{
    $sql = "SELECT * FROM stations";
    $result = mysqli_query($link, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function prepareStationDeletion($link, $stationId)
{
    $routesSql = "SELECT rid FROM routes WHERE station1 = ? OR station2 = ?";
    $stmt = mysqli_prepare($link, $routesSql);
    mysqli_stmt_bind_param($stmt, 'ii', $stationId, $stationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $del = mysqli_prepare($link, "DELETE FROM tickets WHERE rid = ?");
        mysqli_stmt_bind_param($del, 'i', $row['rid']);
        mysqli_stmt_execute($del);
    }

    $deleteRoutesSql = "DELETE FROM routes WHERE station1 = ? OR station2 = ?";
    $stmt = mysqli_prepare($link, $deleteRoutesSql);
    mysqli_stmt_bind_param($stmt, 'ii', $stationId, $stationId);
    mysqli_stmt_execute($stmt);
}

function deleteOrphanStations($link)
{
    $sql = "DELETE FROM stations 
            WHERE sid NOT IN (
                SELECT station1 FROM routes
                UNION
                SELECT station2 FROM routes
            )";
    return mysqli_query($link, $sql);
}

function addStation($link, $name, $latitude, $longitude)
{
    $sql = "INSERT INTO stations (name, latitude, longitude) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'sdd', $name, $latitude, $longitude);
    return mysqli_stmt_execute($stmt);
}


// --- FORM HANDLING ---
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['prepare_station'])) {
        prepareStationDeletion($link, $_POST['sid']);
        $message = "Station prepared for deletion.";
    } elseif (isset($_POST['delete_orphan_stations'])) {
        deleteOrphanStations($link);
        $message = "Orphan stations deleted.";
    } elseif (isset($_POST['add_station'])) {
        addStation($link, $_POST['name'], $_POST['latitude'], $_POST['longitude']);
        $message = "Station added successfully.";
    }
}


$stations = readStations($link);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Stations</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Station Management</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="form-inline">
        <label>Station ID to prepare:</label>
        <input type="number" name="sid" required class="form-control">
        <button class="btn btn-warning" name="prepare_station">Prepare Station for Deletion</button>
    </form>
    <br>
    <form method="post">
        <button class="btn btn-danger" name="delete_orphan_stations">Delete Orphan Stations</button>
    </form>

    <h4>Add New Station</h4>
    <form method="post" class="form-inline">
        <input type="text" name="name" class="form-control" placeholder="Station Name" required>
        <input type="number" step="any" name="latitude" class="form-control" placeholder="Latitude">
        <input type="number" step="any" name="longitude" class="form-control" placeholder="Longitude">
        </form>
    <br>


    <a href="index.php" class="btn btn-default" style="margin-top: 10px;">
    ← Back to Main Page
    </a>

    <h3>Current Stations</h3>
    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Lat</th><th>Lon</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stations as $s): ?>
                <tr>
                    <td><?= $s['sid'] ?></td>
                    <td><?= $s['name'] ?></td>
                    <td><?= $s['latitude'] ?></td>
                    <td><?= $s['longitude'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
