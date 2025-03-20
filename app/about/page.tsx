import { PageHeader } from "@/components/page-header"

export default function AboutPage() {
  return (
    <>
      <PageHeader
        title="About Rudzz"
        description="Learn about our mission to connect car owners with trusted auto repair professionals"
      />
      <div className="container py-12">
        <h2 className="text-2xl font-semibold">Our Story</h2>
        <p className="mt-4">
          Rudzz was founded with a simple goal: to make finding reliable auto repair services easier for everyone. We
          understand the stress and uncertainty that can come with car troubles, which is why we've created a platform
          that connects car owners with skilled, vetted professionals in their area.
        </p>
        <h2 className="mt-8 text-2xl font-semibold">Our Mission</h2>
        <p className="mt-4">
          Our mission is to revolutionize the auto repair industry by providing a transparent, user-friendly platform
          that benefits both car owners and service providers. We strive to build trust, promote quality workmanship,
          and make the process of auto repair as smooth as possible.
        </p>
      </div>
    </>
  )
}

