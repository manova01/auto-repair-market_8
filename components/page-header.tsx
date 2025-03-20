interface PageHeaderProps {
  title: string
  description?: string
}

export function PageHeader({ title, description }: PageHeaderProps) {
  return (
    <div className="bg-rudzz-blue py-12 text-white">
      <div className="container">
        <h1 className="text-3xl font-bold md:text-4xl">{title}</h1>
        {description && <p className="mt-2 text-lg text-white/80">{description}</p>}
      </div>
    </div>
  )
}

