"use client"

import type React from "react"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Checkbox } from "@/components/ui/checkbox"

export default function ProviderSignUpForm() {
  const [step, setStep] = useState(1)

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    // Handle form submission
    console.log("Form submitted")
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {step === 1 && (
        <>
          <div>
            <Label htmlFor="businessName">Business Name</Label>
            <Input id="businessName" type="text" required />
          </div>
          <div>
            <Label htmlFor="email">Business Email</Label>
            <Input id="email" type="email" required />
          </div>
          <div>
            <Label htmlFor="phone">Business Phone</Label>
            <Input id="phone" type="tel" required />
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
            <Input id="password" type="password" required />
          </div>
          <div>
            <Label htmlFor="confirmPassword">Confirm Password</Label>
            <Input id="confirmPassword" type="password" required />
          </div>
          <div>
            <Label htmlFor="address">Business Address</Label>
            <Input id="address" type="text" required />
          </div>
          <Button type="button" onClick={() => setStep(3)}>
            Next
          </Button>
        </>
      )}
      {step === 3 && (
        <>
          <div>
            <Label htmlFor="services">Services Offered</Label>
            <Textarea id="services" required />
          </div>
          <div>
            <Label htmlFor="license">Business License Number</Label>
            <Input id="license" type="text" required />
          </div>
          <div>
            <Label htmlFor="insurance">Insurance Policy Number</Label>
            <Input id="insurance" type="text" required />
          </div>
          <div className="flex items-center space-x-2">
            <Checkbox id="terms" required />
            <Label htmlFor="terms">
              I agree to the{" "}
              <a href="/terms-of-service" className="text-rudzz-blue hover:underline">
                Terms of Service
              </a>{" "}
              and{" "}
              <a href="/privacy-policy" className="text-rudzz-blue hover:underline">
                Privacy Policy
              </a>
            </Label>
          </div>
          <div className="flex gap-4">
            <Button type="button" onClick={() => setStep(2)} variant="outline">
              Back
            </Button>
            <Button type="submit">Create Account</Button>
          </div>
        </>
      )}
    </form>
  )
}

