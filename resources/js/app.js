import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const profileDropdownToggle = document.getElementById('profileDropdownToggle');
    const profileDropdownMenu = document.getElementById('profileDropdownMenu');

    if (profileDropdownToggle && profileDropdownMenu) {
        profileDropdownToggle.addEventListener('click', function () {
            profileDropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            const isClickInside = profileDropdownToggle.contains(event.target) || profileDropdownMenu.contains(event.target);
            if (!isClickInside && !profileDropdownMenu.classList.contains('hidden')) {
                profileDropdownMenu.classList.add('hidden');
            }
        });
    }

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContentWrapper = document.getElementById('mainContentWrapper');
    const body = document.body;

    const SIDEBAR_STATE_KEY = 'sidebarOpen';

    function applySidebarState(isOpen) {
        if (!sidebar || !mainContentWrapper || !body) return;

        sidebar.classList.toggle('-translate-x-full', !isOpen);
        
        if (window.innerWidth >= 768) { // md breakpoint (desktop)
            mainContentWrapper.style.marginLeft = isOpen ? '16rem' : '0';
            body.classList.toggle('md:sidebar-open', isOpen);
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden'); // Ensure overlay is hidden on desktop
        } else { // Mobile
            mainContentWrapper.style.marginLeft = '0';
            body.classList.remove('md:sidebar-open');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('hidden', !isOpen);
        }
    }

    function toggleSidebar() {
        const currentIsOpen = !sidebar.classList.contains('-translate-x-full');
        const newIsOpen = !currentIsOpen;
        localStorage.setItem(SIDEBAR_STATE_KEY, newIsOpen.toString());
        applySidebarState(newIsOpen);
    }

    // Initial state setup from localStorage
    let initiallyOpen = localStorage.getItem(SIDEBAR_STATE_KEY) === 'true';
    applySidebarState(initiallyOpen);


    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            // Only toggle if it's currently open and on mobile (where overlay is visible)
            if (!sidebar.classList.contains('-translate-x-full') && window.innerWidth < 768) {
                toggleSidebar();
            }
        });
    }

    window.addEventListener('resize', () => {
        let currentOpenState = localStorage.getItem(SIDEBAR_STATE_KEY) === 'true';
        applySidebarState(currentOpenState); // Re-apply based on persisted state and new window size
    });

    // Notification Bell Dropdown
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');

    if (notificationBell && notificationDropdown) {
        notificationBell.addEventListener('click', function(event) {
            event.preventDefault();
            notificationDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            const isClickInsideBell = notificationBell.contains(event.target) || notificationDropdown.contains(event.target);
            if (!isClickInsideBell && !notificationDropdown.classList.contains('hidden')) {
                notificationDropdown.classList.add('hidden');
            }
        });
    }
});
