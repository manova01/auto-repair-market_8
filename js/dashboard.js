document.addEventListener("DOMContentLoaded", () => {
  // Mobile sidebar toggle
  const sidebarToggle = document.querySelector(".mobile-menu-toggle")
  const dashboardSidebar = document.querySelector(".dashboard-sidebar")

  if (sidebarToggle && dashboardSidebar) {
    sidebarToggle.addEventListener("click", () => {
      dashboardSidebar.classList.toggle("show")
    })
  }

  // Notification dropdown
  const notificationBtn = document.querySelector(".notification-btn")
  const notificationDropdown = document.querySelector(".notification-dropdown")

  if (notificationBtn && notificationDropdown) {
    notificationBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      notificationDropdown.classList.toggle("show")

      // Close user dropdown if open
      const userDropdown = document.querySelector(".dropdown-menu")
      if (userDropdown && userDropdown.classList.contains("show")) {
        userDropdown.classList.remove("show")
      }
    })
  }

  // User dropdown
  const userDropdownBtn = document.querySelector(".user-dropdown-btn")
  const userDropdownMenu = document.querySelector(".dropdown-menu")

  if (userDropdownBtn && userDropdownMenu) {
    userDropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation()
      userDropdownMenu.classList.toggle("show")

      // Close notification dropdown if open
      if (notificationDropdown && notificationDropdown.classList.contains("show")) {
        notificationDropdown.classList.remove("show")
      }
    })
  }

  // Close dropdowns when clicking outside
  document.addEventListener("click", () => {
    if (notificationDropdown && notificationDropdown.classList.contains("show")) {
      notificationDropdown.classList.remove("show")
    }

    if (userDropdownMenu && userDropdownMenu.classList.contains("show")) {
      userDropdownMenu.classList.remove("show")
    }
  })

  // Prevent dropdown from closing when clicking inside it
  if (notificationDropdown) {
    notificationDropdown.addEventListener("click", (e) => {
      e.stopPropagation()
    })
  }

  if (userDropdownMenu) {
    userDropdownMenu.addEventListener("click", (e) => {
      e.stopPropagation()
    })
  }

  // Mark notifications as read
  const notificationItems = document.querySelectorAll(".notification-item.unread")

  if (notificationItems) {
    notificationItems.forEach((item) => {
      item.addEventListener("click", function () {
        this.classList.remove("unread")

        // Update notification badge count
        const badge = document.querySelector(".notification-badge")
        if (badge) {
          const count = Number.parseInt(badge.textContent) - 1
          badge.textContent = count > 0 ? count : ""

          if (count === 0) {
            badge.style.display = "none"
          }
        }
      })
    })
  }
})

