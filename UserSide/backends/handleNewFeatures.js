// Ensure default roles exist (admin, police, user) on initialization
const db = require("./db");

// Initialize default roles (admin, police, user)
const initDefaultRoles = async () => {
  try {
    await db.query(`INSERT IGNORE INTO Roles (role_id, role_name) VALUES (1, 'admin'), (2, 'police'), (3, 'user')`);
  } catch (error) {
    console.error("Error initializing default roles:", error);
  }
};

// =====================================================
// POLICE STATIONS
// =====================================================

// Get all police stations
const getAllPoliceStations = async (req, res) => {
  try {
    const [stations] = await db.query(
      "SELECT * FROM police_stations ORDER BY station_name"
    );
    
    res.json({
      success: true,
      data: stations
    });
  } catch (error) {
    console.error("Error fetching police stations:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch police stations",
      error: error.message
    });
  }
};

// Get police station by ID
const getPoliceStationById = async (req, res) => {
  const { id } = req.params;
  
  try {
    const [stations] = await db.query(
      "SELECT * FROM police_stations WHERE station_id = ?",
      [id]
    );
    
    if (stations.length === 0) {
      return res.status(404).json({ message: "Police station not found" });
    }
    
    res.json({
      success: true,
      data: stations[0]
    });
  } catch (error) {
    console.error("Error fetching police station:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch police station",
      error: error.message
    });
  }
};

// =====================================================
// USER ROLES
// =====================================================

// Get user roles
const getUserRoles = async (req, res) => {
  const { userId } = req.params;
  
  try {
    const [roles] = await db.query(`
      SELECT r.role_id, r.role_name
      FROM user_role ur
      JOIN roles r ON ur.role_id = r.role_id
      WHERE ur.user_id = ?
    `, [userId]);
    
    res.json({
      success: true,
      data: roles
    });
  } catch (error) {
    console.error("Error fetching user roles:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch user roles",
      error: error.message
    });
  }
};

// Assign role to user
const assignUserRole = async (req, res) => {
  const { userId, roleId } = req.body;
  
  try {
    await db.query(
      "INSERT IGNORE INTO user_role (user_id, role_id) VALUES (?, ?)",
      [userId, roleId]
    );
    
    res.json({
      success: true,
      message: "Role assigned successfully"
    });
  } catch (error) {
    console.error("Error assigning role:", error);
    res.status(500).json({
      success: false,
      message: "Failed to assign role",
      error: error.message
    });
  }
};

// =====================================================
// VERIFICATION
// =====================================================

// Submit verification request
const submitVerification = async (req, res) => {
  const { userId, idPicture, idSelfie, billingDocument } = req.body;
  
  try {
    const expiration = new Date();
    expiration.setMinutes(expiration.getMinutes() + 10); // 10 minutes expiry
    
    // Current timestamp for created_at and updated_at
    const currentTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
    
    // Check if user already has a verification record
    const [existingVerifications] = await db.query(
      "SELECT * FROM verifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1",
      [userId]
    );
    
    let verificationId;
    
    if (existingVerifications.length > 0) {
      const existingVerification = existingVerifications[0];
      // If existing verification is verified, don't allow new submission
      if (existingVerification.is_verified) {
        return res.status(400).json({
          success: false,
          message: "User is already verified"
        });
      }
      
      // If existing verification is pending, don't allow new submission
      if (existingVerification.status === 'pending') {
        return res.status(400).json({
          success: false,
          message: "Verification is already pending review. Please wait for admin approval."
        });
      }
      
      // For rejected status, allow one resubmission
      if (existingVerification.status === 'rejected') {
        const [result] = await db.query(`
          UPDATE verifications 
          SET id_picture = ?, id_selfie = ?, billing_document = ?, status = 'pending', is_verified = FALSE, expiration = ?, updated_at = ?
          WHERE verification_id = ?
        `, [idPicture, idSelfie, billingDocument, expiration, currentTime, existingVerification.verification_id]);
        
        verificationId = existingVerification.verification_id;
      } else {
        // For other statuses, insert new verification
        const [result] = await db.query(`
          INSERT INTO verifications (user_id, otp_code, expiration, status, id_picture, id_selfie, billing_document, is_verified, created_at, updated_at)
          VALUES (?, ?, ?, 'pending', ?, ?, ?, FALSE, ?, ?)
        `, [userId, Math.floor(100000 + Math.random() * 900000).toString(), expiration, idPicture, idSelfie, billingDocument, currentTime, currentTime]);
        
        verificationId = result.insertId;
      }
    } else {
      // Insert new verification for users with no previous verification attempts
      const [result] = await db.query(`
        INSERT INTO verifications (user_id, otp_code, expiration, status, id_picture, id_selfie, billing_document, is_verified, created_at, updated_at)
        VALUES (?, ?, ?, 'pending', ?, ?, ?, FALSE, ?, ?)
      `, [userId, Math.floor(100000 + Math.random() * 900000).toString(), expiration, idPicture, idSelfie, billingDocument, currentTime, currentTime]);
      
      verificationId = result.insertId;
    }
    
    // Create notification for admin
    await db.query(`
      INSERT INTO notifications (user_id, type, message, \`read\`)
      VALUES (?, 'verification', 'New verification request submitted', FALSE)
    `, [userId]);
    
    res.json({
      success: true,
      verificationId: verificationId,
      message: "Verification request submitted"
    });
  } catch (error) {
    console.error("Error submitting verification:", error);
    res.status(500).json({
      success: false,
      message: "Failed to submit verification",
      error: error.message
    });
  }
};

