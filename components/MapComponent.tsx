"use client"

import { useEffect, useRef } from "react"
import mapboxgl from "mapbox-gl"
import "mapbox-gl/dist/mapbox-gl.css"

mapboxgl.accessToken = process.env.NEXT_PUBLIC_MAPBOX_ACCESS_TOKEN

interface MapComponentProps {
  longitude: number
  latitude: number
  zoom?: number
  markers?: Array<{ longitude: number; latitude: number }>
}

export default function MapComponent({ longitude, latitude, zoom = 14, markers = [] }: MapComponentProps) {
  const mapContainer = useRef(null)
  const map = useRef(null)

  useEffect(() => {
    if (map.current) return // initialize map only once
    map.current = new mapboxgl.Map({
      container: mapContainer.current,
      style: "mapbox://styles/mapbox/streets-v11",
      center: [longitude, latitude],
      zoom: zoom,
    })

    // Add navigation control (zoom buttons)
    map.current.addControl(new mapboxgl.NavigationControl(), "top-right")

    // Add markers
    markers.forEach((marker) => {
      new mapboxgl.Marker().setLngLat([marker.longitude, marker.latitude]).addTo(map.current)
    })

    return () => map.current.remove()
  }, [longitude, latitude, zoom, markers])

  return <div ref={mapContainer} className="w-full h-full min-h-[400px] rounded-md" />
}

