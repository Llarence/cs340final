<?php
/* Group 34: Devin Macomb and Kai Turner */
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$station = $name = $latitude = $longitude = "";
$station_err = $name_err = $latitude_err = $longitude_err = "" ;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate First name
    $station = trim($_POST["station"]);
    if (empty($station)) {
        $station_err = "Please enter a station id.";
    }
    // Validate Last name
    $name = trim($_POST["name"]);
    if (empty($name)) {
        $name_err = "Please enter a name.";
    }
    $latitude = trim($_POST["latitude"]);
    if (empty($latitude)) {
        $latitude_err = "Please enter a latitude.";
    }
    $longitude = trim($_POST["longitude"]);
    if (empty($longitude)) {
        $longitude_err = "Please enter a longitude.";
    }

    // Check input errors before inserting in database
    if (empty($station_err) && empty($name_err) && empty($latitude_err) && empty($longitude_err)) {
        // Prepare an insert statement
        $sql = "CALL route_to(?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param(
                $stmt,
                "isdd",
                $param_station,
                $param_name,
                $param_latitude,
                $param_longitude,
            );

            // Set parameters
            $param_station = $station;
            $param_name = $name;
            $param_latitude = $latitude;
            $param_longitude = $longitude;

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
                    <p>Add a route to a new station.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="form-group <?php echo (!empty($station_err)) ? 'has-error' : ''; ?>">
                            <label>Old Station ID</label>
                            <input type="text" name="station" class="form-control" value="<?php echo $station; ?>">
                            <span class="help-block"><?php echo $station_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>New Station Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($latitude_err)) ? 'has-error' : ''; ?>">
                            <label>New Station Latitude</label>
                            <input type="text" name="latitude" class="form-control" value="<?php echo $latitude; ?>">
                            <span class="help-block"><?php echo $latitude_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($longitude_err)) ? 'has-error' : ''; ?>">
                            <label>New Station Longitude</label>
                            <input type="text" name="longitude" class="form-control" value="<?php echo $longitude; ?>">
                            <span class="help-block"><?php echo $longitude_err;?></span>
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
