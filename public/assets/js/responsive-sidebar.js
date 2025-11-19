(function () {
	const sidebar = document.getElementById('sidebar');
	if (!sidebar) {
		return;
	}

	const toggleButtons = Array.from(document.querySelectorAll('.mobile-menu-btn'));
	const overlay = document.querySelector('.sidebar-overlay');
	const mobileHeader = document.querySelector('.portal-mobile-header');
	const mobileLogo = mobileHeader?.querySelector('.portal-mobile-logo');
	const ACTIVE_CLASS = 'slide-in';
	const BODY_LOCK_CLASS = 'sidebar-open';

	const updateToggleIcons = (isOpen) => {
		toggleButtons.forEach((button) => {
			const icon = button.querySelector('i');
			button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			if (!icon) {
				return;
			}
			icon.classList.toggle('ti-menu-2', !isOpen);
			icon.classList.toggle('ti-x', isOpen);
		});
	};

	const setSidebarState = (isOpen) => {
		sidebar.classList.toggle(ACTIVE_CLASS, isOpen);
		document.body.classList.toggle(BODY_LOCK_CLASS, isOpen);
		if (overlay) {
			overlay.classList.toggle('active', isOpen);
		}

		const shouldFloatControls = isOpen && window.innerWidth <= 1400;
		toggleButtons.forEach((button) => {
			button.classList.toggle('is-floating', shouldFloatControls);
		});
		if (mobileLogo) {
			mobileLogo.classList.toggle('is-hidden', shouldFloatControls);
		}
		updateToggleIcons(isOpen);
	};

	const toggleSidebar = (event) => {
		event?.preventDefault();
		const shouldOpen = !sidebar.classList.contains(ACTIVE_CLASS);
		setSidebarState(shouldOpen);
	};

	toggleButtons.forEach((button) => {
		button.addEventListener('click', toggleSidebar);
	});

	overlay?.addEventListener('click', () => setSidebarState(false));

	window.addEventListener('keyup', (event) => {
		if (event.key === 'Escape') {
			setSidebarState(false);
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth > 1400) {
			setSidebarState(false);
		}
	});

	// Ensure correct initial state for icons
	updateToggleIcons(false);
})();
