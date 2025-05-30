// Notification system for Owner
class NotificationManager {
    constructor() {
        this.init();
        this.loadNotifications();
        // Tự động cập nhật thông báo mỗi 30 giây
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }

    init() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationBell && notificationDropdown) {
            notificationBell.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Đóng dropdown khi click bên ngoài
            document.addEventListener('click', (e) => {
                if (!notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
                    this.hideDropdown();
                }
            });
        }
    }

    toggleDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            this.showDropdown();
        } else {
            this.hideDropdown();
        }
    }

    showDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = 'block';
        this.loadNotifications();
    }

    hideDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = 'none';
    }    async loadNotifications() {
        try {
            const response = await fetch('/owner/notifications', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.updateNotificationCount(data.unread_count);
                this.renderNotifications(data.notifications);
            } else {
                throw new Error('Response indicates failure');
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showError();
        }
    }

    updateNotificationCount(count) {
        const countElement = document.getElementById('notificationCount');
        if (countElement) {
            if (count > 0) {
                countElement.textContent = count > 99 ? '99+' : count;
                countElement.style.display = 'block';
            } else {
                countElement.style.display = 'none';
            }
        }
    }    renderNotifications(notifications) {
        const listContainer = document.getElementById('notificationList');
        const loadingElement = document.getElementById('notificationLoading');
        
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }

        if (!notifications || notifications.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-4" style="color: #6c757d;">
                    <i class="bi bi-bell-slash" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                    <p style="margin: 0;">Không có thông báo mới</p>
                </div>
            `;
            return;
        }

        const notificationsHtml = notifications.map(notification => {
            const iconClass = notification.type === 'appointment' ? 'bi-calendar-event' : 'bi-cash-stack';
            const iconColor = notification.type === 'appointment' ? '#007bff' : '#28a745';
            
            // Đảm bảo message không quá dài
            const truncatedMessage = notification.message.length > 100 
                ? notification.message.substring(0, 100) + '...' 
                : notification.message;
            
            return `
                <div class="notification-item" style="padding: 12px 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background-color 0.2s;" onclick="notificationManager.handleNotificationClick('${notification.url}')">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <div style="flex-shrink: 0;">
                            <i class="bi ${iconClass}" style="font-size: 1.2rem; color: ${iconColor};"></i>
                        </div>
                        <div style="flex-grow: 1; min-width: 0;">
                            <div style="font-weight: 600; color: #333; margin-bottom: 2px; font-size: 0.9rem;">
                                ${this.escapeHtml(notification.title)}
                            </div>
                            <div style="color: #666; font-size: 0.85rem; line-height: 1.3; margin-bottom: 4px;">
                                ${this.escapeHtml(truncatedMessage)}
                            </div>
                            <div style="color: #999; font-size: 0.75rem;">
                                ${this.escapeHtml(notification.time)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        listContainer.innerHTML = notificationsHtml;

        // Thêm hover effect
        const items = listContainer.querySelectorAll('.notification-item');
        items.forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = '#f8f9fa';
            });
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = 'white';
            });
        });
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    handleNotificationClick(url) {
        this.hideDropdown();
        if (url) {
            window.location.href = url;
        }
    }

    showError() {
        const listContainer = document.getElementById('notificationList');
        const loadingElement = document.getElementById('notificationLoading');
        
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }

        listContainer.innerHTML = `
            <div class="text-center py-4" style="color: #dc3545;">
                <i class="bi bi-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                <p style="margin: 0;">Không thể tải thông báo</p>
                <button onclick="notificationManager.loadNotifications()" style="margin-top: 10px; padding: 5px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Thử lại
                </button>
            </div>
        `;
    }
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof notificationManager === 'undefined') {
        window.notificationManager = new NotificationManager();
    }
});
