document.addEventListener("DOMContentLoaded", () => {
  // Provider location data
  const providerLocation = {
    latitude: 37.7749,
    longitude: -122.4194,
  }

  // Get provider ID from URL if available
  const urlParams = new URLSearchParams(window.location.search)
  const providerId = urlParams.get("id")

  // If provider ID is present, we could load provider data from an API
  if (providerId) {
    console.log(`Loading provider with ID: ${providerId}`)
    // In a real application, you would fetch provider data from an API
    // and update the page content accordingly
  }

  // Initialize map using our map service
  const mapContainer = document.getElementById("map")
  if (mapContainer) {
    // Create map with provider location
    window.MapService.createMap("map", {
      latitude: providerLocation.latitude,
      longitude: providerLocation.longitude,
      zoom: 14,
    })
      .then((map) => {
        // Add marker for provider location
        map.addMarker(providerLocation.latitude, providerLocation.longitude, {
          title: "Provider Location",
          popup: true,
          popupContent: "<strong>Provider Location</strong>",
        })
      })
      .catch((error) => {
        console.error("Failed to initialize map:", error)
        // Display a fallback message if both map services fail
        mapContainer.innerHTML = `
        <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg p-4">
          <p class="text-gray-500">Map could not be loaded. Please try again later.</p>
        </div>
      `
      })
  }

  // Handle review form toggle
  const writeReviewBtn = document.getElementById("write-review-btn")
  const reviewFormContainer = document.getElementById("review-form-container")
  const cancelReviewBtn = document.getElementById("cancel-review")

  if (writeReviewBtn && reviewFormContainer) {
    writeReviewBtn.addEventListener("click", () => {
      reviewFormContainer.style.display = "block"
      writeReviewBtn.style.display = "none"
    })
  }

  if (cancelReviewBtn && reviewFormContainer && writeReviewBtn) {
    cancelReviewBtn.addEventListener("click", () => {
      reviewFormContainer.style.display = "none"
      writeReviewBtn.style.display = "inline-flex"
    })
  }

  // Handle review form submission
  const reviewForm = document.getElementById("review-form")
  if (reviewForm) {
    reviewForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Get form data
      const rating = document.querySelector('input[name="rating"]:checked').value
      const comment = document.getElementById("comment").value

      // Create new review element
      const reviewsList = document.querySelector(".reviews-list")
      const newReview = document.createElement("div")
      newReview.className = "review-item"
      newReview.innerHTML = `
        <div class="review-header">
          <h3>You</h3>
          <span>Just now</span>
        </div>
        <div class="review-rating">
          ${Array(Number.parseInt(rating)).fill('<i class="fas fa-star"></i>').join("")}
          ${Array(5 - Number.parseInt(rating))
            .fill('<i class="far fa-star"></i>')
            .join("")}
        </div>
        <p>${comment}</p>
      `

      // Add new review to the top of the list
      reviewsList.insertBefore(newReview, reviewsList.firstChild)

      // Reset form and hide it
      reviewForm.reset()
      reviewFormContainer.style.display = "none"
      writeReviewBtn.style.display = "inline-flex"

      // Show success message
      alert("Thank you for your review!")
    })
  }

  // Handle booking appointment
  const bookingButton = document.querySelector('a[href="booking.html?provider=1"]')
  if (bookingButton) {
    bookingButton.addEventListener("click", (e) => {
      e.preventDefault()
      alert("Booking functionality will be implemented soon!")
    })
  }

  // Handle view all reviews
  const viewAllButton = document.querySelector(".view-all .btn")
  if (viewAllButton) {
    viewAllButton.addEventListener("click", () => {
      alert("All reviews will be displayed soon!")
    })
  }
})

