document.addEventListener("DOMContentLoaded", () => {
  // Sample provider locations
  const providers = [
    {
      id: 1,
      name: "Mike's Auto Repair",
      coordinates: { lat: 37.7749, lng: -122.4194 },
      rating: 4.8,
    },
    {
      id: 2,
      name: "AutoFix Pro",
      coordinates: { lat: 37.7814, lng: -122.4284 },
      rating: 5.0,
    },
    {
      id: 3,
      name: "Quick Auto Service",
      coordinates: { lat: 37.7834, lng: -122.4104 },
      rating: 4.0,
    },
  ]

  // Initialize map using our map service
  const mapContainer = document.getElementById("listings-map")
  if (mapContainer) {
    // Create map centered on San Francisco
    window.MapService.createMap("listings-map", {
      latitude: 37.7749,
      longitude: -122.4194,
      zoom: 12,
    })
      .then((map) => {
        // Add markers for each provider
        const bounds = providers.map((provider) => ({
          lat: provider.coordinates.lat,
          lng: provider.coordinates.lng,
        }))

        providers.forEach((provider) => {
          // Create a custom marker element
          const el = document.createElement("div")
          el.className = "custom-marker"
          el.style.backgroundColor = "#3b82f6"
          el.style.width = "25px"
          el.style.height = "25px"
          el.style.borderRadius = "50%"
          el.style.display = "flex"
          el.style.alignItems = "center"
          el.style.justifyContent = "center"
          el.style.color = "white"
          el.style.fontWeight = "bold"
          el.style.fontSize = "12px"
          el.textContent = provider.rating.toFixed(1)

          // Add marker to map
          map.addMarker(provider.coordinates.lat, provider.coordinates.lng, {
            title: provider.name,
            customElement: el,
            popup: true,
            popupContent: `
              <h3>${provider.name}</h3>
              <p>Rating: ${provider.rating}</p>
              <a href="provider-profile.html?id=${provider.id}" class="btn btn-sm btn-primary">View Profile</a>
            `,
          })
        })

        // Fit map to bounds
        if (bounds.length > 0) {
          map.fitBounds(bounds)
        }
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

  // Handle filter application
  const applyFiltersBtn = document.getElementById("apply-filters")
  if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener("click", () => {
      // In a real application, this would filter the results
      alert("Filters applied! This would refresh the results in a real application.")
    })
  }

  // Handle filter reset
  const resetFiltersBtn = document.getElementById("reset-filters")
  if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener("click", () => {
      // Reset all filter inputs
      document.getElementById("service-type").value = ""
      document.getElementById("rating").value = "0"
      document.getElementById("distance").value = "25"

      const checkboxes = document.querySelectorAll('input[type="checkbox"]')
      checkboxes.forEach((checkbox) => {
        checkbox.checked = false
      })

      alert("Filters have been reset!")
    })
  }

  // Handle sort change
  const sortSelect = document.getElementById("sort-by")
  if (sortSelect) {
    sortSelect.addEventListener("change", function () {
      // In a real application, this would sort the results
      alert(`Results sorted by ${this.options[this.selectedIndex].text}!`)
    })
  }

  // Handle pagination
  const paginationBtns = document.querySelectorAll(".pagination-btn:not([disabled])")
  paginationBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Remove active class from all buttons
      paginationBtns.forEach((b) => b.classList.remove("active"))

      // Add active class to clicked button
      this.classList.add("active")

      // In a real application, this would load the corresponding page
      if (this.textContent) {
        alert(`Navigating to page ${this.textContent}!`)
      }
    })
  })

  // Get URL parameters
  const urlParams = new URLSearchParams(window.location.search)
  const category = urlParams.get("category")

  // If category is present, we could filter the listings
  if (category) {
    console.log(`Filtering by category: ${category}`)
    // In a real application, you would filter the listings based on the category
    // and update the page content accordingly

    // Update the service type dropdown
    const serviceTypeSelect = document.getElementById("service-type")
    if (serviceTypeSelect) {
      // Find the option that matches the category
      Array.from(serviceTypeSelect.options).forEach((option) => {
        if (option.value === category) {
          option.selected = true
        }
      })
    }
  }
})

