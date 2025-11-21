const express = require("express");
const cors = require("cors");
const path = require('path');
const multer = require('multer');

// Configure multer for evidence files (reports)
const evidenceStorage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, path.join(__dirname, '../evidence'));
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, 'evidence-' + uniqueSuffix + path.extname(file.originalname));
  }
});

// Configure multer for verification files
const verificationStorage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, path.join(__dirname, '../verifications'));
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, 'verification-' + uniqueSuffix + path.extname(file.originalname));
  }
});

const evidenceUpload = multer({ 
  storage: evidenceStorage,
  fileFilter: function (req, file, cb) {
    // Accept only image files
    if (file.mimetype.startsWith('image/')) {
      cb(null, true);
    } else {
      cb(new Error('Only image files are allowed!'), false);
    }
  },
  limits: {
    fileSize: 5 * 1024 * 1024 // 5MB limit
  }
});

const verificationUpload = multer({ 
  storage: verificationStorage,
  fileFilter: function (req, file, cb) {
    // Accept only image files
    if (file.mimetype.startsWith('image/')) {
      cb(null, true);
    } else {
      cb(new Error('Only image files are allowed!'), false);
    }
  },
  limits: {
    fileSize: 5 * 1024 * 1024 // 5MB limit
  }
});

const handleRegister = require("./handleRegister");
const handleLogin = require("./handleLogin");
const {
  testConnection,
  getUserById,
  upsertUser,
  updateUserAddress,
  executeQuery
} = require("./handleUserProfile");
const {
  upload: reportUpload,
  submitReport,
  getUserReports,
  getAllReports
} = require("./handleReport");
const {
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
} = require("./handleNewFeatures");

// Add this new function for handling notifications
const { getUserNotifications, markNotificationAsRead } = require("./handleNotifications");

// Add location service handler
const { searchLocation, reverseGeocode, getDistance } = require("./handleLocation");

const app = express();
const PORT = process.env.PORT || 3000;

// Middlewares
app.use(cors({ origin: "*" }));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve uploaded files statically from evidence folder
app.use('/evidence', express.static(path.join(__dirname, '../evidence')));

// Serve uploaded files statically from verifications folder
app.use('/verifications', express.static(path.join(__dirname, '../verifications')));

// Backward compatibility: serve old uploads from uploads/evidence folder
app.use('/uploads/evidence', express.static(path.join(__dirname, '../uploads/evidence')));

// Debug logger - BEFORE multer processes the request
app.use((req, res, next) => {
  console.log("\n" + "=".repeat(50));
  console.log("📨 INCOMING REQUEST:");
  console.log("   Method:", req.method);
  console.log("   URL:", req.url);
  console.log("   Content-Type:", req.headers['content-type']);
  console.log("   Body keys:", Object.keys(req.body));
  console.log("=".repeat(50) + "\n");
  next();
});

// Authentication Routes
app.post("/register", handleRegister);
app.post("/login", handleLogin);

// User Profile API Routes
app.get("/api/test-connection", testConnection);
app.get("/api/users/:id", getUserById);
app.post("/api/users/upsert", upsertUser);
app.patch("/api/users/:id/address", updateUserAddress);
app.post("/api/query", executeQuery);

// Report API Routes - with detailed logging
app.post("/api/reports", (req, res, next) => {
  console.log("\n🎯 REPORT ENDPOINT HIT");
  console.log("   Content-Type:", req.headers['content-type']);
  console.log("   Is multipart?", req.headers['content-type']?.includes('multipart'));
  next();
}, reportUpload.single('media'), (req, res, next) => {
  console.log("\n📦 AFTER MULTER:");
  console.log("   req.file exists?", !!req.file);
  console.log("   req.file:", req.file);
  console.log("   req.body:", req.body);
  next();
}, submitReport);
app.get("/api/reports", getAllReports);
app.get("/api/reports/user/:userId", getUserReports);

// Police Stations API Routes
app.get("/api/police-stations", getAllPoliceStations);
app.get("/api/police-stations/:id", getPoliceStationById);

// User Roles API Routes
app.get("/api/users/:userId/roles", getUserRoles);
app.post("/api/users/roles/assign", assignUserRole);

// Verification API Routes
app.post("/api/verification/submit", submitVerification);
app.post("/api/verification/upload", verificationUpload.single('document'), uploadVerificationDocument);
app.get("/api/verification/status/:userId", getVerificationStatus);
app.put("/api/verification/:verificationId/update", updateVerification);
app.post("/api/verification/approve", approveVerification);
app.post("/api/verification/reject", rejectVerification);

// Messages API Routes
// IMPORTANT: Order matters! More specific routes must come before less specific routes
app.get("/api/messages/conversations/:userId", getUserConversations);
app.get("/api/messages/unread/:userId", getUnreadCount);
app.post("/api/messages/typing", updateUserTypingStatus);
app.get("/api/messages/typing-status/:senderId/:receiverId", checkUserTypingStatus);
app.post("/api/messages", sendMessage);
app.patch("/api/messages/conversation/read", markConversationAsRead);
app.patch("/api/messages/:messageId/read", markMessageAsRead);
app.get("/api/messages/:userId/:otherUserId", getMessagesBetweenUsers);
app.get("/api/messages/:userId", getUserMessages);

// Notifications API Routes
app.get("/api/notifications/:userId", getUserNotifications);
app.patch("/api/notifications/:notificationId/read", markNotificationAsRead);

// Crime Analytics API Routes
app.get("/api/analytics", getAllCrimeAnalytics);
app.get("/api/analytics/:locationId", getCrimeAnalytics);

// Crime Forecasts API Routes
app.get("/api/forecasts/:locationId", getCrimeForecasts);

// Location Service API Routes
app.get("/api/location/search", searchLocation);
app.get("/api/location/reverse", reverseGeocode);
app.get("/api/location/distance", getDistance);

// Geocoding API Route (legacy, kept for backward compatibility)
app.post("/api/geocode", async (req, res) => {
  try {
    const { address } = req.body;
    
    if (!address || address.trim().length === 0) {
      return res.status(400).json({ error: "Address is required" });
    }
    
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`,
      {
        headers: {
          'User-Agent': 'AlertDavao/2.0 (Crime Reporting App)',
        }
      }
    );
    
    if (!response.ok) {
      throw new Error(`Nominatim API error: ${response.status}`);
    }
    
    const data = await response.json();
    res.json(data);
  } catch (error) {
    console.error("Geocoding error:", error);
    res.status(500).json({ error: "Failed to geocode address" });
  }
});

// Start server
app.listen(PORT, "0.0.0.0", () => {
  console.log(`🚀 Server running at http://localhost:${PORT}`);
});