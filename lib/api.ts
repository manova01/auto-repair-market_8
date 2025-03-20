const API_BASE_URL = "http://your-php-server-url/api"

export async function registerUser(userData: {
  name: string
  email: string
  phone: string
  password: string
  user_type: "customer" | "provider"
}) {
  const response = await fetch(`${API_BASE_URL}/register.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(userData),
  })

  if (!response.ok) {
    throw new Error("Registration failed")
  }

  return response.json()
}

export async function loginUser(credentials: {
  email?: string
  phone?: string
  password: string
}) {
  const response = await fetch(`${API_BASE_URL}/login.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(credentials),
  })

  if (!response.ok) {
    throw new Error("Login failed")
  }

  return response.json()
}

