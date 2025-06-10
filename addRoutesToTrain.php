<?php
/* Group 34: Devin Macomb and Kai Turner */
require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $trid = intval($_POST["trid"]);
    $rid = intval($_POST["rid"]);

    $sql = "INSERT INTO trains_and_routes (trid, rid) VALUES (?, ?)";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $trid, $rid);
        if (mysqli_stmt_execute($stmt)) {
            $message = "✅ Route $rid successfully added to Train $trid.";
        } else {
            $message = "❌ Error adding route: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "❌ Prepare failed: " . mysqli_error($link);
    }
}

// Fetch available trains and routes
$trains = mysqli_query($link, "SELECT trid FROM trains");
$routes = mysqli_query($link, "SELECT rid FROM routes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Route to Train</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<body>
<div class="container">
    <h2>Add Route to Train</h2>
    <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>

    <form method="post">
        <div class="form-group">
            <label>Train ID (trid)</label>
            <select name="trid" class="form-control" required>
                <option value="">Select a Train</option>
                <?php while ($row = mysqli_fetch_assoc($trains)): ?>
                    <option value="<?= $row['trid'] ?>"><?= $row['trid'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Route ID (rid)</label>
            <select name="rid" class="form-control" required>
                <option value="">Select a Route</option>
                <?php while ($row = mysqli_fetch_assoc($routes)): ?>
                    <option value="<?= $row['rid'] ?>"><?= $row['rid'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Route to Train</button>
        <a href="index.php" class="btn btn-default">Back to Main</a>
    </form>
</div>
</body>
</html>
