import { Star } from "lucide-react"

interface StarRatingProps {
  rating: number
  max?: number
}

export function StarRating({ rating, max = 5 }: StarRatingProps) {
  return (
    <div className="flex">
      {Array.from({ length: max }).map((_, i) => (
        <Star
          key={i}
          className={`h-5 w-5 ${
            i < Math.floor(rating) ? "fill-rudzz-orange text-rudzz-orange" : "text-muted-foreground"
          }`}
        />
      ))}
    </div>
  )
}

