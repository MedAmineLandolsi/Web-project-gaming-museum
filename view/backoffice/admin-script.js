// Toggle Sidebar
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1024) {
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// Animate Statistics Counter
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const updateCounter = () => {
        current += step;
        if (current < target) {
            element.textContent = Math.floor(current).toLocaleString();
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target.toLocaleString();
        }
    };
    
    updateCounter();
}

// Observe stats when they come into view
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statValues = entry.target.querySelectorAll('.stat-value');
            statValues.forEach(stat => animateCounter(stat));
            statsObserver.unobserve(entry.target);
        }
    });
});

const statsSection = document.querySelector('.stats-overview');
if (statsSection) {
    statsObserver.observe(statsSection);
}

// Animate Chart Bars
const chartObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const chartFills = entry.target.querySelectorAll('.chart-fill');
            chartFills.forEach((fill, index) => {
                setTimeout(() => {
                    fill.style.width = fill.style.width;
                }, index * 100);
            });
            chartObserver.unobserve(entry.target);
        }
    });
});

const chartSection = document.querySelector('.popular-games');
if (chartSection) {
    chartObserver.observe(chartSection);
}

// Add active state to nav items
const navItems = document.querySelectorAll('.nav-item a');
navItems.forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelector('.nav-item.active')?.classList.remove('active');
        item.parentElement.classList.add('active');
        
        // Close sidebar on mobile after click
        if (window.innerWidth <= 1024) {
            sidebar.classList.remove('open');
        }
    });
});

// Simulate real-time updates (optional)
setInterval(() => {
    const notifBadge = document.querySelector('.notif-badge');
    if (notifBadge) {
        let count = parseInt(notifBadge.textContent);
        if (Math.random() > 0.7) {
            count++;
            notifBadge.textContent = count;
            notifBadge.style.animation = 'none';
            setTimeout(() => {
                notifBadge.style.animation = '';
            }, 10);
        }
    }
}, 30000); // Every 30 seconds

// Console log for admin actions
const actionButtons = document.querySelectorAll('.action-btn');
actionButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        console.log('Action clicked:', btn.querySelector('.action-text')?.textContent || 'Action');
    });
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const actionsCells = document.querySelectorAll('.actions-cell');
    actionsCells.forEach(cell => {
        if (!cell.contains(e.target)) {
            const dropdown = cell.querySelector('.actions-dropdown-menu');
            if (dropdown) {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
                dropdown.style.transform = 'translateY(-10px)';
            }
        }
    });
});

// Prevent dropdown from closing when clicking inside
document.querySelectorAll('.actions-dropdown-menu').forEach(dropdown => {
    dropdown.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});

// Enhanced search with highlight
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
                
                // Add highlight effect
                row.style.animation = 'none';
                setTimeout(() => {
                    row.style.animation = 'startup 0.3s ease-out';
                }, 10);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update result count (optional)
        console.log(`${visibleCount} utilisateurs trouvés`);
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput?.focus();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const modal = document.getElementById('userModal');
        if (modal && modal.style.display === 'flex') {
            closeUserModal();
        }
    }
});

// Smooth scroll to top button (optional)
let scrollTopBtn = document.createElement('button');
scrollTopBtn.innerHTML = '↑';
scrollTopBtn.style.cssText = `
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
    border: 2px solid var(--primary-green);
    color: var(--text-white);
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 1000;
    font-family: 'Press Start 2P', cursive;
    box-shadow: 0 5px 20px rgba(0, 255, 65, 0.3);
`;

document.body.appendChild(scrollTopBtn);

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollTopBtn.style.opacity = '1';
        scrollTopBtn.style.visibility = 'visible';
    } else {
        scrollTopBtn.style.opacity = '0';
        scrollTopBtn.style.visibility = 'hidden';
    }
});

scrollTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Add hover sound effect (optional - requires audio file)
function playHoverSound() {
    // Uncomment if you have a sound file
    // const audio = new Audio('path/to/hover-sound.mp3');
    // audio.volume = 0.1;
    // audio.play();
}

// Add click sound effect (optional - requires audio file)
function playClickSound() {
    // Uncomment if you have a sound file
    // const audio = new Audio('path/to/click-sound.mp3');
    // audio.volume = 0.2;
    // audio.play();
}

// Add sound to buttons (optional)
document.querySelectorAll('button, .nav-item a, .actions-dropdown-link').forEach(element => {
    element.addEventListener('mouseenter', () => {
        // playHoverSound();
    });
    
    element.addEventListener('click', () => {
        // playClickSound();
    });
});

