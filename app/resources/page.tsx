import { PageHeader } from "@/components/page-header"

export default function ResourcesPage() {
  return (
    <>
      <PageHeader title="Resources" description="Helpful information for both car owners and service providers" />
      <div className="container py-12">
        <h2 className="text-2xl font-semibold">For Car Owners</h2>
        <ul className="mt-4 list-disc pl-6">
          <li>Basic Car Maintenance Tips</li>
          <li>Understanding Your Vehicle's Warning Lights</li>
          <li>How to Choose the Right Auto Repair Shop</li>
        </ul>
        <h2 className="mt-8 text-2xl font-semibold">For Service Providers</h2>
        <ul className="mt-4 list-disc pl-6">
          <li>Best Practices for Customer Service</li>
          <li>Marketing Your Auto Repair Business</li>
          <li>Staying Up-to-Date with Automotive Technology</li>
        </ul>
        {/* Add links or more detailed content for each resource */}
      </div>
    </>
  )
}

