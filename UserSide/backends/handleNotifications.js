const db = require("./db");

// Get user notifications
const getUserNotifications = async (req, res) => {
  const { userId } = req.params;
  
  console.log("Fetching notifications for user:", userId);
  
  try {
    const notifications = [];
    const currentTime = new Date().toISOString();
    
    // 1. Check for unread messages (individual messages)
    try {
      console.log("Checking for unread messages...");
      const [unreadMessages] = await db.query(
        `SELECT 
          message_id,
          message,
          created_at
        FROM messages 
        WHERE receiver_id = ? AND status = FALSE
        ORDER BY created_at DESC
        LIMIT 10`, // Get up to 10 recent unread messages
        [userId]
      );
      
      console.log("Unread messages result:", unreadMessages);
      
      // Create separate notifications for each unread message
      unreadMessages.forEach((msg, index) => {
        notifications.push({
          id: msg.message_id,
          title: "You have a new Message",
          message: msg.message,
          timestamp: msg.created_at,
          read: false,
          type: 'message'
        });
      });
    } catch (error) {
      console.warn('Failed to fetch message notifications:', error);
    }
    
    // 2. Check for new reports (individual reports)
    try {
      console.log("Checking for new reports...");
      const [newReports] = await db.query(
        `SELECT 
          report_id,
          title,
          created_at
        FROM reports 
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 10`, // Get up to 10 recent reports
        [userId]
      );
      
      console.log("New reports result:", newReports);
      
      // Create separate notifications for each new report
      newReports.forEach((report, index) => {
        // Format the date and time in Philippine time
        const reportDate = new Date(report.created_at);
        
        notifications.push({
          id: report.report_id + 10000, // Offset to avoid ID conflicts
          title: "You have successfully submitted a new report",
          message: `Submitted on ${reportDate.toLocaleDateString('en-PH', { timeZone: 'Asia/Manila' })} at ${reportDate.toLocaleTimeString('en-PH', { timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit' })}`,
          timestamp: report.created_at,
          read: false,
          type: 'report'
        });
      });
    } catch (error) {
      console.warn('Failed to fetch new report notifications:', error);
    }
    
    // 3. Check for report status updates (individual reports with status changes)
    try {
      console.log("Checking for report status updates...");
      const [updatedReports] = await db.query(
        `SELECT 
          r.report_id,
          r.title,
          r.status,
          r.updated_at
        FROM reports r
        WHERE r.user_id = ?
        AND r.status != 'pending'
        AND r.updated_at > r.created_at  /* Only reports that have been updated */
        ORDER BY r.updated_at DESC
        LIMIT 10`, // Get up to 10 recent report updates
        [userId]
      );
      
      console.log("Updated reports result:", updatedReports);
      
      // Create separate notifications for each report update
      updatedReports.forEach((report, index) => {
        // Format the date and time in Philippine time
        const updateDate = new Date(report.updated_at);
        
        notifications.push({
          id: report.report_id + 20000, // Offset to avoid ID conflicts
          title: "Report Status Updated",
          message: `Status of your report "${report.title}" has been updated to ${report.status}`,
          timestamp: report.updated_at,
          read: false,
          type: 'report',
          relatedId: report.report_id
        });
      });
    } catch (error) {
      console.warn('Failed to fetch report update notifications:', error);
    }
    
    // 4. Check for verification status updates
    try {
      console.log("Checking for verification status updates...");
      const [verifications] = await db.query(
        `SELECT 
          verification_id,
          status,
          created_at,
          updated_at
        FROM verifications 
        WHERE user_id = ? 
        AND status IN ('approved', 'rejected')
        ORDER BY created_at DESC 
        LIMIT 5`,
        [userId]
      );
      
      console.log("Verifications result:", verifications);
      
      // Create separate notifications for each verification update
      verifications.forEach((verification, index) => {
        // Format the date and time in Philippine time
        const verifyDate = new Date(verification.updated_at || verification.created_at);
        
        notifications.push({
          id: verification.verification_id + 30000, // Offset to avoid ID conflicts
          title: "Verification Status Updated",
          message: `Your account verification has been ${verification.status}`,
          timestamp: verification.updated_at || verification.created_at,
          read: false,
          type: 'verification'
        });
      });
    } catch (error) {
      console.warn('Failed to fetch verification notifications:', error);
    }
    
    // Sort notifications by timestamp (newest first)
    notifications.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    
    console.log("Returning notifications:", notifications);
    
    res.json({
      success: true,
      data: notifications
    });
  } catch (error) {
    console.error("Error fetching notifications:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch notifications",
      error: error.message
    });
  }
};

// Mark a notification as read
const markNotificationAsRead = async (req, res) => {
  const { notificationId } = req.params;
  const { userId } = req.body;
  
  try {
    console.log(`Marking notification ${notificationId} as read for user ${userId}`);
    
    // Since we don't have a dedicated notifications table, we need to determine 
    // which table the notification belongs to based on its ID
    // ID ranges:
    // 1-9999: messages
    // 10000-19999: reports (offset by 10000)
    // 20000-29999: report updates (offset by 20000)
    // 30000-39999: verifications (offset by 30000)
    
    const numericId = parseInt(notificationId);
    
    if (numericId < 10000) {
      // This is a message notification
      // Update the message status to read (TRUE)
      await db.query(
        "UPDATE messages SET status = TRUE WHERE message_id = ?",
        [numericId]
      );
      console.log(`Marked message ${numericId} as read`);
    } else if (numericId >= 10000 && numericId < 20000) {
      // This is a report submission notification
      // For report submissions, we don't need to update anything in the database
      // as they don't have a "read" status
      console.log(`Report submission notification ${numericId} marked as read (no database update needed)`);
    } else if (numericId >= 20000 && numericId < 30000) {
      // This is a report update notification
      // For report updates, we don't need to update anything in the database
      // as they don't have a "read" status
      console.log(`Report update notification ${numericId} marked as read (no database update needed)`);
    } else if (numericId >= 30000 && numericId < 40000) {
      // This is a verification notification
      // For verification notifications, we don't need to update anything in the database
      // as they don't have a "read" status
      console.log(`Verification notification ${numericId} marked as read (no database update needed)`);
    }
    
    res.json({
      success: true,
      message: "Notification marked as read"
    });
  } catch (error) {
    console.error("Error marking notification as read:", error);
    res.status(500).json({
      success: false,
      message: "Failed to mark notification as read",
      error: error.message
    });
  }
};

module.exports = {
  getUserNotifications,
  markNotificationAsRead
};