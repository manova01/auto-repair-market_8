"use client"

import { useState } from "react"
import Image from "next/image"
import { BadgeCheck, Phone, Search, Send, Video } from "lucide-react"
import { Button } from "@/components/ui/button"

export default function MessagesPage() {
  const [selectedChat, setSelectedChat] = useState(1)

  // Mock data for chats
  const chats = [
    {
      id: 1,
      provider: {
        id: 1,
        name: "Mike's Auto Repair",
        image: "/placeholder.svg?height=100&width=100",
        verified: true,
        online: true,
        lastSeen: "Online",
      },
      messages: [
        {
          id: 1,
          sender: "provider",
          text: "Hello! How can I help you with your vehicle today?",
          time: "10:30 AM",
        },
        {
          id: 2,
          sender: "user",
          text: "Hi, I'm having issues with my brakes. They're making a squeaking noise when I press them.",
          time: "10:32 AM",
        },
        {
          id: 3,
          sender: "provider",
          text: "That could be worn brake pads. When did you last have them replaced?",
          time: "10:33 AM",
        },
        {
          id: 4,
          sender: "user",
          text: "It's been about 2 years. How much would it cost to replace them?",
          time: "10:35 AM",
        },
        {
          id: 5,
          sender: "provider",
          text: "For your vehicle model, brake pad replacement typically costs between $150-$250 depending on the quality of pads you prefer. Would you like to schedule an appointment?",
          time: "10:37 AM",
        },
      ],
    },
    {
      id: 2,
      provider: {
        id: 2,
        name: "Quick Fix Auto",
        image: "/placeholder.svg?height=100&width=100",
        verified: true,
        online: false,
        lastSeen: "Last seen 2h ago",
      },
      messages: [
        {
          id: 1,
          sender: "user",
          text: "Do you offer tire rotation services?",
          time: "Yesterday",
        },
        {
          id: 2,
          sender: "provider",
          text: "Yes, we do! Our tire rotation service is $40, or free with any oil change package.",
          time: "Yesterday",
        },
      ],
    },
    {
      id: 3,
      provider: {
        id: 3,
        name: "Pro Mechanics",
        image: "/placeholder.svg?height=100&width=100",
        verified: false,
        online: false,
        lastSeen: "Last seen 1d ago",
      },
      messages: [
        {
          id: 1,
          sender: "provider",
          text: "Thanks for your inquiry. We can definitely help with your AC issues. When would you like to bring your car in?",
          time: "2 days ago",
        },
      ],
    },
  ]

  const selectedChatData = chats.find((chat) => chat.id === selectedChat)

  return (
    <div className="container py-8">
      <h1 className="text-2xl font-bold">Messages</h1>

      <div className="mt-6 grid h-[calc(100vh-12rem)] grid-cols-1 overflow-hidden rounded-lg border bg-card shadow-sm md:grid-cols-3">
        {/* Chat List */}
        <div className="border-r">
          <div className="border-b p-4">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <input
                type="text"
                placeholder="Search messages"
                className="w-full rounded-md border-0 py-2 pl-9 pr-4 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rudzz-blue"
              />
            </div>
          </div>

          <div className="h-[calc(100vh-16rem)] overflow-y-auto">
            {chats.map((chat) => (
              <div
                key={chat.id}
                className={`cursor-pointer border-b p-4 transition-colors hover:bg-muted/50 ${
                  selectedChat === chat.id ? "bg-muted" : ""
                }`}
                onClick={() => setSelectedChat(chat.id)}
              >
                <div className="flex items-start gap-3">
                  <div className="relative h-12 w-12 overflow-hidden rounded-full">
                    <Image
                      src={chat.provider.image || "/placeholder.svg"}
                      alt={chat.provider.name}
                      fill
                      className="object-cover"
                    />
                    {chat.provider.online && (
                      <div className="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-rudzz-green ring-2 ring-background"></div>
                    )}
                  </div>
                  <div className="flex-1 overflow-hidden">
                    <div className="flex items-center gap-1">
                      <h3 className="font-medium">{chat.provider.name}</h3>
                      {chat.provider.verified && <BadgeCheck className="h-4 w-4 text-rudzz-green" />}
                    </div>
                    <p className="truncate text-sm text-muted-foreground">
                      {chat.messages[chat.messages.length - 1].text}
                    </p>
                  </div>
                  <div className="text-xs text-muted-foreground">{chat.messages[chat.messages.length - 1].time}</div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Chat Area */}
        {selectedChatData ? (
          <div className="flex h-full flex-col md:col-span-2">
            {/* Chat Header */}
            <div className="flex items-center justify-between border-b p-4">
              <div className="flex items-center gap-3">
                <div className="relative h-10 w-10 overflow-hidden rounded-full">
                  <Image
                    src={selectedChatData.provider.image || "/placeholder.svg"}
                    alt={selectedChatData.provider.name}
                    fill
                    className="object-cover"
                  />
                  {selectedChatData.provider.online && (
                    <div className="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-rudzz-green ring-2 ring-background"></div>
                  )}
                </div>
                <div>
                  <div className="flex items-center gap-1">
                    <h3 className="font-medium">{selectedChatData.provider.name}</h3>
                    {selectedChatData.provider.verified && <BadgeCheck className="h-4 w-4 text-rudzz-green" />}
                  </div>
                  <p className="text-xs text-muted-foreground">
                    {selectedChatData.provider.online ? "Online" : selectedChatData.provider.lastSeen}
                  </p>
                </div>
              </div>
              <div className="flex gap-2">
                <Button variant="ghost" size="icon" className="h-9 w-9">
                  <Phone className="h-5 w-5" />
                </Button>
                <Button variant="ghost" size="icon" className="h-9 w-9">
                  <Video className="h-5 w-5" />
                </Button>
              </div>
            </div>

            {/* Messages */}
            <div className="flex-1 overflow-y-auto p-4">
              <div className="space-y-4">
                {selectedChatData.messages.map((message) => (
                  <div
                    key={message.id}
                    className={`flex ${message.sender === "user" ? "justify-end" : "justify-start"}`}
                  >
                    <div
                      className={`max-w-[80%] rounded-lg px-4 py-2 ${
                        message.sender === "user" ? "bg-rudzz-blue text-white" : "bg-muted"
                      }`}
                    >
                      <p>{message.text}</p>
                      <p
                        className={`mt-1 text-right text-xs ${
                          message.sender === "user" ? "text-white/70" : "text-muted-foreground"
                        }`}
                      >
                        {message.time}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Message Input */}
            <div className="border-t p-4">
              <div className="flex gap-2">
                <input
                  type="text"
                  placeholder="Type a message..."
                  className="flex-1 rounded-md border-0 py-2 px-4 text-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-rudzz-blue"
                />
                <Button className="h-9 w-9 p-0">
                  <Send className="h-5 w-5" />
                </Button>
              </div>
            </div>
          </div>
        ) : (
          <div className="flex h-full flex-col items-center justify-center md:col-span-2">
            <div className="text-center">
              <h3 className="text-lg font-medium">No chat selected</h3>
              <p className="mt-1 text-sm text-muted-foreground">
                Select a conversation from the list to start chatting
              </p>
            </div>
          </div>
        )}
      </div>
    </div>
  )
}