// Handle file upload for verification documents
const uploadVerificationDocument = async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({
        success: false,
        message: "No file uploaded"
      });
    }
    
    // Return the file path for verifications
    res.json({
      success: true,
      filePath: `/verifications/${req.file.filename}`,
      message: "File uploaded successfully"
    });
  } catch (error) {
    console.error("Error uploading verification document:", error);
    res.status(500).json({
      success: false,
      message: "Failed to upload document",
      error: error.message
    });
  }
};

// Get verification status
const getVerificationStatus = async (req, res) => {
  const { userId } = req.params;
  
  try {
    const [verifications] = await db.query(
      "SELECT * FROM verifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1",
      [userId]
    );
    
    // Format the response to ensure status is correct
    if (verifications.length > 0) {
      const verification = verifications[0];
      
      // Ensure status is one of the allowed values
      const allowedStatuses = ['pending', 'verified', 'expired', 'not verified', 'rejected'];
      if (!allowedStatuses.includes(verification.status)) {
        verification.status = verification.is_verified ? 'verified' : 'not verified';
      }
      
      res.json({
        success: true,
        data: verification
      });
    } else {
      res.json({
        success: true,
        data: null
      });
    }
  } catch (error) {
    console.error("Error fetching verification status:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch verification status",
      error: error.message
    });
  }
};

// Update verification
const updateVerification = async (req, res) => {
  const { verificationId } = req.params;
  const { idPicture, idSelfie, billingDocument } = req.body;
  
  try {
    // First check if the verification exists and its status
    const [existingVerifications] = await db.query(
      "SELECT * FROM verifications WHERE verification_id = ?",
      [verificationId]
    );
    
    if (existingVerifications.length === 0) {
      return res.status(404).json({
        success: false,
        message: "Verification not found"
      });
    }
    
    const existingVerification = existingVerifications[0];
    
    // Don't allow updates to verified records
    if (existingVerification.is_verified) {
      return res.status(400).json({
        success: false,
        message: "Cannot update a verified record"
      });
    }
    
    // Don't allow updates to pending records
    if (existingVerification.status === 'pending') {
      return res.status(400).json({
        success: false,
        message: "Cannot update a pending verification. Please wait for admin approval."
      });
    }
    
    // Current timestamp for updated_at
    const currentTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
    
    const [result] = await db.query(`
      UPDATE verifications 
      SET id_picture = ?, id_selfie = ?, billing_document = ?, status = 'pending', is_verified = FALSE, updated_at = ?
      WHERE verification_id = ?
    `, [idPicture, idSelfie, billingDocument, currentTime, verificationId]);
    
    if (result.affectedRows === 0) {
      return res.status(404).json({
        success: false,
        message: "Verification not found"
      });
    }
    
    res.json({
      success: true,
      message: "Verification updated successfully"
    });
  } catch (error) {
    console.error("Error updating verification:", error);
    res.status(500).json({
      success: false,
      message: "Failed to update verification",
      error: error.message
    });
  }
};

// Approve verification
const approveVerification = async (req, res) => {
  const { verificationId, userId } = req.body;
  
  try {
    // Update verification status
    await db.query(`
      UPDATE verifications 
      SET status = 'verified', is_verified = TRUE
      WHERE verification_id = ?
    `, [verificationId]);
    
    // Update user's is_verified status
    await db.query(`
      UPDATE users 
      SET is_verified = TRUE
      WHERE id = ?
    `, [userId]);
    
    // Create notification for user
    await db.query(`
      INSERT INTO notifications (user_id, type, message, \`read\`)
      VALUES (?, 'verification', 'Your verification request has been approved', FALSE)
    `, [userId]);
    
    res.json({
      success: true,
      message: "Verification approved successfully"
    });
  } catch (error) {
    console.error("Error approving verification:", error);
    res.status(500).json({
      success: false,
      message: "Failed to approve verification",
      error: error.message
    });
  }
};

