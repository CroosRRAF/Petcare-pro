// Header JavaScript
document.addEventListener("DOMContentLoaded", function () {
  // Left sidebar toggle
  const leftSidebarToggle = document.getElementById("left-sidebar-toggle");
  const leftSidebar = document.getElementById("left-sidebar");

  if (leftSidebarToggle && leftSidebar) {
    leftSidebarToggle.addEventListener("click", () => {
      leftSidebar.classList.toggle("open");
      leftSidebarToggle.classList.toggle("open");
    });
  }

  // Mobile menu toggle
  const mobileMenuBtn = document.getElementById("mobile-menu-btn");
  const mobileSidebar = document.getElementById("mobile-sidebar");
  const closeMobileSidebar = document.getElementById("close-mobile-sidebar");

  if (mobileMenuBtn && mobileSidebar) {
    mobileMenuBtn.addEventListener("click", () => {
      mobileSidebar.classList.toggle("open");
    });
  }

  if (closeMobileSidebar) {
    closeMobileSidebar.addEventListener("click", (e) => {
      e.preventDefault();
      mobileSidebar.classList.remove("open");
    });
  }

  // Search form toggle
  const searchBtn = document.getElementById("search-btn");
  const searchForm = document.getElementById("search-form");

  if (searchBtn && searchForm) {
    searchBtn.addEventListener("click", () => {
      searchForm.classList.toggle("active");
    });

    // Close search when clicking outside
    document.addEventListener("click", (e) => {
      if (!searchForm.contains(e.target) && e.target !== searchBtn) {
        searchForm.classList.remove("active");
      }
    });
  }

  // Header scroll effect
  const topNav = document.getElementById("topNav");
  const leftsidebar = document.getElementById("left-sidebar");

  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      if (topNav) topNav.classList.add("scrolled");
      if (leftsidebar) leftsidebar.classList.add("scrolled");
    } else {
      if (topNav) topNav.classList.remove("scrolled");
      if (leftsidebar) leftsidebar.classList.remove("scrolled");
    }
  });

  // Set active nav item based on current page
  const currentLocation = window.location.pathname;
  const navItems = document.querySelectorAll(".top-nav ul li a");

  navItems.forEach((item) => {
    if (item.getAttribute("href") === currentLocation) {
      navItems.forEach((nav) => nav.classList.remove("active"));
      item.classList.add("active");
    }
  });

  // Notification Dropdown Functionality
  const notificationBtn = document.getElementById("notification-btn");
  const notificationDropdown = document.getElementById("notification-dropdown");

  if (notificationBtn && notificationDropdown) {
    notificationBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      notificationDropdown.classList.toggle("active");
    });

    // Close notification dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (
        !notificationDropdown.contains(e.target) &&
        e.target !== notificationBtn
      ) {
        notificationDropdown.classList.remove("active");
      }
    });
  }

  // Mark all as read functionality
  const markAllReadBtn = document.querySelector(".mark-all-read");
  if (markAllReadBtn) {
    markAllReadBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const unreadItems = document.querySelectorAll(
        ".notification-item.unread"
      );
      unreadItems.forEach((item) => {
        item.classList.remove("unread");
      });
      const notificationCount = document.getElementById("notification-count");
      if (notificationCount) {
        notificationCount.textContent = "0";
        notificationCount.style.display = "none";
      }
    });
  }

  // Mark individual notification as read
  const notificationItems = document.querySelectorAll(".notification-item");
  notificationItems.forEach((item) => {
    item.addEventListener("click", () => {
      if (item.classList.contains("unread")) {
        item.classList.remove("unread");
        updateNotificationCount();
      }
    });
  });

  function updateNotificationCount() {
    const unreadCount = document.querySelectorAll(
      ".notification-item.unread"
    ).length;
    const notificationCount = document.getElementById("notification-count");
    if (notificationCount) {
      notificationCount.textContent = unreadCount;
      if (unreadCount === 0) {
        notificationCount.style.display = "none";
      }
    }
  }
});
