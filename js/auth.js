document.addEventListener("DOMContentLoaded", () => {
  // Tab switching
  const tabs = document.querySelectorAll(".auth-tab")
  const tabContents = document.querySelectorAll(".auth-tab-content")

  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      const tabId = this.getAttribute("data-tab")

      // Remove active class from all tabs and contents
      tabs.forEach((t) => t.classList.remove("active"))
      tabContents.forEach((c) => c.classList.remove("active"))

      // Add active class to selected tab and content
      this.classList.add("active")
      document.getElementById(`${tabId}-tab`).classList.add("active")
    })
  })

  // Password toggle
  const passwordToggles = document.querySelectorAll(".password-toggle")
  passwordToggles.forEach((toggle) => {
    toggle.addEventListener("click", function () {
      const passwordInput = this.previousElementSibling
      const icon = this.querySelector("i")

      if (passwordInput.type === "password") {
        passwordInput.type = "text"
        icon.classList.remove("fa-eye")
        icon.classList.add("fa-eye-slash")
      } else {
        passwordInput.type = "password"
        icon.classList.remove("fa-eye-slash")
        icon.classList.add("fa-eye")
      }
    })
  })

  // Phone verification
  const sendCodeBtn = document.getElementById("send-code-btn")
  const phoneInput = document.getElementById("phone")
  const codeContainer = document.getElementById("code-container")
  const resendCodeBtn = document.getElementById("resend-code")
  const countdownSpan = document.getElementById("countdown")
  const otpBoxes = document.querySelectorAll(".otp-box")
  const verificationCodeInput = document.getElementById("verification-code")

  let countdown = 60
  let countdownInterval

  // Focus and select OTP box when typing
  otpBoxes.forEach((box) => {
    box.addEventListener("keyup", function (e) {
      const index = Number.parseInt(this.getAttribute("data-index"))

      // If a number was entered
      if (/^[0-9]$/.test(e.key)) {
        // Move to next input if available
        if (index < 5) {
          otpBoxes[index + 1].focus()
        }

        // Update the hidden verification code
        updateVerificationCode()
      }
      // Handle backspace
      else if (e.key === "Backspace") {
        // Move to previous input if available
        if (index > 0) {
          otpBoxes[index - 1].focus()
        }
      }
    })

    // Select all text when focusing
    box.addEventListener("focus", function () {
      this.select()
    })

    // Handle paste event
    box.addEventListener("paste", (e) => {
      e.preventDefault()
      const pastedData = (e.clipboardData || window.clipboardData).getData("text")

      if (/^\d+$/.test(pastedData) && pastedData.length <= 6) {
        // Split the pasted code and distribute to the input boxes
        const digits = pastedData.split("")
        digits.forEach((digit, i) => {
          if (i < 6) {
            otpBoxes[i].value = digit
          }
        })

        // Focus the last filled box
        if (digits.length < 6) {
          otpBoxes[digits.length].focus()
        } else {
          otpBoxes[5].focus()
        }

        // Update the hidden verification code
        updateVerificationCode()
      }
    })
  })

  function updateVerificationCode() {
    let code = ""
    otpBoxes.forEach((box) => {
      code += box.value
    })
    verificationCodeInput.value = code
  }

  function startCountdown() {
    countdown = 60
    countdownSpan.textContent = countdown
    resendCodeBtn.style.pointerEvents = "none"
    resendCodeBtn.style.opacity = "0.6"

    clearInterval(countdownInterval)
    countdownInterval = setInterval(() => {
      countdown--
      countdownSpan.textContent = countdown

      if (countdown <= 0) {
        clearInterval(countdownInterval)
        resendCodeBtn.textContent = "Resend code"
        resendCodeBtn.style.pointerEvents = "auto"
        resendCodeBtn.style.opacity = "1"
      }
    }, 1000)
  }

  if (sendCodeBtn) {
    sendCodeBtn.addEventListener("click", () => {
      const phone = phoneInput.value.trim()

      if (!phone) {
        alert("Please enter a valid phone number")
        return
      }

      // API call to send verification code
      fetch("/api/phone-verification.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ phone }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            codeContainer.style.display = "block"
            startCountdown()
            otpBoxes[0].focus()
          } else {
            alert(data.message || "Failed to send verification code. Please try again.")
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("An error occurred. Please try again.")
        })
    })
  }

  if (resendCodeBtn) {
    resendCodeBtn.addEventListener("click", () => {
      if (countdown <= 0) {
        const phone = phoneInput.value.trim()

        // API call to resend verification code
        fetch("/api/phone-verification.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ phone }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              startCountdown()
            } else {
              alert(data.message || "Failed to resend verification code. Please try again.")
            }
          })
          .catch((error) => {
            console.error("Error:", error)
            alert("An error occurred. Please try again.")
          })
      }
    })
  }

  // Phone verification form submission
  const phoneLoginForm = document.getElementById("phone-login-form")
  if (phoneLoginForm) {
    phoneLoginForm.addEventListener("submit", (e) => {
      e.preventDefault()

      const phone = phoneInput.value.trim()
      const code = verificationCodeInput.value

      if (!phone || !code || code.length !== 6) {
        alert("Please enter a valid phone number and verification code")
        return
      }

      // API call to verify code and log in
      fetch("/api/verify-code.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ phone, code }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Store user data in localStorage
            localStorage.setItem("user", JSON.stringify(data.user))

            // Redirect to dashboard
            window.location.href = "dashboard.html"
          } else {
            alert(data.message || "Invalid verification code. Please try again.")
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("An error occurred. Please try again.")
        })
    })
  }

  // Social login buttons
  const googleLoginBtn = document.getElementById("google-login")
  const facebookLoginBtn = document.getElementById("facebook-login")

  // Declare variables for Google and Facebook OAuth
  const googleClientId = "YOUR_GOOGLE_CLIENT_ID"
  const googleRedirectUri = "YOUR_GOOGLE_REDIRECT_URI"
  const facebookAppId = "YOUR_FACEBOOK_APP_ID"
  const facebookRedirectUri = "YOUR_FACEBOOK_REDIRECT_URI"

  if (googleLoginBtn) {
    googleLoginBtn.addEventListener("click", () => {
      // Google OAuth URL
      const googleAuthUrl = `https://accounts.google.com/o/oauth2/v2/auth?client_id=${googleClientId}&redirect_uri=${encodeURIComponent(googleRedirectUri)}&response_type=code&scope=email%20profile&access_type=offline`
      window.location.href = googleAuthUrl
    })
  }

  if (facebookLoginBtn) {
    facebookLoginBtn.addEventListener("click", () => {
      // Facebook OAuth URL
      const facebookAuthUrl = `https://www.facebook.com/v12.0/dialog/oauth?client_id=${facebookAppId}&redirect_uri=${encodeURIComponent(facebookRedirectUri)}&response_type=code&scope=email,public_profile`
      window.location.href = facebookAuthUrl
    })
  }

  // Email login form submission
  const emailLoginForm = document.getElementById("email-login-form")
  if (emailLoginForm) {
    emailLoginForm.addEventListener("submit", (e) => {
      e.preventDefault()

      const email = document.getElementById("email").value.trim()
      const password = document.getElementById("password").value

      if (!email || !password) {
        alert("Please enter your email and password")
        return
      }

      // API call to login with email and password
      fetch("/api/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ email, password }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Store user data in localStorage
            localStorage.setItem("user", JSON.stringify(data.user))

            // Redirect to dashboard
            window.location.href = "dashboard.html"
          } else {
            alert(data.message || "Invalid email or password. Please try again.")
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("An error occurred. Please try again.")
        })
    })
  }

  // Set current year in footer
  const currentYearEl = document.getElementById("current-year")
  if (currentYearEl) {
    currentYearEl.textContent = new Date().getFullYear()
  }

  // Check for login error in URL
  const urlParams1 = new URLSearchParams(window.location.search)
  const error = urlParams1.get("error")
  if (error) {
    alert(decodeURIComponent(error))
  }

  // Handle account type selection in signup
  const accountTypeBtns = document.querySelectorAll(".account-type-btn")

  accountTypeBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      accountTypeBtns.forEach((b) => b.classList.remove("active"))
      this.classList.add("active")

      const accountType = this.getAttribute("data-type")
      if (accountType === "provider") {
        // Redirect to provider signup page
        window.location.href = "provider-signup.html"
      }
    })
  })

  // Check URL parameters for account type
  const urlParams = new URLSearchParams(window.location.search)
  const accountType = urlParams.get("type")

  if (accountType === "provider") {
    const providerBtn = document.querySelector('.account-type-btn[data-type="provider"]')
    if (providerBtn) {
      accountTypeBtns.forEach((b) => b.classList.remove("active"))
      providerBtn.classList.add("active")
    }
  }

  // Password strength meter
  const passwordInput = document.getElementById("password")
  const strengthMeter = document.querySelector(".strength-meter-fill")
  const strengthText = document.querySelector(".strength-text span")

  if (passwordInput && strengthMeter && strengthText) {
    passwordInput.addEventListener("input", function () {
      const password = this.value
      let strength = 0

      if (password.length >= 8) strength += 1
      if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1
      if (password.match(/\d/)) strength += 1
      if (password.match(/[^a-zA-Z\d]/)) strength += 1

      strengthMeter.setAttribute("data-strength", strength)

      switch (strength) {
        case 0:
          strengthText.textContent = "Weak"
          break
        case 1:
          strengthText.textContent = "Fair"
          break
        case 2:
          strengthText.textContent = "Good"
          break
        case 3:
          strengthText.textContent = "Strong"
          break
        case 4:
          strengthText.textContent = "Very Strong"
          break
      }
    })
  }

  // Form submission
  const loginForm = document.getElementById("login-form")
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Simulate login
      alert("Login successful! Redirecting to dashboard...")
      window.location.href = "dashboard.html"
    })
  }

  const signupForm = document.getElementById("signup-form")
  if (signupForm) {
    signupForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Simulate signup
      alert("Account created successfully! Redirecting to dashboard...")
      window.location.href = "dashboard.html"
    })
  }
})

