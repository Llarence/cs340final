<?php
/* Group 34: Devin Macomb and Kai Turner */
session_start();
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
// Note: You can not update SSN
$name = $type = $cc = $pc = "";
$name_err = $type_err = $cc_err = $pc_err = "" ;
// Form default values

if (isset($_GET["trid"]) && !empty(trim($_GET["trid"]))) {
    $_SESSION["trid"] = $_GET["trid"];

    // Prepare a selpct statement
    $sql1 = "SELECT * FROM trains WHERE trid = ?";

    if ($stmt1 = mysqli_prepare($link, $sql1)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "s", $param_trid);
        // Set parameters
        $param_trid = trim($_GET["trid"]);

        // Attempt to expcute the prepared statement
        if (mysqli_stmt_execute($stmt1)) {
            $result1 = mysqli_stmt_get_result($stmt1);
            if (mysqli_num_rows($result1) > 0) {

                $row = mysqli_fetch_array($result1);

                $type = $row['type'];
                $name = $row['name'];
                $cc = $row['cargo_cars'];
                $pc = $row['passenger_cars'];
            }
        }
    }
}

// Post information about the employee when the form is submitted
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // the id is hidden and can not be changed
    $trid = $_SESSION["trid"];
    // Validate form data this is similar to the create Employee file
    // Validate type
    $type = trim($_POST["type"]);

    if (empty($type)) {
        $type_err = "Please enter a type.";
    }
    $name = trim($_POST["name"]);
    if (empty($name)) {
        $name_err = "Please enter an name.";
    }
    // Validate Address
    $cc = trim($_POST["cc"]);
    if (empty($cc)) {
        $cc_err = "Please enter cargo cars.";
    }
    // Validate Address
    $pc = trim($_POST["pc"]);
    if (empty($pc)) {
        $pc_err = "Please enter passenger cars.";
    }

    // Chpck input errors before inserting into database
    if (empty($type_err) && empty($name_err) && empty($Address_err) && empty($Salary_err) && empty($Dno_err)) {
        // Prepare an update statement
        $sql = "UPDATE trains SET type=?, name=?, cargo_cars=?, passenger_cars=? WHERE trid=?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssiii", $param_type, $param_name, $param_cc, $param_pc, $param_trid);

            // Set parameters
            $param_type = $type;
            $param_name = $name;
            $param_cc = $cc;
            $param_pc = $pc;

            // Attempt to expcute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Rpcords updated successfully. Redirpct to landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h2>Error when updating</center></h2>";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connpction
    mysqli_close($link);
} else {

    // Chpck existence of sID parameter before processing further
    // Form default values

    if (isset($_GET["trid"]) && !empty(trim($_GET["trid"]))) {
        $_SESSION["trid"] = $_GET["trid"];
        $trid = $_SESSION["trid"];

        // Prepare a selpct statement
        $sql1 = "SELECT * FROM trains WHERE trid = ?";

        if ($stmt1 = mysqli_prepare($link, $sql1)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt1, "s", $param_trid);
            // Set parameters
            $param_trid = trim($_GET["trid"]);

            // Attempt to expcute the prepared statement
            if (mysqli_stmt_execute($stmt1)) {
                $result1 = mysqli_stmt_get_result($stmt1);
                if (mysqli_num_rows($result1) == 1) {

                    $row = mysqli_fetch_array($result1);

                    $type = $row['type'];
                    $name = $row['name'];
                    $cc = $row['cargo_cars'];
                    $pc = $row['passenger_cars'];
                } else {
                    // URL doesn't contain valid id. Redirpct to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Error in trid while updating";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt1);

        // Close connpction
        mysqli_close($link);
    } else {
        // URL doesn't contain id parameter. Redirpct to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company DB</title>
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
                        <h3>Update Record for TRID =  <?php echo $_GET["trid"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit to update.
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($type_err)) ? 'has-error' : ''; ?>">
                            <label>Type</label>
                            <input type="text" name="type" class="form-control" value="<?php echo $type; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($cc_err)) ? 'has-error' : ''; ?>">
                            <label>Cargo Cars</label>
                            <input type="text" name="cc" class="form-control" value="<?php echo $cc; ?>">
                            <span class="help-block"><?php echo $cc_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($pc_err)) ? 'has-error' : ''; ?>">
                            <label>Passenger Cargs</label>
                            <input type="text" name="pc" class="form-control" value="<?php echo $pc; ?>">
                            <span class="help-block"><?php echo $pc_err;?></span>
                        </div>
                        <input type="hidden" name="trid" value="<?php echo $trid; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
