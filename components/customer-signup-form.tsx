"use client"

import type React from "react"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { registerUser } from "@/lib/api"

export default function CustomerSignUpForm() {
  const [step, setStep] = useState(1)
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    phone: "",
    password: "",
    confirmPassword: "",
    location: "",
  })
  const [error, setError] = useState("")
  const router = useRouter()

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.id]: e.target.value })
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError("")

    if (formData.password !== formData.confirmPassword) {
      setError("Passwords do not match")
      return
    }

    try {
      const userData = {
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        password: formData.password,
        user_type: "customer" as const,
      }
      const data = await registerUser(userData)
      console.log("Registration successful", data)
      router.push("/login") // Redirect to login page after successful registration
    } catch (error) {
      setError("Registration failed. Please try again.")
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {step === 1 && (
        <>
          <div>
            <Label htmlFor="name">Full Name</Label>
            <Input id="name" type="text" required value={formData.name} onChange={handleChange} />
          </div>
          <div>
            <Label htmlFor="email">Email</Label>
            <Input id="email" type="email" required value={formData.email} onChange={handleChange} />
          </div>
          <div>
            <Label htmlFor="phone">Phone Number</Label>
            <Input id="phone" type="tel" required value={formData.phone} onChange={handleChange} />
          </div>
          <Button type="button" onClick={() => setStep(2)}>
            Next
          </Button>
        </>
      )}
      {step === 2 && (
        <>
          <div>
            <Label htmlFor="password">Password</Label>
            <Input id="password" type="password" required value={formData.password} onChange={handleChange} />
          </div>
          <div>
            <Label htmlFor="confirmPassword">Confirm Password</Label>
            <Input
              id="confirmPassword"
              type="password"
              required
              value={formData.confirmPassword}
              onChange={handleChange}
            />
          </div>
          <div>
            <Label htmlFor="location">Location</Label>
            <Input id="location" type="text" required value={formData.location} onChange={handleChange} />
          </div>
          {error && <p className="text-sm text-red-500">{error}</p>}
          <div className="flex gap-4">
            <Button type="button" onClick={() => setStep(1)} variant="outline">
              Back
            </Button>
            <Button type="submit">Create Account</Button>
          </div>
        </>
      )}
    </form>
  )
}

