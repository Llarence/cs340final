<?php
/* Group 34: Devin Macomb and Kai Turner */
session_start();
// Include config file
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Tickets</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
	   <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">View Tickets</h2>
						<a href="addTicket.php" class="btn btn-success pull-right">Add Tickets</a>
                    </div>
<?php

// Check existence of id parameter before processing further
if (isset($_GET["cid"]) && !empty(trim($_GET["cid"]))) {
    $_SESSION["cid"] = $_GET["cid"];
}

if (isset($_SESSION["cid"])) {

    // Prepare a select statement
    $sql = "SELECT cid, name, tid, departure, trid, rid, carry_on FROM tickets NATURAL JOIN customers WHERE cid=?";

    //$sql = "SELECT Essn, Pno, Hours From WORKS_ON WHERE Essn = ? ";
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param);
        // Set parameters
        $param = $_SESSION["cid"];

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            echo"<h4> Tickets for customer with cid =".$param."</h4><p>";
            if (mysqli_num_rows($result) > 0) {
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th width = 8%>cid</th>";
                echo "<th width = 10%>name</th>";
                echo "<th width = 8%>tid</th>";
                echo "<th width = 10%>departure</th>";
                echo "<th width = 8%>trid</th>";
                echo "<th width = 8%>rid</th>";
                echo "<th width = 10%>carry_on</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                // output data of each row
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['cid'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['tid'] . "</td>";
                    echo "<td>" . $row['departure'] . "</td>";
                    echo "<td>" . $row['trid'] . "</td>";
                    echo "<td>" . $row['rid'] . "</td>";
                    echo "<td>" . $row['carry_on'] . "</td>";

                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                mysqli_free_result($result);
            } else {
                echo "No Tickets. ";
            }
            //				mysqli_free_result($result);
        } else {
            // URL doesn't contain valid id parameter. Redirect to error page
            header("location: error.php");
            exit();
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else {
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>					                 					
	<p><a href="index.php" class="btn btn-primary">Back</a></p>
    </div>
   </div>        
  </div>
</div>
</body>
</html>
