<?php
/* Group 34: Devin Macomb and Kai Turner */
session_start();
$cid = $_SESSION["cid"];

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$departure = $trid = $rid = $carry_on = "" ;
$departure_err =  $rid_err = $trid_err = $carry_on_err = "" ;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate departure
    $departure = trim($_POST["departure"]);
    if (empty($departure)) {
        $departure_err = "Please enter a departure.";
    }

    // Validate rid
    $rid = trim($_POST["rid"]);
    if (empty($rid)) {
        $rid_err = "Please enter rid.";
    }

    // Validate trid
    $carry_on = trim($_POST["carry_on"]);
    if (empty($carry_on)) {
        $carry_on_err = "Please enter carry_on.";
    }

    // Validate trid
    $trid = trim($_POST["trid"]);
    if (empty($trid)) {
        $trid_err = "Please enter trid.";
    }

    // Check input errors before inserting in database
    if (empty($carry_on_err) && empty($departure_err) && empty($rid_err) && empty($trid_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO tickets (tid, departure, rid, trid, cid, carry_on) 
		        VALUES (NULL, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $param_departure,
                $param_rid,
                $param_trid,
                $param_cid,
                $param_carry_on
            );

            // Set parameters
            $param_departure = $departure;
            $param_rid = $rid;
            $param_trid = $trid;
            $param_cid = $cid;
            $param_carry_on = $carry_on;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h4>Error while creating new ticket: " . mysqli_stmt_error($stmt) . "</h4></center>";
                $name_err = "Re-enter all values";
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
                        <h2>Create Ticket</h2>
						<h3> For customer with cid = <?php echo $cid; ?> </h3>
                    </div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			
             
						<div class="form-group <?php echo (!empty($carry_on_err)) ? 'has-error' : ''; ?>">
                            <label>Carry On</label>
                            <input type="text" name="carry_on" class="form-control" value="<?php echo $carry_on; ?>">
                            <span class="help-block"><?php echo $carry_on_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($trid_err)) ? 'has-error' : ''; ?>">
                            <label>Trid</label>
                            <input type="text" name="trid" class="form-control" value="<?php echo $trid; ?>">
                            <span class="help-block"><?php echo $trid_err;?></span>
                        </div>
				
						<div class="form-group <?php echo (!empty($rid_err)) ? 'has-error' : ''; ?>">
                            <label>Rid</label>
                            <input type="text" name="rid" class="form-control" value="<?php echo $rid; ?>">
                            <span class="help-block"><?php echo $rid_err;?></span>
                        </div>
						                  
						<div class="form-group <?php echo (!empty($departure_err)) ? 'has-error' : ''; ?>">
                            <label>Departure</label>
                            <input type="date" name="departure" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            <span class="help-block"><?php echo $departure_err;?></span>
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
