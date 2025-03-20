document.addEventListener("DOMContentLoaded", () => {
  // Conversation selection
  const conversationItems = document.querySelectorAll(".conversation-item")

  conversationItems.forEach((item) => {
    item.addEventListener("click", function () {
      // Remove active class from all items
      conversationItems.forEach((i) => i.classList.remove("active"))

      // Add active class to clicked item
      this.classList.add("active")

      // Remove unread badge
      const unreadBadge = this.querySelector(".unread-badge")
      if (unreadBadge) {
        unreadBadge.remove()

        // Update message badge count
        const messageBadge = document.querySelector(".message-badge")
        if (messageBadge) {
          const count = Number.parseInt(messageBadge.textContent) - 1
          messageBadge.textContent = count > 0 ? count : ""

          if (count === 0) {
            messageBadge.style.display = "none"
          }
        }
      }

      // In a real application, this would load the conversation
      const conversationId = this.getAttribute("data-conversation")
      console.log(`Loading conversation: ${conversationId}`)

      // For demo purposes, we'll just scroll to the bottom of the chat
      const chatMessages = document.querySelector(".chat-messages")
      if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight
      }
    })
  })

  // Message sending
  const chatInput = document.querySelector(".input-field input")
  const sendBtn = document.querySelector(".send-btn")

  if (chatInput && sendBtn) {
    // Send message on button click
    sendBtn.addEventListener("click", () => {
      sendMessage()
    })

    // Send message on Enter key
    chatInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        sendMessage()
      }
    })
  }

  // Function to send a message
  function sendMessage() {
    const messageText = chatInput.value.trim()

    if (messageText) {
      // Create new message element
      const chatMessages = document.querySelector(".chat-messages")
      const newMessage = document.createElement("div")
      newMessage.className = "message sent"

      const currentTime = new Date()
      const hours = currentTime.getHours()
      const minutes = String(currentTime.getMinutes()).padStart(2, "0")
      const ampm = hours >= 12 ? "PM" : "AM"
      const formattedHours = hours % 12 || 12

      newMessage.innerHTML = `
                <div class="message-content">
                    <div class="message-bubble">
                        <p>${messageText}</p>
                    </div>
                    <div class="message-time">${formattedHours}:${minutes} ${ampm}</div>
                </div>
            `

      chatMessages.appendChild(newMessage)

      // Clear input
      chatInput.value = ""

      // Scroll to bottom
      chatMessages.scrollTop = chatMessages.scrollHeight

      // Simulate response after 1-2 seconds
      setTimeout(
        () => {
          const responseMessage = document.createElement("div")
          responseMessage.className = "message received"

          responseMessage.innerHTML = `
                    <div class="message-avatar">
                        <img src="images/provider1.jpg" alt="Mike's Auto Repair">
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            <p>Thanks for your message! I'll get back to you shortly.</p>
                        </div>
                        <div class="message-time">${formattedHours}:${minutes} ${ampm}</div>
                    </div>
                `

          chatMessages.appendChild(responseMessage)

          // Scroll to bottom
          chatMessages.scrollTop = chatMessages.scrollHeight
        },
        Math.random() * 1000 + 1000,
      )
    }
  }

  // Attachment button
  const attachmentBtn = document.querySelector(".attachment-btn")

  if (attachmentBtn) {
    attachmentBtn.addEventListener("click", () => {
      alert("File attachment functionality will be implemented soon!")
    })
  }

  // Get URL parameters
  const urlParams = new URLSearchParams(window.location.search)
  const conversationId = urlParams.get("conversation")
  const providerId = urlParams.get("provider")

  // If conversation ID is provided, select that conversation
  if (conversationId) {
    const conversation = document.querySelector(`.conversation-item[data-conversation="${conversationId}"]`)
    if (conversation) {
      conversation.click()
    }
  }

  // If provider ID is provided, start a new conversation
  if (providerId) {
    console.log(`Starting new conversation with provider ID: ${providerId}`)
    // In a real application, this would create a new conversation
  }

  // Initialize chat scroll position
  const chatMessages = document.querySelector(".chat-messages")
  if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight
  }
})

