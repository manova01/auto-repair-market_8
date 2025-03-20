document.addEventListener("DOMContentLoaded", () => {
  // Initialize map
  if (typeof mapboxgl !== "undefined") {
    mapboxgl.accessToken = "pk.eyJ1Ijoicm9iMjMiLCJhIjoiY2tvbzViOHdsMDg1bTJvcGljbHp0ZTZrYyJ9.12KbwskPePI6RYd0K6E5Ew" // Replace with your actual token

    const map = new mapboxgl.Map({
      container: "contact-map",
      style: "mapbox://styles/mapbox/streets-v11",
      center: [-122.4194, 37.7749], // San Francisco coordinates
      zoom: 14,
    })

    // Add marker for office location
    new mapboxgl.Marker()
      .setLngLat([-122.4194, 37.7749])
      .setPopup(
        new mapboxgl.Popup({ offset: 25 }).setHTML(`
                    <h3>Rudzz Auto Headquarters</h3>
                    <p>123 Tech Plaza, Suite 400<br>San Francisco, CA 94105</p>
                `),
      )
      .addTo(map)

    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl(), "top-right")
  } else {
    console.error("Mapbox GL JS is not loaded. Make sure you have included it in your HTML.")
  }

  // FAQ accordion
  const faqQuestions = document.querySelectorAll(".faq-question")

  faqQuestions.forEach((question) => {
    question.addEventListener("click", function () {
      const faqItem = this.parentElement
      const answer = this.nextElementSibling
      const icon = this.querySelector("i")

      // Toggle active class
      faqItem.classList.toggle("active")

      // Toggle answer visibility
      if (faqItem.classList.contains("active")) {
        answer.style.maxHeight = answer.scrollHeight + "px"
        icon.classList.remove("fa-chevron-down")
        icon.classList.add("fa-chevron-up")
      } else {
        answer.style.maxHeight = "0"
        icon.classList.remove("fa-chevron-up")
        icon.classList.add("fa-chevron-down")
      }
    })
  })

  // Live chat button
  const liveChatBtn = document.querySelector(".contact-card .btn-primary")

  if (liveChatBtn) {
    liveChatBtn.addEventListener("click", () => {
      alert("Live chat functionality will be implemented soon!")
    })
  }

  // Form submission
  const contactForm = document.getElementById("contact-form")

  if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Simulate form submission
      alert("Your message has been sent! We will get back to you soon.")
      contactForm.reset()
    })
  }
})

