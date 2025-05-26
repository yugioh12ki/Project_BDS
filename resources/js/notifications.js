document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationContent = document.getElementById('notificationContent');
    const notificationCount = document.getElementById('notificationCount');
    const notificationList = document.getElementById('notificationList');

    // Toggle notification dropdown
    notificationBtn?.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationContent.classList.toggle('show');
        if(notificationContent.classList.contains('show')) {
            fetchNotifications();
        }
    });

    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationContent?.contains(e.target) && !notificationBtn?.contains(e.target)) {
            notificationContent?.classList.remove('show');
        }
    });

    // Fetch notifications from server
    function fetchNotifications() {
        fetch('/customer/notifications')
            .then(response => response.json())
            .then(data => {
                updateNotificationCount(data.unreadCount);
                updateNotificationList(data.notifications);
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    // Update notification count badge
    function updateNotificationCount(count) {
        if(notificationCount) {
            notificationCount.textContent = count;
            notificationCount.style.display = count > 0 ? 'block' : 'none';
        }
    }

    // Update notification list content
    function updateNotificationList(notifications) {
        if(!notificationList) return;
        
        notificationList.innerHTML = notifications.length ? notifications.map(notification => `
            <div class="notification-item ${notification.read_at ? '' : 'unread'}" data-id="${notification.id}">
                <div class="notification-title">
                    Lịch hẹn mới từ ${notification.data.agent_name}
                </div>
                <div class="notification-time">
                    ${formatTime(notification.created_at)}
                </div>
            </div>
        `).join('') : '<div class="p-3 text-center">Không có thông báo mới</div>';

        // Add click event to notification items
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                markAsRead(notificationId);
                window.location.href = '/customer/appointments';
            });
        });
    }

    // Mark notification as read
    function markAsRead(notificationId) {
        fetch(`/customer/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).catch(error => console.error('Error marking notification as read:', error));
    }

    // Format time helper
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInHours = Math.floor((now - date) / (1000 * 60 * 60));

        if (diffInHours < 1) {
            return 'Vừa xong';
        } else if (diffInHours < 24) {
            return `${diffInHours} giờ trước`;
        } else {
            return date.toLocaleDateString('vi-VN');
        }
    }

    // Initial fetch
    if(notificationBtn) {
        fetchNotifications();
        // Fetch notifications every 5 minutes
        setInterval(fetchNotifications, 5 * 60 * 1000);
    }
});