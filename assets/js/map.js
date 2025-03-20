document.addEventListener("DOMContentLoaded", () => {
  // Initialize Mapbox
  if (typeof mapboxgl === "undefined") {
    console.error("Mapbox GL JS library is not loaded. Please ensure it is included in your HTML.")
    return
  }

  if (typeof mapboxApiKey === "undefined") {
    console.error("Mapbox API key is not defined. Please set the mapboxApiKey variable in your HTML.")
    return
  }
  mapboxgl.accessToken = mapboxApiKey // This variable should be set in the page that includes this script

  // Provider profile map
  const providerMap = document.getElementById("provider-map")
  if (providerMap) {
    const lat = Number.parseFloat(providerMap.getAttribute("data-lat"))
    const lng = Number.parseFloat(providerMap.getAttribute("data-lng"))

    if (lat && lng) {
      const map = new mapboxgl.Map({
        container: "provider-map",
        style: "mapbox://styles/mapbox/streets-v11",
        center: [lng, lat],
        zoom: 14,
      })

      // Add marker
      new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map)

      // Add navigation controls
      map.addControl(new mapboxgl.NavigationControl(), "top-right")
    }
  }

  // Listings map
  const listingsMap = document.getElementById("listings-map")
  if (listingsMap) {
    const map = new mapboxgl.Map({
      container: "listings-map",
      style: "mapbox://styles/mapbox/streets-v11",
      center: [-122.4194, 37.7749], // Default to San Francisco
      zoom: 12,
    })

    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl(), "top-right")

    // Add markers for providers
    if (typeof providers !== "undefined" && providers.length > 0) {
      // Create bounds to fit all markers
      const bounds = new mapboxgl.LngLatBounds()

      providers.forEach((provider) => {
        // Create custom marker element
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
        new mapboxgl.Marker(el)
          .setLngLat([provider.lng, provider.lat])
          .setPopup(
            new mapboxgl.Popup({ offset: 25 }).setHTML(`
              <h3>${provider.name}</h3>
              <p>Rating: ${provider.rating} (${provider.reviewCount} reviews)</p>
              <a href="provider-profile.php?id=${provider.id}" class="btn btn-sm btn-primary">View Profile</a>
            `),
          )
          .addTo(map)

        // Extend bounds to include this marker
        bounds.extend([provider.lng, provider.lat])
      })

      // Fit map to bounds
      map.fitBounds(bounds, {
        padding: 50,
        maxZoom: 15,
      })
    } else {
      console.warn("No providers data available or providers is not an array.")
    }
  }
})

