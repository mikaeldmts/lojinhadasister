        </main>
    </div>
    
    <script>
    function toggleSidebar() {
        document.querySelector('.admin-sidebar').classList.toggle('active');
    }
    
    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.admin-sidebar');
        const menuBtn = document.querySelector('.mobile-menu-btn');
        
        if (window.innerWidth <= 992) {
            if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
    </script>
</body>
</html>