// Reject verification
const rejectVerification = async (req, res) => {
  const { verificationId, userId } = req.body;
  
  try {
    // Update verification status
    await db.query(`
      UPDATE verifications 
      SET status = 'rejected', is_verified = FALSE
      WHERE verification_id = ?
    `, [verificationId]);
    
    // Note: We don't update the user's is_verified status here because they should be able to resubmit
    
    // Create notification for user
    await db.query(`
      INSERT INTO notifications (user_id, type, message, \`read\`)
      VALUES (?, 'verification', 'Your verification request has been rejected. Please submit new documents.', FALSE)
    `, [userId]);
    
    res.json({
      success: true,
      message: "Verification rejected successfully"
    });
  } catch (error) {
    console.error("Error rejecting verification:", error);
    res.status(500).json({
      success: false,
      message: "Failed to reject verification",
      error: error.message
    });
  }
};

// =====================================================
// MESSAGES
// =====================================================

// Get user conversations (show all conversations with officers/admin)
const getUserConversations = async (req, res) => {
  const { userId } = req.params;
  
  try {
    console.log('📨 ========================================');
    console.log('📨 Fetching conversations for user:', userId);
    
    // STEP 1: Get all unique other_user_ids involved in messages with this user
    const [allMessagePartners] = await db.query(`
      SELECT DISTINCT 
        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_user_id
      FROM messages
      WHERE sender_id = ? OR receiver_id = ?
    `, [userId, userId, userId]);
    
    console.log('📨 Found', allMessagePartners.length, 'message partners');
    
    if (allMessagePartners.length === 0) {
      console.log('⚠️ No messages found for this user');
      return res.json({
        success: true,
        data: []
      });
    }
    
    // STEP 2: Check if any admin/police users exist
    const [adminCheck] = await db.query(`
      SELECT COUNT(*) as count FROM users WHERE role IN ('admin', 'police')
    `);
    const hasAdminPolice = adminCheck[0].count > 0;
    console.log(`📨 Admin/Police users in system: ${hasAdminPolice ? 'YES ✅' : 'NONE ❌'}`);
    
    // STEP 3: Include ALL conversations with message partners (both ways)
     const conversations = [];
     
     for (const partner of allMessagePartners) {
       const otherUserId = partner.other_user_id;
       
       // Get user details
       const [users] = await db.query(
         'SELECT id, firstname, lastname, role FROM users WHERE id = ?',
         [otherUserId]
       );
       
       if (users.length === 0) continue;
       
       const user = users[0];
       const isOfficerOrAdmin = user.role === 'admin' || user.role === 'police';
       
       console.log(`📨 Partner ${otherUserId}: ${user.firstname} ${user.lastname}, Role: ${user.role}, Is Officer/Admin: ${isOfficerOrAdmin}`);
       
       // Always include - users should see all their conversations
       // Police/admin can talk to users, users can talk to police/admin
       console.log(`   ✅ Including conversation`);
      
      // Get last message and unread count
      const [lastMessageResult] = await db.query(`
        SELECT message, sent_at
        FROM messages
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY sent_at DESC
        LIMIT 1
      `, [userId, otherUserId, otherUserId, userId]);
      
      const [unreadResult] = await db.query(`
        SELECT COUNT(*) as count
        FROM messages
        WHERE sender_id = ? AND receiver_id = ? AND status = FALSE
      `, [otherUserId, userId]);
      
      if (lastMessageResult.length > 0) {
        conversations.push({
          user_id: otherUserId,
          user_firstname: user.firstname,
          user_lastname: user.lastname,
          user_name: `${user.firstname} ${user.lastname}`,
          last_message: lastMessageResult[0].message,
          last_message_time: lastMessageResult[0].sent_at,
          unread_count: unreadResult[0]?.count || 0
        });
      }
    }
    
    // Sort by most recent
    conversations.sort((a, b) => new Date(b.last_message_time) - new Date(a.last_message_time));
    
    console.log('✅ Found', conversations.length, 'valid conversations');
    if (conversations.length > 0) {
      console.log('📋 First conversation:', JSON.stringify(conversations[0], null, 2));
    }
    console.log('📨 ========================================');
    
    res.json({
      success: true,
      data: conversations
    });
  } catch (error) {
    console.error("❌ Error fetching conversations:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch conversations",
      error: error.message
    });
  }
};

