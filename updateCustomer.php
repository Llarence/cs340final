<?php
/* Group 34: Devin Macomb and Kai Turner */
session_start();
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
// Note: You can not update SSN
$email = $name = $cc = $ec = "";
$email_err = $name_err = $cc_err = $ec_err = "" ;
// Form default values

if (isset($_GET["cid"]) && !empty(trim($_GET["cid"]))) {
    $_SESSION["cid"] = $_GET["cid"];

    // Prepare a select statement
    $sql1 = "SELECT * FROM customers WHERE cid = ?";

    if ($stmt1 = mysqli_prepare($link, $sql1)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "s", $param_cid);
        // Set parameters
        $param_cid = trim($_GET["cid"]);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt1)) {
            $result1 = mysqli_stmt_get_result($stmt1);
            if (mysqli_num_rows($result1) > 0) {

                $row = mysqli_fetch_array($result1);

                $name = $row['name'];
                $email = $row['email'];
                $cc = $row['credit_card'];
                $ec = $row['emergency_contact'];
            }
        }
    }
}

// Post information about the employee when the form is submitted
// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // the id is hidden and can not be changed
    $cid = $_SESSION["cid"];
    // Validate form data this is similar to the create Employee file
    // Validate name
    $name = trim($_POST["name"]);

    if (empty($name)) {
        $name_err = "Please enter a name.";
    } elseif (!filter_var($name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $name_err = "Please enter a valid name.";
    }
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $email_err = "Please enter an email.";
    }
    // Validate Address
    $cc = trim($_POST["cc"]);
    if (empty($cc)) {
        $cc_err = "Please enter a credit card.";
    }
    // Validate Address
    $ec = trim($_POST["ec"]);
    if (empty($ec)) {
        $ec_err = "Please enter an emergency contact.";
    }

    // Check input errors before inserting into database
    if (empty($name_err) && empty($email_err) && empty($cc_err) && empty($ec_err)) {
        // Prepare an update statement
        $sql = "UPDATE customers SET name=?, email=?, credit_card=?, emergency_contact=? WHERE cid=?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $param_name, $param_email, $param_cc, $param_ec, $param_cid);

            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_cc = $cc;
            $param_ec = $ec;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h2>Error when updating</center></h2>";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
} else {

    // Check existence of sID parameter before processing further
    // Form default values

    if (isset($_GET["cid"]) && !empty(trim($_GET["cid"]))) {
        $_SESSION["cid"] = $_GET["cid"];
        $cid = $_SESSION["cid"];

        // Prepare a select statement
        $sql1 = "SELECT * FROM customers WHERE cid = ?";

        if ($stmt1 = mysqli_prepare($link, $sql1)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt1, "s", $param_cid);
            // Set parameters
            $param_cid = trim($_GET["cid"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt1)) {
                $result1 = mysqli_stmt_get_result($stmt1);
                if (mysqli_num_rows($result1) == 1) {

                    $row = mysqli_fetch_array($result1);

                    $name = $row['name'];
                    $email = $row['email'];
                    $cc = $row['credit_card'];
                    $ec = $row['emergency_contact'];
                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Error in cid while updating";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt1);

        // Close connection
        mysqli_close($link);
    } else {
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
                        <h3>Update Record for CID =  <?php echo $_GET["cid"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit to update.
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                            <span class="help-block"><?php echo $email_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($cc_err)) ? 'has-error' : ''; ?>">
                            <label>Credit Card</label>
                            <input type="text" name="cc" class="form-control" value="<?php echo $cc; ?>">
                            <span class="help-block"><?php echo $cc_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($ec_err)) ? 'has-error' : ''; ?>">
                            <label>Emergency Contact</label>
                            <input type="text" name="ec" class="form-control" value="<?php echo $ec; ?>">
                            <span class="help-block"><?php echo $ec_err;?></span>
                        </div>
                        <input type="hidden" name="cid" value="<?php echo $cid; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
