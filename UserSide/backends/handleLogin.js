// handleLogin.js
const bcrypt = require("bcryptjs");
const db = require("./db");
 
const handleLogin = async (req, res) => {
  const { email, password } = req.body;

  try {
    console.log("📩 Login attempt:", { email });

    // 1. Query user
    const [rows] = await db.query("SELECT * FROM users WHERE email = ?", [email]);
    console.log("📊 Query result:", rows);

    if (rows.length === 0) {
      return res.status(401).json({ message: "User not found" });
    }

    const user = rows[0];
    console.log("👤 Found user:", user);

    // 2. Compare password
    const passwordMatch = await bcrypt.compare(password, user.password);
    if (!passwordMatch) {
      console.log("❌ Invalid password for:", email);
      return res.status(401).json({ message: "Invalid credentials" });
    }

    // 3. Success
    console.log("✅ Login successful for:", user.email);
    res.json({
      message: "Login successful",
      user: {
        id: user.id,
        email: user.email,
        firstname: user.firstname,
        lastname: user.lastname,
        role: user.role || 'user',
      },
    });
  } catch (error) {
    // ✅ Print full error details in terminal
    console.error("❌ Login error occurred:", error);
    res.status(500).json({
      message: "Server error",
      error: error.message || error.sqlMessage || "Unknown error",
    });
  }
};

module.exports = handleLogin;
