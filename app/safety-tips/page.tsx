import { PageHeader } from "@/components/page-header"

export default function SafetyTipsPage() {
  return (
    <>
      <PageHeader title="Safety Tips" description="Important safety information for using auto repair services" />
      <div className="container py-12">
        <h2 className="text-2xl font-semibold">Choosing a Service Provider</h2>
        <ul className="mt-4 list-disc pl-6">
          <li>Always check the provider's ratings and reviews before booking a service.</li>
          <li>Verify that the provider is licensed and insured.</li>
          <li>If possible, get multiple quotes before committing to a service.</li>
        </ul>

        <h2 className="mt-8 text-2xl font-semibold">During the Service</h2>
        <ul className="mt-4 list-disc pl-6">
          <li>Ask for a detailed explanation of the work to be performed and associated costs.</li>
          <li>Don't hesitate to ask questions if you're unsure about any aspect of the service.</li>
          <li>Keep all documentation related to the service, including receipts and warranties.</li>
        </ul>

        <h2 className="mt-8 text-2xl font-semibold">After the Service</h2>
        <ul className="mt-4 list-disc pl-6">
          <li>Test your vehicle to ensure the issue has been resolved before leaving the shop.</li>
          <li>If you experience any problems after the service, contact the provider immediately.</li>
          <li>Leave an honest review to help other users make informed decisions.</li>
        </ul>
      </div>
    </>
  )
}

