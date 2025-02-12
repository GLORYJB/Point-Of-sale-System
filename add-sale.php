<?php
// Start session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "posdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handling product scanning
if (isset($_POST['scan_product'])) {
    $product_code = $_POST['product_code'];
    $product = $conn->query("SELECT * FROM products WHERE product_code = '$product_code'")->fetch_assoc();

    if ($product) {
        // Add product to cart
        $_SESSION['cart'][] = $product;
    } else {
        $error = "Product not found!";
    }
}

// Calculate total
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'];
    }
}

// Handle checkout
if (isset($_POST['checkout'])) {
    if (!empty($_SESSION['cart'])) {
        $conn->query("INSERT INTO sales (total) VALUES ($total)");
        $sale_id = $conn->insert_id;

        foreach ($_SESSION['cart'] as $item) {
            $conn->query("INSERT INTO sales_items (sale_id, product_id, quantity, price) VALUES ($sale_id, {$item['id']}, 1, {$item['price']})");
        }

        // Clear cart after checkout
        $_SESSION['cart'] = [];
        $success = "Transaction complete!";
    }
}

// Handle cart reset
if (isset($_POST['reset_cart'])) {
    $_SESSION['cart'] = [];
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
    <title>Gmall POS System</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome for icons -->
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
            <li class="nav-item has-dropdown ">
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
    <h1 class="title">POS System</h1>

    <form method="POST" action="" class="form">
        <input type="text" name="product_code" class="input" placeholder="Scan Product Code" required autofocus>
        <button type="submit" name="scan_product" class="btn">Add to Cart</button>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>

    <h2 class="subtitle">Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="cart-table">
            <tr>
                <th>Product Name</th>
                <th>Price</th>
            </tr>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="empty-cart">Your cart is empty.</p>
    <?php endif; ?>

    <h3 class="total">Total: $<?php echo number_format($total, 2); ?></h3>

    <form method="POST" action="" class="checkout-form">
        <button type="submit" name="checkout" class="btn-checkout">Checkout</button>
        <button type="submit" name="reset_cart" class="btn-reset">Reset Cart</button>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
    </form>
</div>

<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get all necessary elements
    const sidebar = document.getElementById('mySidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar-btn');
    const dropdownBtns = document.querySelectorAll('.nav-item.has-dropdown');
    const topNavDropdown = document.querySelector('.profile-menu .dropdown');
    
    // Toggle sidebar function
    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        // Save state to localStorage
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    }
    
    // Restore sidebar state on page load
    const savedSidebarState = localStorage.getItem('sidebarCollapsed');
    if (savedSidebarState === 'true') {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }
    
    // Sidebar toggle button click event
    toggleSidebarBtn.addEventListener('click', toggleSidebar);
    
    // Handle dropdown toggles in sidebar
    dropdownBtns.forEach(item => {
        const link = item.querySelector('.nav-link');
        const dropdownContent = item.querySelector('.dropdown-container');
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close other dropdowns
            dropdownBtns.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    const otherDropdown = otherItem.querySelector('.dropdown-container');
                    if (otherDropdown) {
                        otherDropdown.style.maxHeight = null;
                    }
                }
            });
            
            // Toggle current dropdown
            item.classList.toggle('active');
            
            // Animate dropdown height
            if (dropdownContent) {
                if (dropdownContent.style.maxHeight) {
                    dropdownContent.style.maxHeight = null;
                } else {
                    dropdownContent.style.maxHeight = dropdownContent.scrollHeight + "px";
                }
            }
        });
    });
    
    // Top navigation profile dropdown
    if (topNavDropdown) {
        const dropdownContent = topNavDropdown.querySelector('.dropdown-content');
        
        topNavDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!topNavDropdown.contains(e.target)) {
                topNavDropdown.classList.remove('active');
            }
        });
    }
    
    // Handle mobile sidebar
    function handleMobileView() {
        if (window.innerWidth <= 768) {
            let overlay = document.getElementById('sidebar-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'sidebar-overlay';
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('collapsed');
                    this.style.display = 'none';
                });
            }
            
            toggleSidebarBtn.addEventListener('click', function() {
                overlay.style.display = sidebar.classList.contains('collapsed') ? 'block' : 'none';
            });
        }
    }
    
    // Initial call and window resize event
    handleMobileView();
    window.addEventListener('resize', handleMobileView);
});

</script>

</body>
</html>
