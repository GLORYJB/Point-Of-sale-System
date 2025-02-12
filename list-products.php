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

// Fetch products data
$query = "SELECT id, name, price, stock FROM products ORDER BY name ASC";

try {
    $result = $conn->query($query);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} catch (mysqli_sql_exception $e) {
    die("Error executing query: " . $e->getMessage());
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
    <title>Product List - Gmall POS System</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .btn-add-product {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-add-product:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
    </style>
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
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic-top">
        <div class="dropdown">
            <button class="dropbtn"><?php echo htmlspecialchars($user); ?> <i class="fas fa-caret-down"></i></button>
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
            <li class="nav-item">
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
                    <a href="add-products.php" class="dropdown-item"><i class="fas fa-circle nav-icon"></i>Add Product</a>
                    <a href="list-products.php" class="dropdown-item"><i class="fas fa-circle nav-icon"></i>Product List</a>
                </div>
            </li>
            <li class="nav-item has-dropdown active">
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
                    <a href="report.php" class="dropdown-item"><i class="fas fa-circle nav-icon"></i>  Report list</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-circle nav-icon"></i> Add Report</a>
                    
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
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Product List</h1>
        <p class="mb-4">View and manage all products.</p>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Products</h6>
                <a href="add-products.php" class="btn btn-add-product btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Add New Product</span>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="productsTable" class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                <td>
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete-product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        responsive: true,
        order: [[1, 'asc']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
});
</script>
<script src="js/script.js"></script>
</body>
</html>
