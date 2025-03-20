document.addEventListener("DOMContentLoaded", () => {
  // Date picker configuration
  const dateInput = document.getElementById("appointment-date")

  if (dateInput) {
    // Set min date to today
    const today = new Date()
    const yyyy = today.getFullYear()
    const mm = String(today.getMonth() + 1).padStart(2, "0")
    const dd = String(today.getDate()).padStart(2, "0")

    dateInput.min = `${yyyy}-${mm}-${dd}`

    // Set max date to 3 months from now
    const maxDate = new Date()
    maxDate.setMonth(maxDate.getMonth() + 3)

    const maxYyyy = maxDate.getFullYear()
    const maxMm = String(maxDate.getMonth() + 1).padStart(2, "0")
    const maxDd = String(maxDate.getDate()).padStart(2, "0")

    dateInput.max = `${maxYyyy}-${maxMm}-${maxDd}`
  }

  // Service type change handler
  const serviceTypeSelect = document.getElementById("service-type")

  if (serviceTypeSelect) {
    serviceTypeSelect.addEventListener("change", function () {
      // In a real application, this would update available time slots
      // based on the selected service type
      console.log(`Selected service: ${this.value}`)
    })
  }

  // Flexible time checkbox handler
  const flexibleTimeCheckbox = document.getElementById("flexible-time")
  const appointmentTimeSelect = document.getElementById("appointment-time")

  if (flexibleTimeCheckbox && appointmentTimeSelect) {
    flexibleTimeCheckbox.addEventListener("change", function () {
      if (this.checked) {
        appointmentTimeSelect.disabled = true
        appointmentTimeSelect.value = ""
      } else {
        appointmentTimeSelect.disabled = false
      }
    })
  }

  // Form submission
  const bookingForm = document.getElementById("booking-form")

  if (bookingForm) {
    bookingForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Simulate form submission
      alert("Booking submitted successfully! Redirecting to confirmation page...")
      window.location.href = "booking-confirmation.html"
    })
  }

  // Get URL parameters
  const urlParams = new URLSearchParams(window.location.search)
  const providerId = urlParams.get("provider")

  if (providerId) {
    console.log(`Loading provider with ID: ${providerId}`)
    // In a real application, you would fetch provider data from an API
    // and update the page content accordingly
  }
})

