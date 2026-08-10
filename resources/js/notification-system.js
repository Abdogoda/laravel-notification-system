/**
 * Notification System Modular JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    const refreshUnreadBadge = async () => {
        const badgeElement = document.querySelector('[data-notification-unread-badge]');
        if (!badgeElement) return;

        try {
            const response = await fetch('/dashboard/notifications/unread-count', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (response.ok) {
                const data = await response.json();
                if (data.unread_count !== undefined) {
                    badgeElement.textContent = data.unread_count;
                    if (data.unread_count > 0) {
                        badgeElement.classList.remove('d-none', 'hidden');
                    } else {
                        badgeElement.classList.add('hidden');
                    }
                }
            }
        } catch (error) {
            console.error('Failed to update notification unread badge:', error);
        }
    };

    // Auto update every 60s if badge is present
    if (document.querySelector('[data-notification-unread-badge]')) {
        setInterval(refreshUnreadBadge, 60000);
    }
});
