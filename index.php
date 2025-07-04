<?php
/* Group 34: Devin Macomb and Kai Turner */
session_start();
//$currentpage="View Employees";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company DB</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
	<style type="text/css">
        .wrapper{
            width: 70%;
            margin:0 auto;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
		 $('.selectpicker').selectpicker();
    </script>
</head>
<body>
    <?php
        // Include config file
        require_once "config.php";
//		include "header.php";
?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
		    <div class="page-header clearfix">
		     <h2> Train Database </h2> 
             <div class="btn-group" role="group" aria-label="Train Database Navigation">
                <a href="manageTrains.php" class="btn btn-primary">Manage Trains</a>
                <a href="manageStations.php" class="btn btn-primary">Manage Stations</a>
                <a href="manageRoutes.php" class="btn btn-primary">Manage Routes</a>
                <a href="addRoutesToTrain.php" class="btn btn-info">Add Route To Train</a>
                <a href="trainRouteList.php" class="btn btn-info">View Routes By Train</a>
                <a href="createRouteTo.php" class="btn btn-info">Add New Route And Station</a>
            </div>
              <br>

		       <h2 class="pull-left">Customers</h2>
                        <a href="createCustomer.php" class="btn btn-success pull-right">Add New Customer</a>
                    </div>
                    <?php
                // Include config file
                require_once "config.php";

// Attempt select all employee query execution
// *****
// Insert your function for Salary Level
/*
    $sql = "SELECT Ssn,Fname,Lname,Salary, Address, Bdate, PayLevel(Ssn) as Level, Super_ssn, Dno
        FROM EMPLOYEE";
*/
$sql = "SELECT customers.*, COUNT(tickets.tid) as tickets FROM customers LEFT JOIN tickets ON customers.cid=tickets.cid GROUP BY customers.cid";
if ($result = mysqli_query($link, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th width=8%>cid</th>";
        echo "<th width=10%>name</th>";
        echo "<th width=10%>email</th>";
        echo "<th width=10%>credit_card</th>";
        echo "<th width=15%>emergency_contact</th>";
        echo "<th width=8%>tickets</th>";
        echo "<th width=10%>action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $row['cid'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['credit_card'] . "</td>";
            echo "<td>" . $row['emergency_contact'] . "</td>";
            echo "<td>" . $row['tickets'] . "</td>";
            echo "<td>";
            echo "<a href='viewTickets.php?cid=". $row['cid']."' title='View Tickets' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
            echo "<a href='updateCustomer.php?cid=". $row['cid'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
            echo "<a href='deleteCustomer.php?cid=". $row['cid'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        // Free result set
        mysqli_free_result($result);
    } else {
        echo "<p class='lead'><em>No records were found.</em></p>";
    }
} else {
    echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>
                </div>

</body>
</html>
