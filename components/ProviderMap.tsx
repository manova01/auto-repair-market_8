"use client"

import { useEffect, useRef } from "react"
import mapboxgl from "mapbox-gl"
import "mapbox-gl/dist/mapbox-gl.css"

mapboxgl.accessToken = process.env.NEXT_PUBLIC_MAPBOX_ACCESS_TOKEN || ""

interface ProviderMapProps {
  longitude: number
  latitude: number
}

export function ProviderMap({ longitude, latitude }: ProviderMapProps) {
  const mapContainer = useRef<HTMLDivElement>(null)
  const map = useRef<mapboxgl.Map | null>(null)

  useEffect(() => {
    if (map.current) return // initialize map only once
    if (!mapContainer.current) return // wait for the mapContainer to be available

    map.current = new mapboxgl.Map({
      container: mapContainer.current,
      style: "mapbox://styles/mapbox/streets-v11",
      center: [longitude, latitude],
      zoom: 14,
    })

    new mapboxgl.Marker().setLngLat([longitude, latitude]).addTo(map.current)

    return () => {
      if (map.current) {
        map.current.remove()
      }
    }
  }, [longitude, latitude])

  return <div ref={mapContainer} className="w-full h-full min-h-[400px] rounded-md" />
}

