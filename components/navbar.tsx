"use client"

import { useState } from "react"
import Link from "next/link"
import Image from "next/image"
import { Menu, X, MessageSquare } from "lucide-react"
import { Button } from "@/components/ui/button"

export default function Navbar() {
  const [isMenuOpen, setIsMenuOpen] = useState(false)

  return (
    <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="container flex h-16 items-center justify-between">
        <div className="flex items-center gap-2">
          <Link href="/" className="flex items-center gap-2">
            <div className="relative h-10 w-10">
              <Image
                src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/rudzz2-Ka5XUqr2LZC8xWof5OsnDFUFQgz29z.png"
                alt="Rudzz Logo"
                fill
                className="object-contain"
              />
            </div>
            <span className="text-xl font-bold text-rudzz-blue">Rudzz</span>
          </Link>
        </div>

        <nav className="hidden md:flex items-center gap-6">
          <Link href="/" className="text-sm font-medium hover:text-primary">
            Home
          </Link>
          <Link href="/listings" className="text-sm font-medium hover:text-primary">
            Find Services
          </Link>
          <Link href="/providers" className="text-sm font-medium hover:text-primary">
            Service Providers
          </Link>
          <Link href="/about" className="text-sm font-medium hover:text-primary">
            About Us
          </Link>
        </nav>

        <div className="hidden md:flex items-center gap-4">
          <Link href="/messages">
            <Button variant="ghost" size="icon" className="relative">
              <MessageSquare className="h-5 w-5" />
              <span className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rudzz-orange text-[10px] text-white">
                3
              </span>
            </Button>
          </Link>
          <Link href="/login">
            <Button variant="outline" size="sm">
              Log In
            </Button>
          </Link>
          <Link href="/signup">
            <Button size="sm">Sign Up</Button>
          </Link>
        </div>

        <button className="md:hidden" onClick={() => setIsMenuOpen(!isMenuOpen)}>
          {isMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
        </button>
      </div>

      {isMenuOpen && (
        <div className="container md:hidden py-4 border-t">
          <nav className="flex flex-col gap-4">
            <Link href="/" className="text-sm font-medium hover:text-primary" onClick={() => setIsMenuOpen(false)}>
              Home
            </Link>
            <Link
              href="/listings"
              className="text-sm font-medium hover:text-primary"
              onClick={() => setIsMenuOpen(false)}
            >
              Find Services
            </Link>
            <Link
              href="/providers"
              className="text-sm font-medium hover:text-primary"
              onClick={() => setIsMenuOpen(false)}
            >
              Service Providers
            </Link>
            <Link href="/about" className="text-sm font-medium hover:text-primary" onClick={() => setIsMenuOpen(false)}>
              About Us
            </Link>
            <div className="flex flex-col gap-2 pt-4 border-t">
              <Link href="/messages" className="flex items-center gap-2">
                <MessageSquare className="h-5 w-5" />
                <span className="text-sm font-medium">Messages</span>
                <span className="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-rudzz-orange text-[10px] text-white">
                  3
                </span>
              </Link>
              <Link href="/login" className="w-full">
                <Button variant="outline" className="w-full">
                  Log In
                </Button>
              </Link>
              <Link href="/signup" className="w-full">
                <Button className="w-full">Sign Up</Button>
              </Link>
            </div>
          </nav>
        </div>
      )}
    </header>
  )
}

