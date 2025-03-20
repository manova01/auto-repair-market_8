/**
 * Map Service Utility
 * Attempts to load Google Maps first, falls back to Mapbox if Google Maps fails
 */

// Configuration
const MAP_CONFIG = {
  // Google Maps configuration
  google: {
    apiKey: "YOUR_GOOGLE_MAPS_API_KEY", // Replace with your Google Maps API key
    timeout: 5000, // Timeout in milliseconds to wait for Google Maps to load
  },
  // Mapbox configuration
  mapbox: {
    apiKey: "pk.eyJ1Ijoicm9iMjMiLCJhIjoiY2tvbzViOHdsMDg1bTJvcGljbHp0ZTZrYyJ9.12KbwskPePI6RYd0K6E5Ew",
    style: "mapbox://styles/mapbox/streets-v11",
  },
}

// Map service state
const mapService = {
  provider: null, // 'google' or 'mapbox'
  isLoaded: false,
  isLoading: false,
  loadPromise: null,
}

/**
 * Load the map service (Google Maps or Mapbox)
 * @returns {Promise} Resolves when a map service is successfully loaded
 */
function loadMapService() {
  // If already loaded or loading, return the existing promise
  if (mapService.isLoaded) {
    return Promise.resolve(mapService.provider)
  }

  if (mapService.isLoading) {
    return mapService.loadPromise
  }

  // Start loading
  mapService.isLoading = true

  // Create a promise to track loading
  mapService.loadPromise = new Promise((resolve, reject) => {
    // Try Google Maps first
    tryLoadGoogleMaps()
      .then(() => {
        mapService.provider = "google"
        mapService.isLoaded = true
        mapService.isLoading = false
        console.log("Using Google Maps")
        resolve("google")
      })
      .catch((error) => {
        console.warn("Google Maps failed to load:", error)
        console.log("Falling back to Mapbox...")

        // Fall back to Mapbox
        tryLoadMapbox()
          .then(() => {
            mapService.provider = "mapbox"
            mapService.isLoaded = true
            mapService.isLoading = false
            console.log("Using Mapbox")
            resolve("mapbox")
          })
          .catch((mapboxError) => {
            mapService.isLoading = false
            console.error("Both Google Maps and Mapbox failed to load:", mapboxError)
            reject("Failed to load any map service")
          })
      })
  })

  return mapService.loadPromise
}

/**
 * Try to load Google Maps with a timeout
 * @returns {Promise} Resolves when Google Maps is loaded, rejects on timeout or error
 */
function tryLoadGoogleMaps() {
  return new Promise((resolve, reject) => {
    // Check if Google Maps is already loaded
    if (window.google && window.google.maps) {
      return resolve()
    }

    // Set a timeout for Google Maps loading
    const timeoutId = setTimeout(() => {
      reject("Google Maps loading timed out")
    }, MAP_CONFIG.google.timeout)

    // Create a callback for when Google Maps loads
    window.googleMapsCallback = () => {
      clearTimeout(timeoutId)
      resolve()
    }

    // Create and append the Google Maps script
    const script = document.createElement("script")
    script.src = `https://maps.googleapis.com/maps/api/js?key=${MAP_CONFIG.google.apiKey}&callback=googleMapsCallback`
    script.async = true
    script.onerror = () => {
      clearTimeout(timeoutId)
      reject("Google Maps script failed to load")
    }

    document.head.appendChild(script)
  })
}

/**
 * Try to load Mapbox
 * @returns {Promise} Resolves when Mapbox is loaded, rejects on error
 */
function tryLoadMapbox() {
  return new Promise((resolve, reject) => {
    // Check if Mapbox is already loaded
    if (window.mapboxgl) {
      window.mapboxgl.accessToken = MAP_CONFIG.mapbox.apiKey
      return resolve()
    }

    // Load Mapbox CSS
    const mapboxCss = document.createElement("link")
    mapboxCss.rel = "stylesheet"
    mapboxCss.href = "https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css"
    document.head.appendChild(mapboxCss)

    // Load Mapbox JS
    const script = document.createElement("script")
    script.src = "https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"
    script.async = true

    script.onload = () => {
      window.mapboxgl.accessToken = MAP_CONFIG.mapbox.apiKey
      resolve()
    }

    script.onerror = () => {
      reject("Mapbox script failed to load")
    }

    document.head.appendChild(script)
  })
}

