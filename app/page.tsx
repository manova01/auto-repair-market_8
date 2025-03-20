import Link from "next/link"
import {
  Search,
  MapPin,
  Wrench,
  PenToolIcon as Tool,
  Truck,
  Settings,
  Star,
  ArrowRight,
  MessageSquare,
  Car,
  Zap,
} from "lucide-react"
import { Button } from "@/components/ui/button"
import ServiceCategoryCard from "@/components/service-category-card"
import FeaturedProviderCard from "@/components/featured-provider-card"

export default function Home() {
  const categories = [
    {
      id: 1,
      name: "Engine Repair",
      icon: <Settings className="h-8 w-8 text-rudzz-blue" />,
      count: 124,
    },
    {
      id: 2,
      name: "Tire Services",
      icon: <Truck className="h-8 w-8 text-rudzz-blue" />,
      count: 98,
    },
    {
      id: 3,
      name: "Brake Repair",
      icon: <Tool className="h-8 w-8 text-rudzz-blue" />,
      count: 87,
    },
    {
      id: 4,
      name: "Oil Change",
      icon: <Wrench className="h-8 w-8 text-rudzz-blue" />,
      count: 156,
    },
    {
      id: 5,
      name: "Body Work",
      icon: <Car className="h-8 w-8 text-rudzz-blue" />,
      count: 72,
    },
    {
      id: 6,
      name: "Auto Electrician",
      icon: <Zap className="h-8 w-8 text-rudzz-blue" />,
      count: 63,
    },
  ]

  const featuredProviders = [
    {
      id: 1,
      name: "Mike's Auto Repair",
      image: "/placeholder.svg?height=100&width=100",
      rating: 4.8,
      reviewCount: 124,
      location: "San Francisco, CA",
      verified: true,
      services: ["Engine Repair", "Brake Service", "Oil Change"],
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
    },
  ]

  return (
    <div className="flex flex-col">
      {/* Hero Section */}
      <section className="relative bg-gradient-to-r from-rudzz-blue to-rudzz-green py-16 md:py-24">
        <div className="container relative z-10 flex flex-col items-center text-center">
          <h1 className="max-w-3xl text-3xl font-bold tracking-tight text-white sm:text-4xl md:text-5xl lg:text-6xl">
            Find Trusted Auto Repair Professionals Near You
          </h1>
          <p className="mt-4 max-w-2xl text-lg text-white/90 md:text-xl">
            Connect with verified mechanics and service providers for all your automotive needs
          </p>

          {/* Search Bar */}
          <div className="mt-8 w-full max-w-3xl rounded-lg bg-white p-2 shadow-lg">
            <div className="flex flex-col gap-2 md:flex-row">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                <input
                  type="text"
                  placeholder="Search for services (e.g. oil change, brake repair)"
                  className="w-full rounded-md border-0 py-2 pl-10 pr-4 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rudzz-blue"
                />
              </div>
              <div className="relative flex-1">
                <MapPin className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                <input
                  type="text"
                  placeholder="Location (city, zip code)"
                  className="w-full rounded-md border-0 py-2 pl-10 pr-4 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rudzz-blue"
                />
              </div>
              <Button className="bg-rudzz-blue hover:bg-rudzz-blue/90">Search</Button>
            </div>
          </div>

          <div className="mt-6 flex flex-wrap items-center justify-center gap-2 text-sm text-white">
            <span>Popular:</span>
            <Link
              href="/listings?category=engine-repair"
              className="rounded-full bg-white/20 px-3 py-1 hover:bg-white/30"
            >
              Engine Repair
            </Link>
            <Link
              href="/listings?category=tire-replacement"
              className="rounded-full bg-white/20 px-3 py-1 hover:bg-white/30"
            >
              Tire Replacement
            </Link>
            <Link href="/listings?category=oil-change" className="rounded-full bg-white/20 px-3 py-1 hover:bg-white/30">
              Oil Change
            </Link>
          </div>
        </div>

        {/* Decorative elements */}
        <div className="absolute inset-0 overflow-hidden">
          <div className="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-rudzz-orange/20 blur-3xl"></div>
          <div className="absolute -bottom-20 -left-20 h-80 w-80 rounded-full bg-rudzz-green/20 blur-3xl"></div>
        </div>
      </section>

      {/* How It Works Section */}
      <section className="py-16">
        <div className="container">
          <div className="text-center">
            <h2 className="text-3xl font-bold tracking-tight md:text-4xl">How It Works</h2>
            <p className="mt-4 text-lg text-muted-foreground">
              Find and connect with auto repair professionals in just a few simple steps
            </p>
          </div>

          <div className="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
            <div className="flex flex-col items-center text-center">
              <div className="flex h-16 w-16 items-center justify-center rounded-full bg-rudzz-blue/10 text-rudzz-blue">
                <Search className="h-8 w-8" />
              </div>
              <h3 className="mt-4 text-xl font-semibold">Search</h3>
              <p className="mt-2 text-muted-foreground">
                Search for auto repair services by location, service type, or keywords
              </p>
            </div>

            <div className="flex flex-col items-center text-center">
              <div className="flex h-16 w-16 items-center justify-center rounded-full bg-rudzz-green/10 text-rudzz-green">
                <Star className="h-8 w-8" />
              </div>
              <h3 className="mt-4 text-xl font-semibold">Choose</h3>
              <p className="mt-2 text-muted-foreground">
                Compare providers based on ratings, reviews, and verification status
              </p>
            </div>

            <div className="flex flex-col items-center text-center">
              <div className="flex h-16 w-16 items-center justify-center rounded-full bg-rudzz-orange/10 text-rudzz-orange">
                <MessageSquare className="h-8 w-8" />
              </div>
              <h3 className="mt-4 text-xl font-semibold">Connect</h3>
              <p className="mt-2 text-muted-foreground">
                Message providers directly to discuss your needs and book services
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Service Categories Section */}
      <section className="bg-muted/30 py-16">
        <div className="container">
          <div className="flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
              <h2 className="text-3xl font-bold tracking-tight md:text-4xl">Service Categories</h2>
              <p className="mt-4 text-lg text-muted-foreground">Browse auto repair services by category</p>
            </div>
            <Link href="/categories" className="mt-4 flex items-center text-rudzz-blue hover:underline md:mt-0">
              View All Categories <ArrowRight className="ml-1 h-4 w-4" />
            </Link>
          </div>

          <div className="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
            {categories.map((category) => (
              <ServiceCategoryCard key={category.id} category={category} />
            ))}
          </div>
        </div>
      </section>

      {/* Featured Providers Section */}
      <section className="py-16">
        <div className="container">
          <div className="flex flex-col md:flex-row md:items-end md:justify-between">
            <div>
              <h2 className="text-3xl font-bold tracking-tight md:text-4xl">Featured Providers</h2>
              <p className="mt-4 text-lg text-muted-foreground">Top-rated auto repair professionals in your area</p>
            </div>
            <Link href="/listings" className="mt-4 flex items-center text-rudzz-blue hover:underline md:mt-0">
              View All Providers <ArrowRight className="ml-1 h-4 w-4" />
            </Link>
          </div>

          <div className="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
            {featuredProviders.map((provider) => (
              <FeaturedProviderCard key={provider.id} provider={provider} />
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="bg-rudzz-blue py-16">
        <div className="container">
          <div className="flex flex-col items-center text-center">
            <h2 className="text-3xl font-bold tracking-tight text-white md:text-4xl">
              Join Our Network of Auto Repair Professionals
            </h2>
            <p className="mt-4 max-w-2xl text-lg text-white/90 md:text-xl">
              Grow your business by connecting with customers looking for your services
            </p>
            <div className="mt-8 flex flex-col gap-4 sm:flex-row">
              <Link href="/signup?type=provider">
                <Button size="lg" className="bg-white text-rudzz-blue hover:bg-white/90">
                  Register as a Provider
                </Button>
              </Link>
              <Link href="/about">
                <Button size="lg" variant="outline" className="border-white text-white hover:bg-white/10">
                  Learn More
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}

