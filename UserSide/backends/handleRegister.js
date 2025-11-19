const bcrypt = require("bcryptjs");
const db = require("./db");

const handleRegister = async (req, res) => {
  const { firstname, lastname, email, contact, password } = req.body;

  if (!firstname || !lastname || !email || !contact || !password) {
    return res.status(400).json({ message: "All fields are required" });
  }

  try {
    const hashedPassword = await bcrypt.hash(password, 10); // ✅ hash password

    // Include NULL values for latitude and longitude
    const sql =
      "INSERT INTO users (firstname, lastname, email, contact, password, latitude, longitude) VALUES (?, ?, ?, ?, ?, NULL, NULL)";
    await db.query(sql, [firstname, lastname, email, contact, hashedPassword]);

    res.status(201).json({ message: "✅ User registered successfully!" });
  } catch (err) {
    console.error("❌ Registration error:", err);
    
    // Provide more specific error messages
    if (err.code === 'ER_DUP_ENTRY') {
      return res.status(409).json({ message: "Email already registered" });
    }
    if (err.code === 'ECONNREFUSED') {
      return res.status(500).json({ message: "Database connection failed. Please check if MySQL is running." });
    }
    if (err.code === 'ER_ACCESS_DENIED_ERROR') {
      return res.status(500).json({ message: "Database access denied. Please check credentials." });
    }
    
    res.status(500).json({ 
      message: "Error saving user",
      error: err.message || err.sqlMessage || "Unknown error"
    });
  }
};

module.exports = handleRegister;