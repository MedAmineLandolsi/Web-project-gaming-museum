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
        console.log('Action clicked:', btn.querySelector('.action-text').textContent);
        // Add your logic here
    });
});

// Edit and Delete buttons
const editButtons = document.querySelectorAll('.icon-btn.edit');
const deleteButtons = document.querySelectorAll('.icon-btn.delete');

editButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        const row = e.target.closest('tr') || e.target.closest('.event-item');
        console.log('Edit:', row);
        // Add your edit logic here
    });
});

deleteButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
            const row = e.target.closest('tr');
            if (row) {
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
        }
    });
});

