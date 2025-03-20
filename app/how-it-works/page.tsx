import { PageHeader } from "@/components/page-header"
import { Search, Star, MessageSquare } from "lucide-react"

export default function HowItWorksPage() {
  return (
    <>
      <PageHeader
        title="How It Works"
        description="Learn how to use Rudzz to find and connect with auto repair professionals"
      />
      <div className="container py-12">
        <div className="grid gap-8 md:grid-cols-3">
          <div className="flex flex-col items-center text-center">
            <div className="flex h-16 w-16 items-center justify-center rounded-full bg-rudzz-blue/10 text-rudzz-blue">
              <Search className="h-8 w-8" />
            </div>
            <h3 className="mt-4 text-xl font-semibold">Search</h3>
            <p className="mt-2 text-muted-foreground">
              Enter your location and the type of service you need to find nearby auto repair professionals.
            </p>
          </div>
          <div className="flex flex-col items-center text-center">
            <div className="flex h-16 w-16 items-center justify-center rounded-full bg-rudzz-green/10 text-rudzz-green">
              <Star className="h-8 w-8" />
            </div>
            <h3 className="mt-4 text-xl font-semibold">Choose</h3>
            <p className="mt-2 text-muted-foreground">
              Compare providers based on ratings, reviews, and services offered to find the best match for your needs.
            </p>
          </div>
          <div className="flex flex-col items-center text-center">
            <div className="flex h-16 w-16 items-center justify-center rounded-full bg-rudzz-orange/10 text-rudzz-orange">
              <MessageSquare className="h-8 w-8" />
            </div>
            <h3 className="mt-4 text-xl font-semibold">Connect</h3>
            <p className="mt-2 text-muted-foreground">
              Message providers directly through the platform to discuss your needs and book services.
            </p>
          </div>
        </div>
      </div>
    </>
  )
}