// Get messages between two users
const getMessagesBetweenUsers = async (req, res) => {
  const { userId, otherUserId } = req.params;
  
  console.log('📨 [getMessagesBetweenUsers] Fetching messages:', { userId, otherUserId });
  
  try {
    const [messages] = await db.query(`
      SELECT m.*, 
             sender.firstname as sender_firstname, 
             sender.lastname as sender_lastname,
             receiver.firstname as receiver_firstname,
             receiver.lastname as receiver_lastname
      FROM messages m
      JOIN users sender ON m.sender_id = sender.id
      JOIN users receiver ON m.receiver_id = receiver.id
      WHERE 
        (m.sender_id = ? AND m.receiver_id = ?) OR
        (m.sender_id = ? AND m.receiver_id = ?)
      ORDER BY m.sent_at ASC
    `, [userId, otherUserId, otherUserId, userId]);
    
    console.log(`✅ [getMessagesBetweenUsers] Found ${messages.length} messages`);
    
    res.json({
      success: true,
      data: messages
    });
  } catch (error) {
    console.error("❌ [getMessagesBetweenUsers] Error fetching messages:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch messages",
      error: error.message
    });
  }
};

// Get user messages (legacy endpoint)
const getUserMessages = async (req, res) => {
  const { userId } = req.params;
  
  try {
    const [messages] = await db.query(`
      SELECT m.*, 
             sender.firstname as sender_firstname, 
             sender.lastname as sender_lastname,
             receiver.firstname as receiver_firstname,
             receiver.lastname as receiver_lastname
      FROM messages m
      JOIN users sender ON m.sender_id = sender.id
      JOIN users receiver ON m.receiver_id = receiver.id
      WHERE m.sender_id = ? OR m.receiver_id = ?
      ORDER BY m.sent_at DESC
    `, [userId, userId]);
    
    res.json({
      success: true,
      data: messages
    });
  } catch (error) {
    console.error("Error fetching messages:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch messages",
      error: error.message
    });
  }
};

// Send message
const sendMessage = async (req, res) => {
  const { senderId, receiverId, reportId, message } = req.body;
  
  console.log('📨 [sendMessage] Received request:', { senderId, receiverId, reportId, message: message?.substring(0, 50) });
  
  try {
    // Validate input
    if (!senderId || !receiverId || !message) {
      console.error('❌ [sendMessage] Missing required fields:', { senderId, receiverId, message: !!message });
      return res.status(400).json({
        success: false,
        message: "Missing required fields (senderId, receiverId, message)",
      });
    }

    const sentAt = new Date();
    
    console.log('💾 [sendMessage] Inserting message into database...');
    const [result] = await db.query(`
      INSERT INTO messages (sender_id, receiver_id, report_id, message, sent_at, status)
      VALUES (?, ?, ?, ?, ?, FALSE)
    `, [senderId, receiverId, reportId || null, message, sentAt]);
    
    console.log('✅ [sendMessage] Message inserted successfully:', { messageId: result.insertId });
    
    res.json({
      success: true,
      messageId: result.insertId,
      message: "Message sent successfully"
    });
  } catch (error) {
    console.error("❌ [sendMessage] Error sending message:", error);
    res.status(500).json({
      success: false,
      message: "Failed to send message",
      error: error.message
    });
  }
};

// Mark message as read
const markMessageAsRead = async (req, res) => {
  const { messageId } = req.params;
  
  try {
    await db.query(
      "UPDATE messages SET status = TRUE WHERE message_id = ?",
      [messageId]
    );
    
    res.json({
      success: true,
      message: "Message marked as read"
    });
  } catch (error) {
    console.error("Error marking message as read:", error);
    res.status(500).json({
      success: false,
      message: "Failed to mark message as read",
      error: error.message
    });
  }
};

// Mark conversation as read
const markConversationAsRead = async (req, res) => {
  const { userId, otherUserId } = req.body;
  
  try {
    await db.query(
      "UPDATE messages SET status = TRUE WHERE receiver_id = ? AND sender_id = ? AND status = FALSE",
      [userId, otherUserId]
    );
    
    res.json({
      success: true,
      message: "Conversation marked as read"
    });
  } catch (error) {
    console.error("Error marking conversation as read:", error);
    res.status(500).json({
      success: false,
      message: "Failed to mark conversation as read",
      error: error.message
    });
  }
};

// Get unread count
const getUnreadCount = async (req, res) => {
  const { userId } = req.params;
  
  try {
    const [result] = await db.query(
      "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND status = FALSE",
      [userId]
    );
    
    res.json({
      success: true,
      count: result[0].count
    });
  } catch (error) {
    console.error("Error fetching unread count:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch unread count",
      error: error.message,
      count: 0
    });
  }
};

