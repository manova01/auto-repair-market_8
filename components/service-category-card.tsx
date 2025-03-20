import type React from "react"
import Link from "next/link"
import { ArrowRight } from "lucide-react"

interface ServiceCategoryProps {
  category: {
    id: number
    name: string
    icon: React.ReactNode
    count: number
  }
}

export default function ServiceCategoryCard({ category }: ServiceCategoryProps) {
  return (
    <Link
      href={`/listings?category=${category.name.toLowerCase().replace(/\s+/g, "-")}`}
      className="group flex flex-col rounded-lg border bg-card p-6 shadow-sm transition-all hover:shadow-md"
    >
      <div className="mb-4">{category.icon}</div>
      <h3 className="text-xl font-semibold">{category.name}</h3>
      <p className="mt-1 text-sm text-muted-foreground">{category.count} providers</p>
      <div className="mt-4 flex items-center text-sm font-medium text-rudyz-blue">
        <span>Browse services</span>
        <ArrowRight className="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" />
      </div>
    </Link>
  )
}

