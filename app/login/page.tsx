"use client"

import type React from "react"

import { useState } from "react"
import Link from "next/link"
import Image from "next/image"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Mail, Phone, Lock } from "lucide-react"
import { loginUser } from "@/lib/api"

export default function LoginPage() {
  const [loginMethod, setLoginMethod] = useState<"email" | "phone">("email")
  const [email, setEmail] = useState("")
  const [phone, setPhone] = useState("")
  const [password, setPassword] = useState("")
  const [error, setError] = useState("")
  const router = useRouter()

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    setError("")

    try {
      const credentials = loginMethod === "email" ? { email, password } : { phone, password }

      const data = await loginUser(credentials)
      console.log("Login successful", data)
      // Here you would typically store the token and user info in your app's state or local storage
      router.push("/dashboard") // Redirect to dashboard or home page after successful login
    } catch (error) {
      setError("Login failed. Please check your credentials and try again.")
    }
  }

  return (
    <div className="container flex min-h-[calc(100vh-4rem)] flex-col items-center justify-center py-8">
      <div className="mx-auto w-full max-w-md">
        <div className="flex flex-col items-center space-y-2 text-center">
          <div className="relative h-12 w-12">
            <Image
              src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/rudzz2-Ka5XUqr2LZC8xWof5OsnDFUFQgz29z.png"
              alt="Rudzz Logo"
              fill
              className="object-contain"
            />
          </div>
          <h1 className="text-2xl font-bold">Welcome back</h1>
          <p className="text-sm text-muted-foreground">Sign in to your account to continue</p>
        </div>

        <div className="mt-8">
          <Tabs defaultValue="email" className="w-full">
            <TabsList className="grid w-full grid-cols-2">
              <TabsTrigger value="email" onClick={() => setLoginMethod("email")}>
                Email
              </TabsTrigger>
              <TabsTrigger value="phone" onClick={() => setLoginMethod("phone")}>
                Phone
              </TabsTrigger>
            </TabsList>
            <TabsContent value="email">
              <form onSubmit={handleSubmit} className="mt-4 space-y-4">
                <div>
                  <Label htmlFor="email">Email address</Label>
                  <div className="relative mt-1">
                    <Mail className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                    <Input
                      id="email"
                      type="email"
                      placeholder="Enter your email"
                      className="pl-10"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                    />
                  </div>
                </div>
                <div>
                  <Label htmlFor="password-email">Password</Label>
                  <div className="relative mt-1">
                    <Lock className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                    <Input
                      id="password-email"
                      type="password"
                      placeholder="Enter your password"
                      className="pl-10"
                      required
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                    />
                  </div>
                </div>
                {error && <p className="text-sm text-red-500">{error}</p>}
                <Button type="submit" className="w-full">
                  Sign In
                </Button>
              </form>
            </TabsContent>
            <TabsContent value="phone">
              <form onSubmit={handleSubmit} className="mt-4 space-y-4">
                <div>
                  <Label htmlFor="phone">Phone number</Label>
                  <div className="relative mt-1">
                    <Phone className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                    <Input
                      id="phone"
                      type="tel"
                      placeholder="Enter your phone number"
                      className="pl-10"
                      required
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                    />
                  </div>
                </div>
                <div>
                  <Label htmlFor="password-phone">Password</Label>
                  <div className="relative mt-1">
                    <Lock className="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                    <Input
                      id="password-phone"
                      type="password"
                      placeholder="Enter your password"
                      className="pl-10"
                      required
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                    />
                  </div>
                </div>
                {error && <p className="text-sm text-red-500">{error}</p>}
                <Button type="submit" className="w-full">
                  Sign In
                </Button>
              </form>
            </TabsContent>
          </Tabs>

          <p className="mt-6 text-center text-sm text-muted-foreground">
            Don't have an account?{" "}
            <Link href="/signup" className="font-medium text-rudzz-blue hover:underline">
              Sign up
            </Link>
          </p>
        </div>
      </div>
    </div>
  )
}