// =====================================================
// CRIME ANALYTICS
// =====================================================

// Get crime analytics by location
const getCrimeAnalytics = async (req, res) => {
  const { locationId } = req.params;
  
  try {
    const [analytics] = await db.query(
      "SELECT * FROM crime_analytics WHERE location_id = ?",
      [locationId]
    );
    
    res.json({
      success: true,
      data: analytics[0] || null
    });
  } catch (error) {
    console.error("Error fetching crime analytics:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch crime analytics",
      error: error.message
    });
  }
};

// Get all crime analytics
const getAllCrimeAnalytics = async (req, res) => {
  try {
    const [analytics] = await db.query(`
      SELECT ca.*, l.barangay
      FROM crime_analytics ca
      JOIN locations l ON ca.location_id = l.location_id
      ORDER BY ca.crime_rate DESC
    `);
    
    res.json({
      success: true,
      data: analytics
    });
  } catch (error) {
    console.error("Error fetching all crime analytics:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch crime analytics",
      error: error.message
    });
  }
};

// =====================================================
// CRIME FORECASTS
// =====================================================

// Get crime forecasts
const getCrimeForecasts = async (req, res) => {
  const { locationId } = req.params;
  
  try {
    const [forecasts] = await db.query(
      "SELECT * FROM crime_forecasts WHERE location_id = ? ORDER BY forecast_date",
      [locationId]
    );
    
    res.json({
      success: true,
      data: forecasts
    });
  } catch (error) {
    console.error("Error fetching crime forecasts:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch crime forecasts",
      error: error.message
    });
  }
};

// =====================================================
// TYPING STATUS
// =====================================================

const updateUserTypingStatus = async (req, res) => {
  const { sender_id, receiver_id, is_typing } = req.body;

  if (!sender_id || !receiver_id) {
    return res.status(400).json({
      success: false,
      message: "sender_id and receiver_id are required"
    });
  }

  try {
    const fs = require('fs');
    const path = require('path');
    const typingDir = path.join(__dirname, 'typing_status');
    
    // Create directory if it doesn't exist
    if (!fs.existsSync(typingDir)) {
      fs.mkdirSync(typingDir, { recursive: true });
    }

    const typingStatusFile = path.join(typingDir, `typing_${sender_id}_${receiver_id}.json`);
    
    if (is_typing) {
      fs.writeFileSync(typingStatusFile, JSON.stringify({
        is_typing: true,
        timestamp: Date.now()
      }));
    } else {
      if (fs.existsSync(typingStatusFile)) {
        fs.unlinkSync(typingStatusFile);
      }
    }

    res.json({
      success: true
    });
  } catch (error) {
    console.error('Error updating typing status:', error);
    res.status(500).json({
      success: false,
      message: "Failed to update typing status",
      error: error.message
    });
  }
};

const checkUserTypingStatus = async (req, res) => {
  const { senderId, receiverId } = req.params;

  if (!senderId || !receiverId) {
    return res.status(400).json({
      success: false,
      message: "senderId and receiverId are required"
    });
  }

  try {
    const fs = require('fs');
    const path = require('path');
    const typingDir = path.join(__dirname, 'typing_status');
    const typingStatusFile = path.join(typingDir, `typing_${senderId}_${receiverId}.json`);
    
    if (fs.existsSync(typingStatusFile)) {
      const data = JSON.parse(fs.readFileSync(typingStatusFile, 'utf8'));
      
      // Check if typing status is still valid (within last 3 seconds)
      if (Date.now() - data.timestamp < 3000) {
        return res.json({
          success: true,
          is_typing: true
        });
      } else {
        // Delete expired typing status
        fs.unlinkSync(typingStatusFile);
      }
    }

    res.json({
      success: true,
      is_typing: false
    });
  } catch (error) {
    console.error('Error checking typing status:', error);
    res.json({
      success: true,
      is_typing: false
    });
  }
};

module.exports = {
  // Police Stations
  getAllPoliceStations,
  getPoliceStationById,
  // User Roles
  getUserRoles,
  assignUserRole,
  // Verification
  submitVerification,
  uploadVerificationDocument,
  getVerificationStatus,
  updateVerification,
  approveVerification,
  rejectVerification,
  // Messages
  getUserConversations,
  getMessagesBetweenUsers,
  getUserMessages,
  sendMessage,
  markMessageAsRead,
  markConversationAsRead,
  getUnreadCount,
  updateUserTypingStatus,
  checkUserTypingStatus,
  // Crime Analytics
  getCrimeAnalytics,
  getAllCrimeAnalytics,
  // Crime Forecasts
  getCrimeForecasts
};