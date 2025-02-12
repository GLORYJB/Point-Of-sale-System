<?php
// Start session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "posdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sample user info
$user = "Glory John";
$profile_pic = "assets/profile.png";

// Fetch dashboard data
$today = date('Y-m-d');
$month = date('Y-m');
$year = date('Y');

// Today's sales - Add error handling
$today_sales_query = $conn->query("SELECT COUNT(*) as count, COALESCE(SUM(total), 0) as total 
                             FROM sales WHERE DATE(created_at) = '$today'");
if ($today_sales_query === false) {
    error_log("Query failed: " . $conn->error);
    $today_sales = ['count' => 0, 'total' => 0];
} else {
    $today_sales = $today_sales_query->fetch_assoc();
}

// Monthly sales - Add error handling
$monthly_sales_query = $conn->query("SELECT COUNT(*) as count, COALESCE(SUM(total), 0) as total 
                               FROM sales WHERE DATE(created_at) LIKE '$month%'");
if ($monthly_sales_query === false) {
    error_log("Query failed: " . $conn->error);
    $monthly_sales = ['count' => 0, 'total' => 0];
} else {
    $monthly_sales = $monthly_sales_query->fetch_assoc();
}

// Top selling products - Add error handling
$top_products = $conn->query("SELECT p.name, COUNT(*) as count
                              FROM sales_items si
                              JOIN products p ON p.id = si.product_id
                              GROUP BY p.id
                              ORDER BY count DESC
                              LIMIT 5");
if ($top_products === false) {
    error_log("Query failed: " . $conn->error);
    $top_products = [];
}

// Daily sales for the last 7 days - Add error handling
$daily_sales = $conn->query("SELECT DATE(created_at) as date, SUM(total) as total
                             FROM sales
                             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                             GROUP BY DATE(created_at)
                             ORDER BY date ASC");
if ($daily_sales === false) {
    error_log("Query failed: " . $conn->error);
    $sales_data = [];
} else {
    $sales_data = array();
    while ($row = $daily_sales->fetch_assoc()) {
        $sales_data[] = $row;
    }
}

// Low stock products - Add error handling
$low_stock = $conn->query("SELECT name, stock_quantity
                           FROM products
                           WHERE stock_quantity <= 10
                           ORDER BY stock_quantity ASC
                           LIMIT 5");
if ($low_stock === false) {
    error_log("Query failed: " . $conn->error);
    $low_stock = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gmall POS System</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Top Navigation -->
<div class="topnav">
    <button class="toggle-sidebar-btn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="logo">
        <h2>Gmall</h2>
    </div>
    <div class="profile-menu">
        <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-pic-top">
        <div class="dropdown">
            <button class="dropbtn"><?php echo $user; ?> <i class="fas fa-caret-down"></i></button>
            <div class="dropdown-content">
                <a href="#"><i class="fas fa-user"></i> Update Profile</a>
                <a href="#"><i class="fas fa-cog"></i> Settings</a>
                <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="sidebar" id="mySidebar">
    <nav class="nav">
        <ul>
            <li class="nav-item active">
                <a href="dashboard.php" class="nav-link" data-tooltip="Dashboard">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php" class="nav-link" data-tooltip="POS">
                    <i class="fas fa-cash-register nav-icon"></i>
                    <span class="nav-text">POS</span>
                </a>
            </li>
            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link" data-tooltip="Products">
                    <i class="fas fa-box nav-icon"></i>
                    <span class="nav-text">Products</span>
                    <i class="fas fa-chevron-down dropdown-indicator"></i>
                </a>
                <div class="dropdown-container">
                    <a href="#" class="dropdown-item"><i class="fas fa-circle nav-icon"></i>Add Product</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-circle nav-icon"></i>Product List</a>
                </div>
            </li>
            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link" data-tooltip="Sales">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    <span class="nav-text">Sales</span>
                    <i class="fas fa-chevron-down dropdown-indicator"></i>
                </a>
                <div class="dropdown-container">
                    <a href="add-sale.php" class="dropdown-item"><i class="fas fa-circle nav-icon"></i>Add Sales</a>
                    <a href="list-sales.php" class="dropdown-item"><i class="fas fa-circle nav-icon"></i>Sales List</a>
                </div>
            </li>
            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link" data-tooltip="Reports">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    <span class="nav-text">Reports</span>
                    <i class="fas fa-chevron-down dropdown-indicator"></i>
                </a>
                <div class="dropdown-container">
                    <a href="#" class="dropdown-item"><i class="fas fa-circle nav-icon"></i> Daily Report</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-circle nav-icon"></i> Weekly Report</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-circle nav-icon"></i> Monthly Report</a>
                </div>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-tooltip="Settings">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>
    </nav>
</div>



<div class="main-content">
    <h1 class="title">POS System Dashboard</h1>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-details">
                <h3>Today's Sales</h3>
                <p class="stat-number"><?php echo $today_sales['count']; ?></p>
                <p class="stat-amount">$<?php echo number_format($today_sales['total'], 2); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-details">
                <h3>Monthly Sales</h3>
                <p class="stat-number"><?php echo $monthly_sales['count']; ?></p>
                <p class="stat-amount">$<?php echo number_format($monthly_sales['total'], 2); ?></p>
            </div>
        </div>
        <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-box"></i></div>
        <div class="stat-details">
            <h3>Low Stock Items</h3>
            <p class="stat-number"><?php $low_stock=0; $low_stock_count; ?></p>
            <p class="stat-label">Products</p>
        </div>
    </div>
</div>

    <!-- Charts Row -->
    <div class="charts-row">
        <div class="chart-container">
            <h2>Sales Last 7 Days</h2>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Top Selling Products</h2>
            <canvas id="productsChart"></canvas>
        </div>
    </div> 

    <div class="alert-section">
    <h2><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h2>
    <div class="alert-grid">
        <?php 
        if (is_object($low_stock) && $low_stock->num_rows > 0):
            while ($product = $low_stock->fetch_assoc()): 
        ?>
            <div class="alert-card">
                <div class="alert-icon"><i class="fas fa-box"></i></div>
                <div class="alert-details">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p>Quantity: <span class="<?php echo $product['stock_quantity'] <= 5 ? 'critical' : 'warning'; ?>">
                        <?php echo htmlspecialchars($product['stock_quantity']); ?>
                    </span></p>
                </div>
            </div>
        <?php 
            endwhile;
        else:
        ?>
            <div class="alert-card">
                <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                <div class="alert-details">
                    <h4>No Low Stock Items</h4>
                    <p>All products have sufficient stock.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
// Existing sidebar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // ... (keep your existing sidebar JavaScript here)
    
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($sales_data, 'date')); ?>,
            datasets: [{
                label: 'Daily Sales',
                data: <?php echo json_encode(array_column($sales_data, 'total')); ?>,
                borderColor: '#3498db',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Top Products Chart
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    new Chart(productsCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php 
                $top_products->data_seek(0);
                while ($product = $top_products->fetch_assoc()) {
                    echo "'" . $product['name'] . "',";
                }
            ?>],
            datasets: [{
                data: [<?php 
                    $top_products->data_seek(0);
                    while ($product = $top_products->fetch_assoc()) {
                        echo $product['count'] . ",";
                    }
                ?>],
                backgroundColor: ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
<script src="js/script.js"></script>

</body>
</html>