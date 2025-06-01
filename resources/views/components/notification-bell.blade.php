<div class="relative">
    <button id="notification-btn" class="text-white hover:text-gray-300 p-2 relative">
        <i class="fas fa-bell text-xl"></i>
        <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
    </button>
    
    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50">
        <div class="py-2">
            <div class="px-4 py-2 border-b border-gray-200">
                <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
            </div>
            <div id="notification-list" class="max-h-64 overflow-y-auto">
                <!-- Notifications will be loaded here -->
            </div>
            <div class="px-4 py-2 border-t border-gray-200">
                <button id="mark-all-read" class="text-sm text-blue-600 hover:text-blue-500">Tandai semua telah dibaca</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.getElementById('notification-btn');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationBadge = document.getElementById('notification-badge');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Toggle dropdown
    notificationBtn.addEventListener('click', function() {
        notificationDropdown.classList.toggle('hidden');
        if (!notificationDropdown.classList.contains('hidden')) {
            loadNotifications();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notificationBtn.contains(event.target) && !notificationDropdown.contains(event.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });

    // Load notifications
    function loadNotifications() {
        fetch('/notifications')
            .then(response => response.json())
            .then(notifications => {
                notificationList.innerHTML = '';
                
                if (notifications.length === 0) {
                    notificationList.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">Tidak ada notifikasi</div>';
                    notificationBadge.classList.add('hidden');
                } else {
                    const unreadCount = notifications.filter(n => !n.is_read).length;
                    if (unreadCount > 0) {
                        notificationBadge.textContent = unreadCount;
                    notificationBadge.classList.remove('hidden');
                    } else {
                        notificationBadge.classList.add('hidden');
                    }
                    
                    notifications.forEach(notification => {
                        const notificationElement = createNotificationElement(notification);
                        notificationList.appendChild(notificationElement);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = '<div class="px-4 py-2 text-sm text-red-500">Gagal memuat notifikasi</div>';
            });
    }

    // Create notification element
    function createNotificationElement(notification) {
        const div = document.createElement('div');
        div.className = `px-4 py-3 hover:bg-gray-50 border-b border-gray-100 ${notification.is_read ? 'opacity-75' : ''}`;
        
        const typeColors = {
            'info': 'text-blue-600',
            'warning': 'text-yellow-600',
            'error': 'text-red-600',
            'success': 'text-green-600'
        };
        
        div.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-circle ${typeColors[notification.type] || 'text-gray-600'} text-xs mt-1"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                    <p class="text-sm text-gray-600">${notification.message}</p>
                    <p class="text-xs text-gray-400 mt-1">${formatTime(notification.created_at)}</p>
                </div>
            </div>
        `;
        
        if (notification.url) {
            div.addEventListener('click', function() {
                window.location.href = notification.url;
            });
            div.classList.add('cursor-pointer');
        }
        
        return div;
    }

    // Format time
    function formatTime(timeString) {
        const time = new Date(timeString);
        const now = new Date();
        const diff = now - time;
        
        if (diff < 60000) {
            return 'Baru saja';
        } else if (diff < 3600000) {
            return Math.floor(diff / 60000) + ' menit lalu';
        } else if (diff < 86400000) {
            return Math.floor(diff / 3600000) + ' jam lalu';
        } else {
            return Math.floor(diff / 86400000) + ' hari lalu';
        }
    }

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        fetch('/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                loadNotifications(); // Reload notifications after marking as read
            notificationBadge.classList.add('hidden');
            } else {
                throw new Error('Failed to mark notifications as read');
            }
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
            alert('Gagal menandai notifikasi sebagai telah dibaca');
        });
    });

    // Load notifications on page load
    loadNotifications();

    // Auto-refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});
</script>

