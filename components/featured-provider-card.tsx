import Link from "next/link"
import Image from "next/image"
import { MapPin, Star, BadgeCheck } from "lucide-react"
import { Button } from "@/components/ui/button"

interface FeaturedProviderProps {
  provider: {
    id: number
    name: string
    image: string
    rating: number
    reviewCount: number
    location: string
    verified: boolean
    services: string[]
  }
}

export default function FeaturedProviderCard({ provider }: FeaturedProviderProps) {
  return (
    <div className="flex flex-col rounded-lg border bg-card shadow-sm">
      <div className="flex items-center gap-4 p-6">
        <div className="relative h-16 w-16 overflow-hidden rounded-full">
          <Image src={provider.image || "/placeholder.svg"} alt={provider.name} fill className="object-cover" />
        </div>
        <div className="flex-1">
          <div className="flex items-center gap-1">
            <h3 className="font-semibold">{provider.name}</h3>
            {provider.verified && <BadgeCheck className="h-4 w-4 text-rudzz-green" />}
          </div>
          <div className="mt-1 flex items-center gap-1 text-sm">
            <Star className="h-4 w-4 fill-rudzz-orange text-rudzz-orange" />
            <span className="font-medium">{provider.rating}</span>
            <span className="text-muted-foreground">({provider.reviewCount} reviews)</span>
          </div>
          <div className="mt-1 flex items-center gap-1 text-sm text-muted-foreground">
            <MapPin className="h-4 w-4" />
            <span>{provider.location}</span>
          </div>
        </div>
      </div>

      <div className="border-t px-6 py-4">
        <h4 className="text-sm font-medium">Services:</h4>
        <div className="mt-2 flex flex-wrap gap-2">
          {provider.services.map((service) => (
            <span key={service} className="rounded-full bg-muted px-2 py-1 text-xs">
              {service}
            </span>
          ))}
        </div>
      </div>

      <div className="border-t p-4">
        <div className="flex gap-2">
          <Link href={`/providers/${provider.id}`} className="flex-1">
            <Button variant="outline" className="w-full">
              View Profile
            </Button>
          </Link>
          <Link href={`/messages/new?provider=${provider.id}`} className="flex-1">
            <Button className="w-full">Contact</Button>
          </Link>
        </div>
      </div>
    </div>
  )
}

