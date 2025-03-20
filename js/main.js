document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const mobileMenuToggle = document.getElementById("mobile-menu-toggle")
  const mainNav = document.querySelector(".main-nav")

  if (mobileMenuToggle && mainNav) {
    mobileMenuToggle.addEventListener("click", function () {
      mainNav.classList.toggle("show")

      // Change icon based on menu state
      const icon = this.querySelector("i")
      if (icon) {
        if (mainNav.classList.contains("show")) {
          icon.classList.remove("fa-bars")
          icon.classList.add("fa-times")
        } else {
          icon.classList.remove("fa-times")
          icon.classList.add("fa-bars")
        }
      }
    })
  }

  // Set current year in footer
  const currentYearElement = document.getElementById("current-year")
  if (currentYearElement) {
    currentYearElement.textContent = new Date().getFullYear()
  }

  // Handle form submissions
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      // For demo purposes, prevent actual form submission
      if (!form.hasAttribute("data-real-submit")) {
        e.preventDefault()

        // Show success message or redirect based on form type
        const formType = form.getAttribute("data-form-type")
        if (formType === "search") {
          window.location.href = "listings.html"
        } else {
          alert("Form submitted successfully!")
        }
      }
    })
  })

  // Initialize tooltips if any
  const tooltips = document.querySelectorAll("[data-tooltip]")
  tooltips.forEach((tooltip) => {
    tooltip.addEventListener("mouseenter", function () {
      const tooltipText = this.getAttribute("data-tooltip")
      const tooltipElement = document.createElement("div")
      tooltipElement.className = "tooltip"
      tooltipElement.textContent = tooltipText
      document.body.appendChild(tooltipElement)

      const rect = this.getBoundingClientRect()
      tooltipElement.style.top = `${rect.top - tooltipElement.offsetHeight - 10}px`
      tooltipElement.style.left = `${rect.left + (rect.width / 2) - tooltipElement.offsetWidth / 2}px`
      tooltipElement.style.opacity = "1"

      this.addEventListener("mouseleave", () => {
        tooltipElement.remove()
      })
    })
  })
})

