import { PageHeader } from "@/components/page-header"

export default function PrivacyPolicyPage() {
  return (
    <>
      <PageHeader
        title="Privacy Policy"
        description="Learn how we collect, use, and protect your personal information"
      />
      <div className="container py-12">
        <h2 className="text-2xl font-semibold">Information We Collect</h2>
        <p className="mt-4">
          We collect information you provide directly to us, such as when you create an account, request a service, or
          communicate with us. This may include your name, email address, phone number, and location.
        </p>
        <h2 className="mt-8 text-2xl font-semibold">How We Use Your Information</h2>
        <p className="mt-4">
          We use the information we collect to provide, maintain, and improve our services, to communicate with you, and
          to personalize your experience on our platform.
        </p>
        {/* Add more sections as needed */}
      </div>
    </>
  )
}

