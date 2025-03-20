import { PageHeader } from "@/components/page-header"

export default function ProviderGuidelinesPage() {
  return (
    <>
      <PageHeader
        title="Provider Guidelines"
        description="Essential information for service providers on the Rudzz platform"
      />
      <div className="container py-12">
        <h2 className="text-2xl font-semibold">Becoming a Rudzz Provider</h2>
        <p className="mt-4">
          To become a service provider on Rudzz, you must meet our quality standards and agree to our terms of service.
          This includes having the necessary licenses, insurance, and experience in your field.
        </p>
        <h2 className="mt-8 text-2xl font-semibold">Service Quality Standards</h2>
        <p className="mt-4">
          We expect all providers to maintain high standards of service quality. This includes clear communication,
          punctuality, fair pricing, and excellent workmanship.
        </p>
        <h2 className="mt-8 text-2xl font-semibold">Responding to Requests</h2>
        <p className="mt-4">
          Timely responses to customer inquiries and service requests are crucial. We recommend responding to all
          messages within 24 hours to maintain a high level of customer satisfaction.
        </p>
      </div>
    </>
  )
}

