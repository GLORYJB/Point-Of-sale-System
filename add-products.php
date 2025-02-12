<?php
// Start session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "posdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Initialize variables
$name = $price = $stock = $description = "";
$error = $success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $name = trim($_POST["name"]);
    $price = filter_var($_POST["price"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $stock = filter_var($_POST["stock"], FILTER_SANITIZE_NUMBER_INT);
    $description = trim($_POST["description"]);

    // Basic validation
    if (empty($name) || empty($price) || empty($stock)) {
        $error = "Please fill in all required fields.";
    } else {
        // Create new product
        $product = new Product($conn);
        $result = $product->create();

        if ($result) {
            $success = "Product added successfully!";
            // Clear form fields after successful submission
            $name = $price = $stock = $description = "";
        } else {
            $error = "Error: Unable to add product.";
        }
    }
}

// Sample user info (replace with actual user data in production)
$user = "Glory John";
$profile_pic = "assets/profile.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Gmall POS System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-submit {
            background-color: #4e73df;
            border-color: #4e73df;
            border-radius: 10px;
        }
        .btn-submit:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        .alert {
            border-radius: 10px;
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="h3 mb-4 text-gray-800">Add New Product</h1>
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Product Information</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <form id="addProductForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="name">Product Name *</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="price">Price *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="stock">Stock *</label>
                                    <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($stock); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-submit btn-block">
                                <i class="fas fa-plus-circle mr-2"></i>Add Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
$(document).ready(function() {
    // Client-side form validation
    $("#addProductForm").submit(function(event) {
        var name = $("#name").val().trim();
        var price = $("#price").val().trim();
        var stock = $("#stock").val().trim();

        if (name === "" || price === "" || stock === "") {
            event.preventDefault();
            alert("Please fill in all required fields.");
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $(".alert").alert('close');
    }, 5000);
});
</script>
<script src="js/script.js"></script>

</body>
</html>