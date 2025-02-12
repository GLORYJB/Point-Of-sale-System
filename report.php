<?php
// Start session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "posdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Function to get sales data
function getSalesData($start_date, $end_date) {
    global $conn;
    $query = "SELECT DATE(date) as sale_date, SUM(total) as daily_total
              FROM sales
              WHERE date BETWEEN ? AND ?
              GROUP BY DATE(date)
              ORDER BY DATE(date)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sales_data = [];
    while ($row = $result->fetch_assoc()) {
        $sales_data[] = $row;
    }
    
    return $sales_data;
}

// Get date range from request or use default
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days', strtotime($end_date)));

// Get sales data
$sales_data = getSalesData($start_date, $end_date);

// Calculate total sales
$total_sales = array_sum(array_column($sales_data, 'daily_total'));

// Prepare data for charts
$dates = [];
$totals = [];
foreach ($sales_data as $data) {
    $dates[] = $data['sale_date'];
    $totals[] = $data['daily_total'];
}

// Sample user info
$user = "Glory John";
$profile_pic = "assets/profile.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Gmall POS System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            padding-top: 60px;
            background-color: #343a40;
            transition: all 0.3s;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .main-content.expanded {
            margin-left: 70px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .btn-export {
            margin-right: 10px;
        }
        #salesChart {
            max-height: 400px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .main-content {
                margin-left: 70px;
            }
            .sidebar .nav-text {
                display: none;
            }
            .sidebar.collapsed {
                width: 0;
            }
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Gmall POS</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="rounded-circle" width="30" height="30">
                    <?php echo htmlspecialchars($user); ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a>
                    <a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fas fa-cash-register"></i> <span class="nav-text">POS</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="list-sales.php">
                <i class="fas fa-list"></i> <span class="nav-text">Sales List</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="report.php">
                <i class="fas fa-chart-bar"></i> <span class="nav-text">Reports</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-cog"></i> <span class="nav-text">Settings</span>
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h1 class="mt-4">Sales Report</h1>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Date Range Selection
                    </div>
                    <div class="card-body">
                        <form id="dateRangeForm" class="form-inline">
                            <div class="form-group mr-2">
                                <label for="start_date" class="mr-2">Start Date:</label>
                                <input type="text" id="start_date" name="start_date" class="form-control datepicker" value="<?php echo $start_date; ?>" required>
                            </div>
                            <div class="form-group mr-2">
                                <label for="end_date" class="mr-2">End Date:</label>
                                <input type="text" id="end_date" name="end_date" class="form-control datepicker" value="<?php echo $end_date; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Sales Overview
                    </div>
                    <div class="card-body">
                        <h5>Total Sales: $<?php echo number_format($total_sales, 2); ?></h5>
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Export Options
                    </div>
                    <div class="card-body">
                        <button id="printBtn" class="btn btn-secondary btn-export"><i class="fas fa-print"></i> Print</button>
                        <button id="pdfBtn" class="btn btn-danger btn-export"><i class="fas fa-file-pdf"></i> Export PDF</button>
                        <button id="excelBtn" class="btn btn-success btn-export"><i class="fas fa-file-excel"></i> Export Excel</button>
                        <button id="csvBtn" class="btn btn-info btn-export"><i class="fas fa-file-csv"></i> Export CSV</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize date pickers
    $(".datepicker").flatpickr({
        dateFormat: "Y-m-d"
    });

    // Initialize sales chart
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Daily Sales',
                data: <?php echo json_encode($totals); ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Print functionality
    $("#printBtn").click(function() {
        window.print();
    });

    // PDF export
    $("#pdfBtn").click(function() {
        var doc = new jspdf.jsPDF();
        doc.text("Sales Report", 10, 10);
        doc.autoTable({ html: '#salesTable' });
        doc.save("sales_report.pdf");
    });

    // Excel export
    $("#excelBtn").click(function() {
        var wb = XLSX.utils.table_to_book(document.getElementById('salesTable'), {sheet:"Sales Report"});
        XLSX.writeFile(wb, "sales_report.xlsx");
    });

    // CSV export
    $("#csvBtn").click(function() {
        var wb = XLSX.utils.table_to_book(document.getElementById('salesTable'), {sheet:"Sales Report"});
        XLSX.writeFile(wb, "sales_report.csv");
    });

    // Sidebar toggle functionality
    $(".navbar-toggler").click(function() {
        $("#sidebar").toggleClass("collapsed");
        $(".main-content").toggleClass("expanded");
    });
});
</script>

</body>
</html>