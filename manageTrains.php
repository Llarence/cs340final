<?php
/* Group 34: Devin Macomb and Kai Turner */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

// --- FUNCTIONS ---
function readTrains($link)
{
    $sql = "SELECT * FROM trains";
    $result = mysqli_query($link, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function prepareTrainDeletion($link, $trainId)
{
    $sql = "DELETE FROM tickets WHERE trid = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $trainId);
    return mysqli_stmt_execute($stmt);
}

function addTrain($link, $type, $name, $cargoCars, $passengerCars)
{
    $sql = "INSERT INTO trains (type, name, cargo_cars, passenger_cars) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'ssii', $type, $name, $cargoCars, $passengerCars);
    return mysqli_stmt_execute($stmt);
}

function deleteOrphanTrains($link)
{
    // Step 1: Find orphan trains (trains not in tickets)
    $sql = "SELECT trid FROM trains 
            WHERE trid NOT IN (SELECT DISTINCT trid FROM tickets)";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        return false;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $trid = $row['trid'];

        // Step 2: Remove related entries from trains_and_routes
        $delAssoc = mysqli_prepare($link, "DELETE FROM trains_and_routes WHERE trid = ?");
        mysqli_stmt_bind_param($delAssoc, 'i', $trid);
        mysqli_stmt_execute($delAssoc);

        // Step 3: Now delete the train
        $delTrain = mysqli_prepare($link, "DELETE FROM trains WHERE trid = ?");
        mysqli_stmt_bind_param($delTrain, 'i', $trid);
        mysqli_stmt_execute($delTrain);
    }

    return true;
}

// --- FORM HANDLING ---
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['delete_train'])) {
        prepareTrainDeletion($link, $_POST['trid']);
        $message = "Train's tickets deleted (ready for removal).";
    } elseif (isset($_POST['add_train'])) {
        addTrain($link, $_POST['type'], $_POST['name'], $_POST['cargo_cars'], $_POST['passenger_cars']);
        $message = "Train added successfully.";
    } elseif (isset($_POST['delete_orphan_trains'])) {
        deleteOrphanTrains($link);
        $message = "Orphan trains deleted.";
    }
}

$trains = readTrains($link);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Trains</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Train Management</h2>
    <a href="index.php" class="btn btn-default" style="margin-bottom: 15px;">← Back to Main Page</a>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <h4>Add New Train</h4>
    <form method="post" class="form-inline">
        <input type="text" name="type" class="form-control" placeholder="Type" required>
        <input type="text" name="name" class="form-control" placeholder="Name" required>
        <input type="number" name="cargo_cars" class="form-control" placeholder="Cargo Cars" required>
        <input type="number" name="passenger_cars" class="form-control" placeholder="Passenger Cars" required>
        <button class="btn btn-success" name="add_train">Add Train</button>
    </form>

    <br>
    <form method="post" class="form-inline">
        <label>Train ID to prepare:</label>
        <input type="number" name="trid" class="form-control" required>
        <button class="btn btn-warning" name="delete_train">Delete Tickets for Train</button>
    </form>

    <br>
    <form method="post">
        <button class="btn btn-danger" name="delete_orphan_trains">Delete Orphan Trains</button>
    </form>

    <h4>Current Trains</h4>
    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>Type</th><th>Name</th><th>Cargo Cars</th><th>Passenger Cars</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($trains as $t): ?>
                <tr>
                    <td><?= $t['trid'] ?></td>
                    <td><?= $t['type'] ?></td>
                    <td><?= $t['name'] ?></td>
                    <td><?= $t['cargo_cars'] ?></td>
                    <td><?= $t['passenger_cars'] ?></td>
<td><a href='updateTrain.php?trid=<?= $t['trid'] ?>' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
