document.addEventListener("DOMContentLoaded", () => {
  // Provider registration form validation
  const providerForm = document.getElementById("provider-registration-form")

  if (providerForm) {
    // Initialize Mapbox GL and Geocoder
    mapboxgl.accessToken = "YOUR_MAPBOX_ACCESS_TOKEN" // Replace with your actual token

    // Initialize geocoder for address lookup
    const geocoder = new MapboxGeocoder({
      accessToken: mapboxgl.accessToken,
      types: "address",
      countries: "us",
    })

    geocoder.addTo("#geocoder-container")

    // When an address is selected, fill in the form fields
    geocoder.on("result", (e) => {
      const result = e.result

      // Extract address components
      let address = ""
      let city = ""
      let state = ""
      let zipCode = ""

      if (result.context) {
        result.context.forEach((context) => {
          if (context.id.startsWith("postcode")) {
            zipCode = context.text
          } else if (context.id.startsWith("place")) {
            city = context.text
          } else if (context.id.startsWith("region")) {
            state = context.text
          }
        })
      }

      // Set address to the main text
      address = result.place_name.split(",")[0]

      // Fill form fields
      document.getElementById("address").value = address
      document.getElementById("city").value = city
      document.getElementById("state").value = state
      document.getElementById("zip_code").value = zipCode
      document.getElementById("latitude").value = result.center[1]
      document.getElementById("longitude").value = result.center[0]
    })

    // Form submission validation
    providerForm.addEventListener("submit", (e) => {
      let isValid = true

      // Check if coordinates are set
      const lat = document.getElementById("latitude").value
      const lng = document.getElementById("longitude").value

      if (!lat || !lng) {
        isValid = false
        alert("Please select a valid address from the dropdown.")
      }

      // Check business hours
      const openTimes = document.querySelectorAll('input[name^="open_time"]')
      const closeTimes = document.querySelectorAll('input[name^="close_time"]')
      const isClosed = document.querySelectorAll('input[name^="is_closed"]')

      for (let i = 0; i < openTimes.length; i++) {
        if (!isClosed[i].checked) {
          if (!openTimes[i].value || !closeTimes[i].value) {
            isValid = false
            alert("Please set both opening and closing times for all business days, or mark them as closed.")
            break
          }

          // Check if closing time is after opening time
          const openTime = new Date(`2000-01-01T${openTimes[i].value}`)
          const closeTime = new Date(`2000-01-01T${closeTimes[i].value}`)

          if (closeTime <= openTime) {
            isValid = false
            alert("Closing time must be after opening time.")
            break
          }
        }
      }

      if (!isValid) {
        e.preventDefault()
      }
    })

    // Toggle business hours inputs based on "Closed" checkbox
    const closedCheckboxes = document.querySelectorAll('input[name^="is_closed"]')
    closedCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        const dayIndex = this.getAttribute("data-day-index")
        const openTimeInput = document.querySelector(`input[name="open_time[${dayIndex}]"]`)
        const closeTimeInput = document.querySelector(`input[name="close_time[${dayIndex}]"]`)

        if (this.checked) {
          openTimeInput.disabled = true
          closeTimeInput.disabled = true
        } else {
          openTimeInput.disabled = false
          closeTimeInput.disabled = false
        }
      })
    })
  }

  // Service price validation
  const priceInputs = document.querySelectorAll('input[type="number"][data-validate="price"]')
  priceInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const min = Number.parseFloat(this.getAttribute("min") || 0)
      const max = Number.parseFloat(this.getAttribute("max") || Number.POSITIVE_INFINITY)
      const value = Number.parseFloat(this.value)

      if (value < min) {
        this.value = min
      } else if (value > max) {
        this.value = max
      }
    })
  })

  // Review form validation
  const reviewForm = document.getElementById("review-form")
  if (reviewForm) {
    const ratingInputs = document.querySelectorAll('input[name="rating"]')
    const submitButton = reviewForm.querySelector('button[type="submit"]')

    // Disable submit button if no rating is selected
    submitButton.disabled = true

    ratingInputs.forEach((input) => {
      input.addEventListener("change", () => {
        submitButton.disabled = false
      })
    })

    reviewForm.addEventListener("submit", (e) => {
      const rating = document.querySelector('input[name="rating"]:checked')
      const comment = document.querySelector('textarea[name="comment"]')

      if (!rating) {
        e.preventDefault()
        alert("Please select a rating.")
      }

      if (!comment.value.trim()) {
        e.preventDefault()
        alert("Please enter a comment.")
      }
    })
  }
})

