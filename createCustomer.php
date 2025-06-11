<?php
/* Group 34: Devin Macomb and Kai Turner */
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$email = $name = $cc = $emergency_contact = "";
$email_err = $name_err = $cc_err = $emergency_contact_err = "" ;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate First name
    $name = trim($_POST["name"]);
    if (empty($name)) {
        $name_err = "Please enter a name.";
    } elseif (!filter_var($name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $name_err = "Please enter a valid name.";
    }
    // Validate Last name
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $email_err = "Please enter a email.";
    }
    $cc = trim($_POST["cc"]);
    // Validate Last name
    $emergency_contact = trim($_POST["emergency_contact"]);
    if (empty($emergency_contact)) {
        $emergency_contact_err = "Please enter a emergency_contact.";
    }

    // Check input errors before inserting in database
    if (empty($email_err) && empty($name_err) && empty($emergency_contact_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO customers (cid, name, email, credit_card, emergency_contact) 
		        VALUES (NULL, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $param_name,
                $param_email,
                $param_cc,
                $param_emergency_contact,
            );

            // Set parameters
            $param_email = $email;
            $param_name = $name;
            $param_cc = $cc;
            $param_emergency_contact = $emergency_contact;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h4>Error while creating new customer</h4></center>";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
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
                        <h2>Create Record</h2>
                    </div>
                    <p>Please fill this form and submit to add an Employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
						<div class="form-group <?php echo (!empty($emergency_contact_err)) ? 'has-error' : ''; ?>">
                            <label>Emergency Contact</label>
                            <input type="text" name="emergency_contact" class="form-control" value="<?php echo $emergency_contact; ?>">
                            <span class="help-block"><?php echo $emergency_contact_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
