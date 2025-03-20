import { PageHeader } from "@/components/page-header"

export default function CareersPage() {
  return (
    <>
      <PageHeader
        title="Careers at Rudzz"
        description="Join our team and help revolutionize the auto repair industry"
      />
      <div className="container py-12">
        <h2 className="text-2xl font-semibold">Why Work With Us?</h2>
        <p className="mt-4">
          At Rudzz, we're passionate about improving the auto repair experience for everyone. We offer a dynamic work
          environment, competitive benefits, and the opportunity to make a real impact in the industry.
        </p>
        <h2 className="mt-8 text-2xl font-semibold">Open Positions</h2>
        <p className="mt-4">
          We're always looking for talented individuals to join our team. Check back soon for open positions!
        </p>
      </div>
    </>
  )
}

