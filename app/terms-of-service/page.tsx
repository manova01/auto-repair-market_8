import { PageHeader } from "@/components/page-header"

export default function TermsOfServicePage() {
  return (
    <>
      <PageHeader title="Terms of Service" description="Please read these terms carefully before using our platform" />
      <div className="container py-12">
        <h2 className="text-2xl font-semibold">Acceptance of Terms</h2>
        <p className="mt-4">
          By accessing or using the Rudzz platform, you agree to be bound by these Terms of Service and all applicable
          laws and regulations. If you do not agree with any part of these terms, you may not use our services.
        </p>
        <h2 className="mt-8 text-2xl font-semibold">Use of Services</h2>
        <p className="mt-4">
          You may use our services only as permitted by law and these Terms of Service. You agree not to misuse our
          services or help anyone else do so.
        </p>
        {/* Add more sections as needed */}
      </div>
    </>
  )
}

