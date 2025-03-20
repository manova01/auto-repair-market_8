import { Filter, MapPin, Search, Star, BadgeCheck } from "lucide-react"
import { Button } from "@/components/ui/button"
import FeaturedProviderCard from "@/components/featured-provider-card"
import MapComponent from "@/components/MapComponent"

export default function ListingsPage() {
  // Mock data for providers
  const providers = [
    {
      id: 1,
      name: "Mike's Auto Repair",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.8,
      reviewCount: 124,
      location: "San Francisco, CA",
      verified: true,
      services: ["Engine Repair", "Brake Service", "Oil Change"],
      latitude: 37.7749,
      longitude: -122.4194,
    },
    {
      id: 2,
      name: "Quick Fix Auto",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.6,
      reviewCount: 98,
      location: "Los Angeles, CA",
      verified: true,
      services: ["Tire Replacement", "Wheel Alignment", "Suspension"],
      latitude: 34.0522,
      longitude: -118.2437,
    },
    {
      id: 3,
      name: "Pro Mechanics",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.9,
      reviewCount: 156,
      location: "New York, NY",
      verified: true,
      services: ["Diagnostics", "Electrical Repair", "AC Service"],
      latitude: 40.7128,
      longitude: -74.006,
    },
    {
      id: 4,
      name: "Auto Care Center",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.5,
      reviewCount: 87,
      location: "Chicago, IL",
      verified: false,
      services: ["Oil Change", "Tune-Up", "Fluid Services"],
      latitude: 41.8781,
      longitude: -87.6298,
    },
    {
      id: 5,
      name: "Elite Auto Shop",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.7,
      reviewCount: 112,
      location: "Houston, TX",
      verified: true,
      services: ["Transmission Repair", "Engine Diagnostics", "Brake Service"],
      latitude: 29.7604,
      longitude: -95.3698,
    },
    {
      id: 6,
      name: "City Garage",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.4,
      reviewCount: 76,
      location: "Phoenix, AZ",
      verified: false,
      services: ["General Repair", "Maintenance", "Inspection"],
      latitude: 33.4484,
      longitude: -112.074,
    },
    {
      id: 7,
      name: "AutoBody Experts",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.7,
      reviewCount: 89,
      location: "Miami, FL",
      verified: true,
      services: ["Body Work", "Paint Jobs", "Dent Repair"],
      latitude: 25.7617,
      longitude: -80.1918,
    },
    {
      id: 8,
      name: "Electric Auto Solutions",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.6,
      reviewCount: 72,
      location: "Seattle, WA",
      verified: true,
      services: ["Auto Electrician", "Battery Service", "Electrical Diagnostics"],
      latitude: 47.6062,
      longitude: -122.3321,
    },
  ]

  const centerLat = providers.reduce((sum, provider) => sum + provider.latitude, 0) / providers.length
  const centerLng = providers.reduce((sum, provider) => sum + provider.longitude, 0) / providers.length

  return (
    <div className="container py-8">
      <h1 className="text-3xl font-bold">Find Auto Repair Services</h1>

      {/* Search and Filter Bar */}
      <div className="mt-6 flex flex-col gap-4 rounded-lg border bg-card p-4 shadow-sm md:flex-row">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
          <input
            type="text"
            placeholder="Search for services"
            className="w-full rounded-md border-0 py-2 pl-10 pr-4 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rudzz-blue"
          />
        </div>
        <div className="relative flex-1">
          <MapPin className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
          <input
            type="text"
            placeholder="Location"
            className="w-full rounded-md border-0 py-2 pl-10 pr-4 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rudzz-blue"
          />
        </div>
        <Button className="flex gap-2">
          <Filter className="h-4 w-4" />
          <span>Search</span>
        </Button>
      </div>

      {/* Main Content */}
      <div className="mt-8 flex flex-col gap-8 lg:flex-row">
        {/* Filters Sidebar */}
        <div className="w-full lg:w-64 shrink-0">
          <div className="rounded-lg border bg-card shadow-sm">
            <div className="flex items-center justify-between border-b p-4">
              <h2 className="font-semibold">Filters</h2>
              <Button variant="ghost" size="sm" className="h-8 text-xs">
                Reset
              </Button>
            </div>

            {/* Service Type Filter */}
            <div className="border-b p-4">
              <h3 className="mb-3 text-sm font-medium">Service Type</h3>
              <div className="space-y-2">
                {["Engine Repair", "Brake Service", "Oil Change", "Tire Services", "Body Work", "Auto Electrician"].map(
                  (service) => (
                    <div key={service} className="flex items-center">
                      <input
                        type="checkbox"
                        id={`service-${service}`}
                        className="h-4 w-4 rounded border-gray-300 text-rudzz-blue focus:ring-rudzz-blue"
                      />
                      <label htmlFor={`service-${service}`} className="ml-2 text-sm">
                        {service}
                      </label>
                    </div>
                  ),
                )}
              </div>
            </div>

            {/* Rating Filter */}
            <div className="border-b p-4">
              <h3 className="mb-3 text-sm font-medium">Rating</h3>
              <div className="space-y-2">
                {[5, 4, 3, 2, 1].map((rating) => (
                  <div key={rating} className="flex items-center">
                    <input
                      type="checkbox"
                      id={`rating-${rating}`}
                      className="h-4 w-4 rounded border-gray-300 text-rudzz-blue focus:ring-rudzz-blue"
                    />
                    <label htmlFor={`rating-${rating}`} className="ml-2 flex items-center text-sm">
                      {Array.from({ length: rating }).map((_, i) => (
                        <Star key={i} className="h-4 w-4 fill-rudzz-orange text-rudzz-orange" />
                      ))}
                      {Array.from({ length: 5 - rating }).map((_, i) => (
                        <Star key={i} className="h-4 w-4 text-muted-foreground" />
                      ))}
                      <span className="ml-1">& Up</span>
                    </label>
                  </div>
                ))}
              </div>
            </div>

            {/* Verification Filter */}
            <div className="p-4">
              <h3 className="mb-3 text-sm font-medium">Verification</h3>
              <div className="flex items-center">
                <input
                  type="checkbox"
                  id="verified-only"
                  className="h-4 w-4 rounded border-gray-300 text-rudzz-blue focus:ring-rudzz-blue"
                />
                <label htmlFor="verified-only" className="ml-2 flex items-center text-sm">
                  Verified Providers Only
                  <BadgeCheck className="ml-1 h-4 w-4 text-rudzz-green" />
                </label>
              </div>
            </div>
          </div>
        </div>

        {/* Listings */}
        <div className="flex-1">
          <div className="mb-4 flex items-center justify-between">
            <p className="text-sm text-muted-foreground">Showing {providers.length} results</p>
            <div className="flex items-center gap-2">
              <span className="text-sm">Sort by:</span>
              <select className="rounded-md border-gray-300 text-sm focus:border-rudzz-blue focus:ring-rudzz-blue">
                <option>Relevance</option>
                <option>Rating: High to Low</option>
                <option>Rating: Low to High</option>
              </select>
            </div>
          </div>

          {/* Map */}
          <div className="mb-8 h-[400px] rounded-lg overflow-hidden">
            <MapComponent
              longitude={centerLng}
              latitude={centerLat}
              zoom={5}
              markers={providers.map((p) => ({ longitude: p.longitude, latitude: p.latitude }))}
            />
          </div>

          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
            {providers.map((provider) => (
              <FeaturedProviderCard key={provider.id} provider={provider} />
            ))}
          </div>

          {/* Pagination */}
          <div className="mt-8 flex justify-center">
            <nav className="flex items-center gap-1">
              <Button variant="outline" size="sm" disabled>
                Previous
              </Button>
              <Button variant="outline" size="sm" className="h-8 w-8 p-0">
                1
              </Button>
              <Button variant="outline" size="sm" className="h-8 w-8 bg-rudzz-blue text-white p-0">
                2
              </Button>
              <Button variant="outline" size="sm" className="h-8 w-8 p-0">
                3
              </Button>
              <span className="mx-1">...</span>
              <Button variant="outline" size="sm" className="h-8 w-8 p-0">
                8
              </Button>
              <Button variant="outline" size="sm">
                Next
              </Button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  )
}

