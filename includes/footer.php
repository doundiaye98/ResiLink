        </div>
    </main>
    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 ResiLink - Votre réseau social résidentiel</p>
            <p class="mb-0 mt-2">
                <small class="text-muted">Développé avec ❤️ par 
                    <a href="https://s2ntech.com" target="_blank" class="text-decoration-none fw-bold">S2NTech</a>
                </small>
            </p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isLoggedIn()): ?>
    <script>
        // Charger les notifications
        function loadNotifications() {
            fetch('api/notifications.php')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    const list = document.getElementById('notificationsList');
                    
                    if (data.unread > 0) {
                        badge.textContent = data.unread;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                    
                    if (data.notifications.length > 0) {
                        list.innerHTML = data.notifications.map(notif => 
                            `<li><a class="dropdown-item ${!notif.is_read ? 'fw-bold' : ''}" href="${notif.link || '#'}" onclick="markRead(${notif.id})">
                                ${notif.message}
                                <small class="text-muted d-block">${notif.time_ago}</small>
                            </a></li>`
                        ).join('');
                    }
                });
        }
        
        function markRead(notifId) {
            fetch('api/mark_notification_read.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id: notifId})
            });
        }
        
        // Charger les notifications toutes les 30 secondes
        loadNotifications();
        setInterval(loadNotifications, 30000);
    </script>
    <?php endif; ?>
</body>
</html>

