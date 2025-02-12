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

// Fetch sales data
$query = "SELECT s.id, s.total, s.date AS sale_date, GROUP_CONCAT(p.name SEPARATOR ', ') AS products
          FROM sales s
          LEFT JOIN sales_items si ON s.id = si.sale_id
          LEFT JOIN products p ON si.product_id = p.id
          GROUP BY s.id
          ORDER BY s.date DESC";

try {
    $result = $conn->query($query);
    
    $sales = [];
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
} catch (mysqli_sql_exception $e) {
    die("Error executing query: " . $e->getMessage());
}

// Sample user info
$user = "Glory John";
$profile_pic = "assets/profile.png";
?>

<!DOCTYPE html>
< lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales List - Gmall POS System</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.5s;
        }
        .main-content.expanded {
            margin-left: 70px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
        }
        .btn-add-sale {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-add-sale:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        .table-responsive {
            overflow-x: auto;
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
        <h1 class="h3 mb-2 text-gray-800">Sales List</h1>
        <p class="mb-4">View and manage all sales transactions.</p>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Sales Transactions</h6>
                <a href="add-sale.php" class="btn btn-add-sale btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Add New Sale</span>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="salesTable" class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sale['id']); ?></td>
                                <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                                <td>$<?php echo number_format($sale['total'], 2); ?></td>
                                <td><?php echo htmlspecialchars($sale['products']); ?></td>
                                <td>
                                    <a href="view-sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit-sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete-sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this sale?');">
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
    $('#salesTable').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        columnDefs: [
            { targets: 2, render: $.fn.dataTable.render.number(',', '.', 2, '$') }
        ]
    });

    // Sidebar toggle functionality
    const sidebar = document.getElementById('mySidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar-btn');
    
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

    // Dropdown functionality
    const dropdownBtns = document.querySelectorAll('.nav-item.has-dropdown');
    
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
    const topNavDropdown = document.querySelector('.profile-menu .dropdown');
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