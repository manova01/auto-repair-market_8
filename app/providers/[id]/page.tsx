import Image from "next/image"
import Link from "next/link"
import { BadgeCheck, Calendar, Clock, MapPin, MessageSquare, Phone } from "lucide-react"
import { Button } from "@/components/ui/button"
import { StarRating } from "@/components/StarRating"
import { ServiceItem } from "@/components/ServiceItem"
import { ReviewItem } from "@/components/ReviewItem"
import { ProviderMap } from "@/components/ProviderMap"

// Mock data - in a real app, this would come from an API call
const provider = {
  id: 1,
  name: "Mike's Auto Repair",
  image: "/placeholder.svg?height=200&width=200",
  coverImage: "/placeholder.svg?height=300&width=1200",
  rating: 4.8,
  reviewCount: 124,
  location: "123 Main St, San Francisco, CA 94105",
  verified: true,
  description:
    "With over 15 years of experience, we provide top-quality auto repair services for all makes and models. Our certified technicians use the latest diagnostic equipment to ensure your vehicle is repaired correctly the first time.",
  services: [
    { name: "Engine Repair", price: "$150-$500" },
    { name: "Brake Service", price: "$80-$250" },
    { name: "Oil Change", price: "$40-$80" },
    { name: "Tire Replacement", price: "$60-$200 per tire" },
    { name: "Wheel Alignment", price: "$80-$120" },
    { name: "AC Service", price: "$100-$300" },
    { name: "Body Work", price: "$500-$2000" },
    { name: "Auto Electrical", price: "$80-$400" },
  ],
  hours: [
    { day: "Monday", hours: "8:00 AM - 6:00 PM" },
    { day: "Tuesday", hours: "8:00 AM - 6:00 PM" },
    { day: "Wednesday", hours: "8:00 AM - 6:00 PM" },
    { day: "Thursday", hours: "8:00 AM - 6:00 PM" },
    { day: "Friday", hours: "8:00 AM - 6:00 PM" },
    { day: "Saturday", hours: "9:00 AM - 4:00 PM" },
    { day: "Sunday", hours: "Closed" },
  ],
  reviews: [
    {
      id: 1,
      user: "John D.",
      rating: 5,
      date: "2 weeks ago",
      comment: "Great service! They fixed my car quickly and at a reasonable price. Would definitely recommend.",
    },
    {
      id: 2,
      user: "Sarah M.",
      rating: 4,
      date: "1 month ago",
      comment: "Professional and knowledgeable staff. They explained everything clearly before starting the work.",
    },
    {
      id: 3,
      user: "Robert L.",
      rating: 5,
      date: "2 months ago",
      comment: "Excellent service. They diagnosed and fixed an issue that two other shops couldn't figure out.",
    },
  ],
  phoneNumber: "555-123-4567",
  latitude: 37.7749,
  longitude: -122.4194,
}

export default function ProviderProfilePage({ params }: { params: { id: string } }) {
  return (
    <div className="container py-8">
      {/* Cover Image */}
      <div className="relative h-48 w-full overflow-hidden rounded-lg md:h-64">
        <Image
          src={provider.coverImage || "/placeholder.svg"}
          alt={`${provider.name} cover`}
          fill
          className="object-cover"
        />
      </div>

      {/* Provider Info */}
      <div className="relative -mt-16 flex flex-col items-center rounded-lg border bg-card p-6 shadow-sm md:flex-row md:items-start">
        <div className="relative h-32 w-32 overflow-hidden rounded-full border-4 border-background md:h-40 md:w-40">
          <Image src={provider.image || "/placeholder.svg"} alt={provider.name} fill className="object-cover" />
        </div>

        <div className="mt-4 flex flex-1 flex-col items-center text-center md:ml-6 md:items-start md:text-left">
          <div className="flex items-center gap-2">
            <h1 className="text-2xl font-bold md:text-3xl">{provider.name}</h1>
            {provider.verified && <BadgeCheck className="h-6 w-6 text-rudzz-green" />}
          </div>

          <div className="mt-2 flex items-center gap-1">
            <StarRating rating={provider.rating} />
            <span className="font-medium">{provider.rating}</span>
            <span className="text-muted-foreground">({provider.reviewCount} reviews)</span>
          </div>

          <div className="mt-2 flex items-center gap-1 text-muted-foreground">
            <MapPin className="h-5 w-5" />
            <span>{provider.location}</span>
          </div>
        </div>

        <div className="mt-6 flex w-full flex-col gap-3 md:mt-0 md:w-auto">
          <Link href={`tel:${provider.phoneNumber}`}>
            <Button className="w-full md:w-auto">
              <Phone className="mr-2 h-4 w-4" />
              Call Now
            </Button>
          </Link>
          <Link href={`/messages/new?provider=${provider.id}`}>
            <Button variant="outline" className="w-full md:w-auto">
              <MessageSquare className="mr-2 h-4 w-4" />
              Message
            </Button>
          </Link>
          <Button variant="outline" className="w-full md:w-auto">
            <Calendar className="mr-2 h-4 w-4" />
            Book Appointment
          </Button>
        </div>
      </div>

      {/* Main Content */}
      <div className="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
        {/* Left Column */}
        <div className="lg:col-span-2">
          {/* About */}
          <div className="rounded-lg border bg-card p-6 shadow-sm">
            <h2 className="text-xl font-semibold">About</h2>
            <p className="mt-4 text-muted-foreground">{provider.description}</p>
          </div>

          {/* Services */}
          <div className="mt-8 rounded-lg border bg-card p-6 shadow-sm">
            <h2 className="text-xl font-semibold">Services</h2>
            <div className="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
              {provider.services.map((service) => (
                <ServiceItem key={service.name} name={service.name} price={service.price} />
              ))}
            </div>
          </div>

          {/* Reviews */}
          <div className="mt-8 rounded-lg border bg-card p-6 shadow-sm">
            <div className="flex items-center justify-between">
              <h2 className="text-xl font-semibold">Reviews</h2>
              <Button variant="outline" size="sm">
                Write a Review
              </Button>
            </div>

            <div className="mt-6 space-y-6">
              {provider.reviews.map((review) => (
                <ReviewItem key={review.id} {...review} />
              ))}
            </div>

            <div className="mt-6 text-center">
              <Button variant="outline">View All Reviews</Button>
            </div>
          </div>
        </div>

        {/* Right Column */}
        <div>
          {/* Map */}
          <div className="rounded-lg border bg-card p-6 shadow-sm">
            <h2 className="text-lg font-semibold">Location</h2>
            <div className="mt-4 aspect-video w-full overflow-hidden rounded-md">
              <ProviderMap longitude={provider.longitude} latitude={provider.latitude} />
            </div>
            <p className="mt-2 text-sm text-muted-foreground">{provider.location}</p>
          </div>

          {/* Business Hours */}
          <div className="mt-8 rounded-lg border bg-card p-6 shadow-sm">
            <div className="flex items-center gap-2">
              <Clock className="h-5 w-5 text-rudzz-blue" />
              <h2 className="text-lg font-semibold">Business Hours</h2>
            </div>
            <div className="mt-4 space-y-2">
              {provider.hours.map((item) => (
                <div key={item.day} className="flex justify-between text-sm">
                  <span className="font-medium">{item.day}</span>
                  <span className="text-muted-foreground">{item.hours}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

