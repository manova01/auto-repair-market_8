"use client"

import { useState } from "react"
import { useSearchParams } from "next/navigation"
import { PageHeader } from "@/components/page-header"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import CustomerSignUpForm from "@/components/customer-signup-form"
import ProviderSignUpForm from "@/components/provider-signup-form"

export default function SignUpPage() {
  const searchParams = useSearchParams()
  const initialType = searchParams.get("type") === "provider" ? "provider" : "customer"
  const [userType, setUserType] = useState<"customer" | "provider">(initialType)

  return (
    <>
      <PageHeader title="Sign Up for Rudzz" description="Create your account to get started with Rudzz" />
      <div className="container py-12">
        <div className="mx-auto max-w-2xl">
          <Tabs defaultValue={userType} onValueChange={(value) => setUserType(value as "customer" | "provider")}>
            <TabsList className="grid w-full grid-cols-2">
              <TabsTrigger value="customer">Customer</TabsTrigger>
              <TabsTrigger value="provider">Service Provider</TabsTrigger>
            </TabsList>
            <TabsContent value="customer">
              <CustomerSignUpForm />
            </TabsContent>
            <TabsContent value="provider">
              <ProviderSignUpForm />
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </>
  )
}

