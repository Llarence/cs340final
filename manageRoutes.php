<?php
/* Group 34: Devin Macomb and Kai Turner */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

// --- FUNCTIONS ---
function readRoutes($link) {
    $sql = "SELECT r.rid, s1.name AS station1, s2.name AS station2
            FROM routes r
            JOIN stations s1 ON r.station1 = s1.sid
            JOIN stations s2 ON r.station2 = s2.sid";
    $result = mysqli_query($link, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function addRoute($link, $station1, $station2) {
    $sql = "INSERT INTO routes (station1, station2) VALUES (?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $station1, $station2);
    return mysqli_stmt_execute($stmt);
}

function prepareRouteDeletion($link, $routeId) {
    $sql = "DELETE FROM tickets WHERE rid = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $routeId);
    return mysqli_stmt_execute($stmt);
}

function deleteOrphanRoutes($link) {
    // Find orphan routes (those not used in tickets)
    $sql = "SELECT rid FROM routes 
            WHERE rid NOT IN (SELECT DISTINCT rid FROM tickets)";
    $result = mysqli_query($link, $sql);
    if (!$result) return false;

    while ($row = mysqli_fetch_assoc($result)) {
        $rid = $row['rid'];

        // Delete from trains_and_routes (or any other referencing tables)
        $deleteAssoc = mysqli_prepare($link, "DELETE FROM trains_and_routes WHERE rid = ?");
        mysqli_stmt_bind_param($deleteAssoc, 'i', $rid);
        mysqli_stmt_execute($deleteAssoc);

        // Delete from routes
        $deleteRoute = mysqli_prepare($link, "DELETE FROM routes WHERE rid = ?");
        mysqli_stmt_bind_param($deleteRoute, 'i', $rid);
        mysqli_stmt_execute($deleteRoute);
    }

    return true;
}


// --- FORM HANDLING ---
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add_route'])) {
        addRoute($link, $_POST['station1'], $_POST['station2']);
        $message = "Route added.";
    } elseif (isset($_POST['prepare_route'])) {
        prepareRouteDeletion($link, $_POST['rid']);
        $message = "Route prepared for deletion.";
    } elseif (isset($_POST['delete_orphan_routes'])) {
        deleteOrphanRoutes($link);
        $message = "Orphan routes deleted.";
    }
}

$routes = readRoutes($link);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Routes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Route Management</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="form-inline">
        <label>From Station ID:</label>
        <input type="number" name="station1" class="form-control" required>
        <label>To Station ID:</label>
        <input type="number" name="station2" class="form-control" required>
        <button class="btn btn-success" name="add_route">Add Route</button>
    </form>
    <br>
    <form method="post" class="form-inline">
        <label>Route ID to prepare:</label>
        <input type="number" name="rid" class="form-control" required>
        <button class="btn btn-warning" name="prepare_route">Prepare Route for Deletion</button>
    </form>
    <br>
    <form method="post">
        <button class="btn btn-danger" name="delete_orphan_routes">Delete Orphan Routes</button>
    </form>

    <a href="index.php" class="btn btn-default" style="margin-top: 10px;">
    ← Back to Main Page
    </a>


    <h3>Current Routes</h3>
    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>From</th><th>To</th></tr>
        </thead>
        <tbody>
            <?php foreach ($routes as $r): ?>
                <tr>
                    <td><?= $r['rid'] ?></td>
                    <td><?= $r['station1'] ?></td>
                    <td><?= $r['station2'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