// Matrix rain effect on sidebar (optional)
function createMatrixRain() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;
    
    const canvas = document.createElement('canvas');
    canvas.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.05;
        pointer-events: none;
        z-index: 0;
    `;
    
    sidebar.style.position = 'relative';
    sidebar.insertBefore(canvas, sidebar.firstChild);
    
    const ctx = canvas.getContext('2d');
    canvas.width = sidebar.offsetWidth;
    canvas.height = sidebar.offsetHeight;
    
    const chars = '01アイウエオカキクケコサシスセソタチツテト';
    const fontSize = 14;
    const columns = canvas.width / fontSize;
    const drops = Array(Math.floor(columns)).fill(1);
    
    function draw() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.fillStyle = '#00FF41';
        ctx.font = fontSize + 'px monospace';
        
        drops.forEach((y, i) => {
            const text = chars[Math.floor(Math.random() * chars.length)];
            const x = i * fontSize;
            ctx.fillText(text, x, y * fontSize);
            
            if (y * fontSize > canvas.height && Math.random() > 0.975) {
                drops[i] = 0;
            }
            drops[i]++;
        });
    }
    
    setInterval(draw, 50);
}

// Uncomment to activate matrix rain effect
// createMatrixRain();

// Typing effect for page title (optional)
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.textContent = '';
    
    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    
    type();
}

// Data table sorting functionality
function sortTable(columnIndex, tableId = 'usersTable') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    let ascending = true;
    const currentSort = table.dataset.sortColumn;
    const currentOrder = table.dataset.sortOrder;
    
    if (currentSort === columnIndex.toString() && currentOrder === 'asc') {
        ascending = false;
    }
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Try to parse as numbers
        const aNum = parseFloat(aValue.replace(/[^0-9.-]/g, ''));
        const bNum = parseFloat(bValue.replace(/[^0-9.-]/g, ''));
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return ascending ? aNum - bNum : bNum - aNum;
        }
        
        return ascending 
            ? aValue.localeCompare(bValue)
            : bValue.localeCompare(aValue);
    });
    
    rows.forEach(row => tbody.appendChild(row));
    
    table.dataset.sortColumn = columnIndex;
    table.dataset.sortOrder = ascending ? 'asc' : 'desc';
    
    // Visual feedback
    const headers = table.querySelectorAll('th');
    headers.forEach((header, index) => {
        header.classList.remove('sorted-asc', 'sorted-desc');
        if (index === columnIndex) {
            header.classList.add(ascending ? 'sorted-asc' : 'sorted-desc');
        }
    });
}

// Add click handlers to table headers for sorting
document.querySelectorAll('.data-table th').forEach((header, index) => {
    header.style.cursor = 'pointer';
    header.addEventListener('click', () => {
        sortTable(index);
    });
});

// Auto-refresh data (optional - every 5 minutes)
let autoRefreshInterval;

function startAutoRefresh(intervalMinutes = 5) {
    autoRefreshInterval = setInterval(() => {
        console.log('Auto-refreshing data...');
        // You can add actual refresh logic here
        // location.reload();
    }, intervalMinutes * 60 * 1000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Uncomment to enable auto-refresh
// startAutoRefresh(5);

// Export data to CSV functionality
function exportTableToCSV(tableId = 'usersTable', filename = 'users_export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        cols.forEach(col => {
            csvRow.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
        });
        csv.push(csvRow.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (navigator.msSaveBlob) {
        navigator.msSaveBlob(blob, filename);
    } else {
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }
}

// Print table functionality
function printTable(tableId = 'usersTable') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Table</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<h2>Users Table</h2>');
    printWindow.document.write(table.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Bulk selection functionality
let selectedUsers = new Set();

function toggleUserSelection(userId) {
    if (selectedUsers.has(userId)) {
        selectedUsers.delete(userId);
    } else {
        selectedUsers.add(userId);
    }
    updateSelectionUI();
}

function selectAllUsers() {
    const rows = document.querySelectorAll('#usersTable tbody tr');
    rows.forEach(row => {
        const userId = row.dataset.userId;
        selectedUsers.add(userId);
    });
    updateSelectionUI();
}

function deselectAllUsers() {
    selectedUsers.clear();
    updateSelectionUI();
}

function updateSelectionUI() {
    const rows = document.querySelectorAll('#usersTable tbody tr');
    rows.forEach(row => {
        const userId = row.dataset.userId;
        if (selectedUsers.has(userId)) {
            row.style.backgroundColor = 'rgba(0, 255, 65, 0.1)';
        } else {
            row.style.backgroundColor = '';
        }
    });
    
    console.log(`${selectedUsers.size} users selected`);
}

// Performance monitoring
const performanceMonitor = {
    start: performance.now(),
    
    logMetric(metricName) {
        const now = performance.now();
        const duration = now - this.start;
        console.log(`[Performance] ${metricName}: ${duration.toFixed(2)}ms`);
    },
    
    reset() {
        this.start = performance.now();
    }
};

// Log page load time
window.addEventListener('load', () => {
    performanceMonitor.logMetric('Page Load Complete');
});

// Notification system enhancement
function showToastWithIcon(message, type = 'info', icon = '') {
    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };
    
    const finalIcon = icon || icons[type] || icons.info;
    
    if (typeof showToast === 'function') {
        showToast(`${finalIcon} ${message}`, type);
    } else {
        console.log(`[${type.toUpperCase()}] ${finalIcon} ${message}`);
    }
}

// Initialize tooltips (if you want to add them)
function initTooltips() {
    const elements = document.querySelectorAll('[data-tooltip]');
    
    elements.forEach(element => {
        element.addEventListener('mouseenter', (e) => {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = element.dataset.tooltip;
            tooltip.style.cssText = `
                position: absolute;
                background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
                border: 2px solid var(--primary-green);
                color: var(--text-white);
                padding: 0.5rem 1rem;
                font-size: 0.5rem;
                font-family: 'Press Start 2P', cursive;
                z-index: 10000;
                pointer-events: none;
                box-shadow: 0 5px 20px rgba(0, 255, 65, 0.3);
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = element.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
            
            element.addEventListener('mouseleave', () => {
                tooltip.remove();
            }, { once: true });
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    console.log('Admin Dashboard Initialized');
    initTooltips();
    
    // Add loading complete animation
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s';
        document.body.style.opacity = '1';
    }, 100);
});

// Debug mode toggle (Ctrl + Shift + D)
let debugMode = false;

document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        debugMode = !debugMode;
        console.log(`Debug mode: ${debugMode ? 'ON' : 'OFF'}`);
        
        if (debugMode) {
            document.body.style.outline = '2px solid red';
        } else {
            document.body.style.outline = 'none';
        }
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    stopAutoRefresh();
    selectedUsers.clear();
});

console.log('✓ Admin Script Loaded Successfully');