import { PageHeader } from "@/components/page-header"
import { Button } from "@/components/ui/button"

export default function ContactPage() {
  return (
    <>
      <PageHeader title="Contact Us" description="Get in touch with our team for support or inquiries" />
      <div className="container py-12">
        <div className="grid gap-8 md:grid-cols-2">
          <div>
            <h2 className="text-2xl font-semibold">Get in Touch</h2>
            <p className="mt-4">
              We're here to help! If you have any questions, concerns, or feedback, please don't hesitate to reach out.
            </p>
            <div className="mt-6">
              <h3 className="text-lg font-medium">Email</h3>
              <p className="mt-2">support@rudzz.com</p>
            </div>
            <div className="mt-4">
              <h3 className="text-lg font-medium">Phone</h3>
              <p className="mt-2">1-800-RUDZZ-HELP</p>
            </div>
          </div>
          <div>
            <h2 className="text-2xl font-semibold">Contact Form</h2>
            <form className="mt-4 space-y-4">
              <div>
                <label htmlFor="name" className="block text-sm font-medium">
                  Name
                </label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  className="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-rudzz-blue focus:ring-rudzz-blue"
                />
              </div>
              <div>
                <label htmlFor="email" className="block text-sm font-medium">
                  Email
                </label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  className="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-rudzz-blue focus:ring-rudzz-blue"
                />
              </div>
              <div>
                <label htmlFor="message" className="block text-sm font-medium">
                  Message
                </label>
                <textarea
                  id="message"
                  name="message"
                  rows={4}
                  className="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-rudzz-blue focus:ring-rudzz-blue"
                ></textarea>
              </div>
              <Button type="submit">Send Message</Button>
            </form>
          </div>
        </div>
      </div>
    </>
  )
}

