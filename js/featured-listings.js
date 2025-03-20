/**
 * Featured Listings JavaScript
 * Handles client-side functionality for featured listings
 */
document.addEventListener("DOMContentLoaded", () => {
  // Cache DOM elements
  const featuredSection = document.querySelector(".featured-providers")
  const featuredFilter = document.getElementById("featured-filter")
  const featuredBadges = document.querySelectorAll(".featured-badge")

  // Function to fetch featured providers via AJAX
  function loadFeaturedProviders(limit = 6, categoryId = null) {
    // Show loading state
    if (featuredSection) {
      featuredSection.classList.add("loading")
    }

    // Build query parameters
    const params = new URLSearchParams()
    params.append("limit", limit)
    if (categoryId) {
      params.append("category", categoryId)
    }

    // Fetch data
    fetch(`/api/get_featured_providers.php?${params.toString()}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success && featuredSection) {
          // Update the featured providers section
          const providersContainer = featuredSection.querySelector(".grid")
          if (providersContainer && data.providers.length > 0) {
            // Clear existing content
            providersContainer.innerHTML = ""

            // Add new providers
            data.providers.forEach((provider) => {
              providersContainer.appendChild(createProviderCard(provider))
            })

            // Show the container
            providersContainer.closest(".container").classList.remove("hidden")

            // Hide the empty message if it exists
            const emptyMessage = featuredSection.querySelector(".text-center.py-8")
            if (emptyMessage) {
              emptyMessage.classList.add("hidden")
            }
          } else {
            // Show empty message
            providersContainer.innerHTML = `
                            <div class="text-center py-8 col-span-full">
                                <p>No featured providers available for this category.</p>
                            </div>
                        `
          }
        }
      })
      .catch((error) => {
        console.error("Error fetching featured providers:", error)
      })
      .finally(() => {
        // Remove loading state
        if (featuredSection) {
          featuredSection.classList.remove("loading")
        }
      })
  }

  // Function to create a provider card element
  function createProviderCard(provider) {
    const card = document.createElement("div")
    card.className = "provider-card featured"

    // Generate stars HTML
    let starsHtml = ""
    const fullStars = Math.floor(provider.rating)
    const halfStar = provider.rating - fullStars >= 0.5

    for (let i = 1; i <= 5; i++) {
      if (i <= fullStars) {
        starsHtml += '<i class="fas fa-star"></i>'
      } else if (halfStar && i === fullStars + 1) {
        starsHtml += '<i class="fas fa-star-half-alt"></i>'
      } else {
        starsHtml += '<i class="far fa-star"></i>'
      }
    }

    // Generate services HTML
    const servicesHtml = provider.services
      .map((service) => `<span class="service-tag">${escapeHtml(service)}</span>`)
      .join("")

    // Build card HTML
    card.innerHTML = `
            <div class="featured-badge">
                <span>Featured</span>
            </div>
            <div class="provider-header">
                <img src="${escapeHtml(provider.image)}" alt="${escapeHtml(provider.name)}" class="provider-image">
                <div class="provider-info">
                    <h3 class="provider-name">
                        ${escapeHtml(provider.name)}
                        ${provider.verified ? '<span class="verified-badge" title="Verified Provider"><i class="fas fa-check-circle"></i></span>' : ""}
                    </h3>
                    <div class="provider-rating">
                        <div class="stars">
                            ${starsHtml}
                        </div>
                        <span class="rating-text">${provider.rating} (${provider.reviewCount} reviews)</span>
                    </div>
                    <div class="provider-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${escapeHtml(provider.location)}</span>
                    </div>
                </div>
            </div>
            <div class="provider-services">
                <h4>Services:</h4>
                <div class="service-tags">
                    ${servicesHtml}
                </div>
            </div>
            <div class="provider-actions">
                <a href="provider-profile.php?id=${provider.id}" class="btn btn-outline">View Profile</a>
                <a href="booking.php?provider=${provider.id}" class="btn btn-primary">Book Now</a>
            </div>
        `

    return card
  }

  // Helper function to escape HTML
  function escapeHtml(unsafe) {
    return unsafe
      .toString()
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;")
  }

  // Handle featured filter change
  if (featuredFilter) {
    featuredFilter.addEventListener("change", function () {
      const listingsContainer = document.querySelector(".listings-container")

      if (this.checked) {
        listingsContainer.classList.add("show-featured-only")
      } else {
        listingsContainer.classList.remove("show-featured-only")
      }

      // Trigger any custom event listeners
      const event = new CustomEvent("featuredFilterChanged", {
        detail: { showFeaturedOnly: this.checked },
      })
      document.dispatchEvent(event)
    })
  }

  // Add animation to featured badges
  if (featuredBadges.length > 0) {
    featuredBadges.forEach((badge) => {
      // Add subtle animation
      badge.classList.add("animate-pulse")
    })
  }

  // Export functions for external use
  window.FeaturedListings = {
    load: loadFeaturedProviders,
  }
})

