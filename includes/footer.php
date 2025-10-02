<?php 
// Ensure this file is only included by a main PHP page (like dashboard.php)
if (basename($_SERVER['PHP_SELF']) == 'footer.php') {
    die('Access denied.');
}
?>
            </main>
        </div>
    </div>
    
    <script>
        // --- Core Frontend Initializer ---
        
        // Function to create icons after content is loaded
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // --- Mobile Menu Toggle Logic ---
            const sidebar = document.getElementById('sidebar');
            const mobileButton = document.getElementById('mobileMenuButton');
            
            if (mobileButton) {
                mobileButton.addEventListener('click', () => {
                    // On mobile, the button is hidden, so this handles desktop view only
                    sidebar.classList.toggle('hidden');
                    
                    // Adjust the fixed header's left position and width dynamically
                    const header = document.getElementById('top-header');
                    if (sidebar.classList.contains('hidden')) {
                        // Sidebar is closed: Header takes full width
                        header.classList.remove('md:w-[calc(100%-16rem)]', 'md:left-64');
                        header.classList.add('w-full', 'md:left-0');
                    } else {
                        // Sidebar is open: Header must offset
                        header.classList.remove('md:left-0');
                        header.classList.add('md:w-[calc(100%-16rem)]', 'md:left-64');
                    }
                });
            }
            
            // Ensure the initial state is correct for desktop (sidebar visible)
            // This is crucial for responsiveness on load
            if (window.innerWidth >= 768) { 
                const header = document.getElementById('top-header');
                header.classList.add('md:w-[calc(100%-16rem)]', 'md:left-64');
            }
        });
    </script>
</body>
</html>
