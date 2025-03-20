import { PenToolIcon as Tool } from "lucide-react"

interface ServiceItemProps {
  name: string
  price: string
}

export function ServiceItem({ name, price }: ServiceItemProps) {
  return (
    <div className="flex items-start gap-3 rounded-md border p-3">
      <Tool className="mt-0.5 h-5 w-5 text-rudzz-blue" />
      <div>
        <h3 className="font-medium">{name}</h3>
        <p className="text-sm text-muted-foreground">{price}</p>
      </div>
    </div>
  )
}

