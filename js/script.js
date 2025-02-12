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

// JavaScript to handle active class
document.querySelectorAll('.nav-item a').forEach(item => {
    item.addEventListener('click', function() {
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(nav => {
            nav.classList.remove('active');
        });
        // Add active class to the clicked nav item
        this.parentElement.classList.add('active');
    });
});

document.getElementById('generateReport').addEventListener('click', function() {
    alert('Generate report functionality not implemented!');
});

document.getElementById('printReport').addEventListener('click', function() {
    window.print();
});

document.getElementById('pdfReport').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("Report Title", 20, 10);
    doc.autoTable({ html: '#reportTable' });
    doc.save('report.pdf');
});

document.getElementById('csvReport').addEventListener('click', function() {
    let table = document.getElementById('reportTable');
    let csvContent = [];
    for (let i = 0; i < table.rows.length; i++) {
        let row = Array.from(table.rows[i].cells).map(cell => cell.innerText);
        csvContent.push(row.join(","));
    }
    let csvBlob = new Blob([csvContent.join("\n")], { type: 'text/csv' });
    saveAs(csvBlob, 'report.csv');
});

document.getElementById('excelReport').addEventListener('click', function() {
    let table = document.getElementById('reportTable');
    let wb = XLSX.utils.table_to_book(table, { sheet: "Report" });
    XLSX.writeFile(wb, 'report.xlsx');
});
