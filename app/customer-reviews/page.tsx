import { PageHeader } from "@/components/page-header"
import { Star } from "lucide-react"

export default function CustomerReviewsPage() {
  const reviews = [
    {
      id: 1,
      name: "John D.",
      rating: 5,
      comment: "Great service! They fixed my car quickly and at a reasonable price.",
    },
    {
      id: 2,
      name: "Sarah M.",
      rating: 4,
      comment: "Professional and knowledgeable staff. They explained everything clearly.",
    },
    {
      id: 3,
      name: "Robert L.",
      rating: 5,
      comment: "Excellent service. They diagnosed and fixed an issue that two other shops couldn't figure out.",
    },
  ]

  return (
    <>
      <PageHeader
        title="Customer Reviews"
        description="See what our customers are saying about their experiences with Rudzz"
      />
      <div className="container py-12">
        <div className="space-y-8">
          {reviews.map((review) => (
            <div key={review.id} className="rounded-lg border p-6 shadow-sm">
              <div className="flex items-center justify-between">
                <h3 className="text-lg font-semibold">{review.name}</h3>
                <div className="flex">
                  {Array.from({ length: 5 }).map((_, i) => (
                    <Star
                      key={i}
                      className={`h-5 w-5 ${
                        i < review.rating ? "fill-rudzz-orange text-rudzz-orange" : "text-muted-foreground"
                      }`}
                    />
                  ))}
                </div>
              </div>
              <p className="mt-2 text-muted-foreground">{review.comment}</p>
            </div>
          ))}
        </div>
      </div>
    </>
  )
}

