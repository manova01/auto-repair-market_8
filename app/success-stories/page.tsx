import { PageHeader } from "@/components/page-header"

export default function SuccessStoriesPage() {
  return (
    <>
      <PageHeader
        title="Success Stories"
        description="Read about the positive experiences of our customers and service providers"
      />
      <div className="container py-12">
        <div className="space-y-12">
          <div>
            <h2 className="text-2xl font-semibold">John's Auto Repair Shop</h2>
            <p className="mt-4">
              "Since joining Rudzz, our business has grown by 30%. The platform has made it easy for us to connect with
              new customers and manage our appointments efficiently."
            </p>
          </div>
          <div>
            <h2 className="text-2xl font-semibold">Sarah, Car Owner</h2>
            <p className="mt-4">
              "I was stranded with a flat tire and found a nearby mechanic through Rudzz within minutes. The service was
              quick, professional, and reasonably priced. I'm now a regular user of the platform."
            </p>
          </div>
          {/* Add more success stories as needed */}
        </div>
      </div>
    </>
  )
}

