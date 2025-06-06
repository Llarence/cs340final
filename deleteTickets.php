<?php
/* Group 34: Devin Macomb and Kai Turner */
session_start();
if (isset($_GET["tid"]) && !empty(trim($_GET["tid"]))) {
    $_SESSION["tid"] = $_GET["tid"];
}

require_once "config.php";
// Delete an Dependents's record after confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["tid"]) && !empty($_SESSION["tid"])) {
        $tid = $_SESSION['tid'];

        // Prepare a delete statement
        $sql = "DELETE FROM tickets WHERE tid = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param);

            // Set parameters
            $param = $tid;
            //echo $Essn;
            //echo $tid;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records deleted successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else {
                echo "Error deleting the employee";
            }
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of id parameter
    if (empty(trim($_GET["tid"]))) {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Delete Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="tid" value="<?php echo($_SESSION["tid"]); ?>"/>
                            <p>Are you sure you want to delete the record for the ticket with id 
							     <?php echo($_SESSION["tid"]);
echo " ".$tid; ?>?</p><br>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="index.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
