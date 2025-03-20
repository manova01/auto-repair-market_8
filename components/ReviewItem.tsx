import { StarRating } from "./StarRating"

interface ReviewItemProps {
  user: string
  rating: number
  date: string
  comment: string
}

export function ReviewItem({ user, rating, date, comment }: ReviewItemProps) {
  return (
    <div className="border-b pb-6 last:border-0">
      <div className="flex items-center justify-between">
        <h3 className="font-medium">{user}</h3>
        <span className="text-sm text-muted-foreground">{date}</span>
      </div>
      <div className="mt-1">
        <StarRating rating={rating} />
      </div>
      <p className="mt-2 text-muted-foreground">{comment}</p>
    </div>
  )
}

