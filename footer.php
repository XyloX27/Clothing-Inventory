        </div> <!-- Close main-content -->
    </div> <!-- Close body wrapper -->

    <!-- Required Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile Sidebar Toggle -->
    <script>
        // Initialize mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.createElement('button');
            sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
            sidebarToggle.className = 'btn btn-primary d-lg-none position-fixed';
            sidebarToggle.style.bottom = '20px';
            sidebarToggle.style.right = '20px';
            sidebarToggle.style.zIndex = '1000';
            
            sidebarToggle.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
            
            if(document.querySelector('.sidebar')) {
                document.body.appendChild(sidebarToggle);
            }
        });
    </script>
</body>
</html>