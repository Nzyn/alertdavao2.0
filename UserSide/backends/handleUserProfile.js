// handleUserProfile.js - API handlers for user profile operations
const db = require("./db");

// Test MySQL connection
const testConnection = async (req, res) => {
  try {
    await db.query("SELECT 1");
    res.json({ success: true, message: "Database connection successful" });
  } catch (error) {
    console.error("Database connection test failed:", error);
    res.status(500).json({ success: false, message: "Database connection failed" });
  }
};

// Get user by ID
const getUserById = async (req, res) => {
  const { id } = req.params;
  
  try {
    console.log(`📊 Fetching user profile for ID: ${id}`);
    
    const [rows] = await db.query(
      "SELECT * FROM users WHERE id = ?",
      [id]
    );
    
    if (rows.length === 0) {
      return res.status(404).json({ message: "User not found" });
    }
    
    const user = rows[0];
    console.log("✅ User profile fetched:", user);
    
    res.json({
      success: true,
      data: user
    });
  } catch (error) {
    console.error("❌ Error fetching user:", error);
    res.status(500).json({ 
      success: false, 
      message: "Failed to fetch user",
      error: error.message 
    });
  }
};

// Insert or update user (upsert)
const upsertUser = async (req, res) => {
  const { id, firstname, lastname, email, contact, address, latitude, longitude, station_id, is_verified } = req.body;
  
  try {
    console.log("💾 Upserting user profile:", req.body);
    console.log(`📍 Address field received: "${address}"`);
    console.log(`🌍 Coordinates: lat=${latitude}, lng=${longitude}`);
    
    // Check if user exists
    const [existing] = await db.query("SELECT id FROM users WHERE id = ?", [id]);
    
    if (existing.length > 0) {
      // Update existing user
      const query = `
        UPDATE users 
        SET firstname = ?, 
            lastname = ?, 
            email = ?, 
            contact = ?, 
            address = ?, 
            latitude = ?,
            longitude = ?,
            station_id = ?,
            is_verified = ?,
            updated_at = NOW()
        WHERE id = ?
      `;
      
      await db.query(query, [
        firstname, 
        lastname, 
        email, 
        contact, 
        address, 
        latitude || null,
        longitude || null,
        station_id || null,
        is_verified, 
        id
      ]);
      
      console.log(`✅ User ${id} updated successfully`);
      console.log(`📍 Address saved: "${address}"`);
    } else {
      // Insert new user
      const query = `
        INSERT INTO users (id, firstname, lastname, email, contact, address, latitude, longitude, station_id, is_verified, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
      `;
      
      await db.query(query, [
        id,
        firstname,
        lastname,
        email,
        contact,
        address,
        latitude || null,
        longitude || null,
        station_id || null,
        is_verified
      ]);
      
      console.log(`✅ User ${id} created successfully`);
      console.log(`📍 Address saved: "${address}"`);
    }
    
    res.json({
      success: true,
      message: "User profile saved successfully"
    });
  } catch (error) {
    console.error("❌ Error upserting user:", error);
    res.status(500).json({
      success: false,
      message: "Failed to save user profile",
      error: error.message
    });
  }
};

// Update user address only
const updateUserAddress = async (req, res) => {
  const { id } = req.params;
  const { address } = req.body;
  
  try {
    console.log(`📍 Updating address for user ${id}:`, address);
    
    const query = `
      UPDATE users 
      SET address = ?, 
          updated_at = NOW()
      WHERE id = ?
    `;
    
    const [result] = await db.query(query, [address, id]);
    
    if (result.affectedRows === 0) {
      return res.status(404).json({ message: "User not found" });
    }
    
    console.log(`✅ Address updated successfully for user ${id}`);
    
    res.json({
      success: true,
      message: "Address updated successfully"
    });
  } catch (error) {
    console.error("❌ Error updating address:", error);
    res.status(500).json({
      success: false,
      message: "Failed to update address",
      error: error.message
    });
  }
};

// Execute raw SQL query (for debugging/testing)
const executeQuery = async (req, res) => {
  const { query, params } = req.body;
  
  try {
    console.log("🔧 Executing query:", query, params);
    
    const [rows] = await db.query(query, params || []);
    
    res.json({
      success: true,
      data: rows
    });
  } catch (error) {
    console.error("❌ Query execution failed:", error);
    res.status(500).json({
      success: false,
      message: "Query execution failed",
      error: error.message
    });
  }
};

module.exports = {
  testConnection,
  getUserById,
  upsertUser,
  updateUserAddress,
  executeQuery
};