/**
 * Create a map instance using the loaded map service
 * @param {string} containerId - The ID of the container element
 * @param {Object} options - Map options
 * @returns {Promise<Object>} Resolves with the map instance
 */
function createMap(containerId, options = {}) {
  return loadMapService().then((provider) => {
    const container = document.getElementById(containerId)
    if (!container) {
      throw new Error(`Map container with ID "${containerId}" not found`)
    }

    const defaultOptions = {
      center: { lat: options.latitude || 37.7749, lng: options.longitude || -122.4194 },
      zoom: options.zoom || 12,
    }

    if (provider === "google") {
      return createGoogleMap(container, { ...defaultOptions, ...options })
    } else {
      return createMapboxMap(container, { ...defaultOptions, ...options })
    }
  })
}

/**
 * Create a Google Map instance
 * @param {HTMLElement} container - The container element
 * @param {Object} options - Map options
 * @returns {Object} The Google Map instance and utility methods
 */
function createGoogleMap(container, options) {
  const map = new window.google.maps.Map(container, {
    center: options.center,
    zoom: options.zoom,
    mapTypeId: window.google.maps.MapTypeId.ROADMAP,
    mapTypeControl: options.mapTypeControl || false,
    streetViewControl: options.streetViewControl || false,
    fullscreenControl: options.fullscreenControl || true,
    zoomControl: options.zoomControl || true,
  })

  // Return map instance with unified API
  return {
    instance: map,
    provider: "google",

    // Add a marker
    addMarker: (lat, lng, options = {}) => {
      const marker = new window.google.maps.Marker({
        position: { lat, lng },
        map: map,
        title: options.title || "",
        icon: options.icon || null,
      })

      if (options.popup && options.popupContent) {
        const infoWindow = new window.google.maps.InfoWindow({
          content: options.popupContent,
        })

        marker.addListener("click", () => {
          infoWindow.open(map, marker)
        })
      }

      return marker
    },

    // Fit bounds
    fitBounds: (bounds) => {
      const googleBounds = new window.google.maps.LatLngBounds()
      bounds.forEach((point) => {
        googleBounds.extend(new window.google.maps.LatLng(point.lat, point.lng))
      })
      map.fitBounds(googleBounds, 50) // 50px padding
    },

    // Add controls
    addControls: () => {
      map.controls[window.google.maps.ControlPosition.TOP_RIGHT].push(new window.google.maps.ZoomControl())
    },

    // Set center
    setCenter: (lat, lng) => {
      map.setCenter({ lat, lng })
    },

    // Set zoom
    setZoom: (zoom) => {
      map.setZoom(zoom)
    },
  }
}

/**
 * Create a Mapbox map instance
 * @param {HTMLElement} container - The container element
 * @param {Object} options - Map options
 * @returns {Object} The Mapbox map instance and utility methods
 */
function createMapboxMap(container, options) {
  const map = new mapboxgl.Map({
    container: container,
    style: MAP_CONFIG.mapbox.style,
    center: [options.center.lng, options.center.lat],
    zoom: options.zoom,
  })

  // Add navigation control
  if (options.zoomControl !== false) {
    map.addControl(new mapboxgl.NavigationControl(), "top-right")
  }

  // Return map instance with unified API
  return {
    instance: map,
    provider: "mapbox",

    // Add a marker
    addMarker: (lat, lng, options = {}) => {
      // Create custom marker element if specified
      let element
      if (options.customElement) {
        element = options.customElement
      }

      const marker = new mapboxgl.Marker(element).setLngLat([lng, lat]).addTo(map)

      if (options.popup && options.popupContent) {
        const popup = new mapboxgl.Popup({ offset: 25 }).setHTML(options.popupContent)

        marker.setPopup(popup)
      }

      return marker
    },

    // Fit bounds
    fitBounds: (bounds) => {
      const mapboxBounds = new mapboxgl.LngLatBounds()
      bounds.forEach((point) => {
        mapboxBounds.extend([point.lng, point.lat])
      })
      map.fitBounds(mapboxBounds, { padding: 50 })
    },

    // Add controls
    addControls: () => {
      map.addControl(new mapboxgl.NavigationControl(), "top-right")
    },

    // Set center
    setCenter: (lat, lng) => {
      map.setCenter([lng, lat])
    },

    // Set zoom
    setZoom: (zoom) => {
      map.setZoom(zoom)
    },
  }
}

// Export the map service API
window.MapService = {
  loadMapService,
  createMap,
  getProvider: () => mapService.provider,
  isLoaded: () => mapService.isLoaded,
}

