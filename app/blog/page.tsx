import { PageHeader } from "@/components/page-header"

export default function BlogPage() {
  const blogPosts = [
    { id: 1, title: "5 Signs Your Car Needs a Tune-Up", date: "2023-05-15" },
    { id: 2, title: "The Importance of Regular Oil Changes", date: "2023-05-10" },
    { id: 3, title: "How to Prepare Your Car for a Long Road Trip", date: "2023-05-05" },
  ]

  return (
    <>
      <PageHeader title="Rudzz Blog" description="Stay informed with the latest auto repair tips and industry news" />
      <div className="container py-12">
        <div className="space-y-8">
          {blogPosts.map((post) => (
            <div key={post.id}>
              <h2 className="text-2xl font-semibold">{post.title}</h2>
              <p className="mt-2 text-sm text-gray-500">{post.date}</p>
              <p className="mt-4">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua.
              </p>
              <a href={`/blog/${post.id}`} className="mt-2 inline-block text-rudzz-blue hover:underline">
                Read more
              </a>
            </div>
          ))}
        </div>
      </div>
    </>
  )
}

