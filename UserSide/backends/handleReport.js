const db = require("./db");
const multer = require("multer");
const path = require("path");
const fs = require("fs");

// Configure multer for file uploads
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const uploadDir = path.join(__dirname, "../evidence");
    // Create directory if it doesn't exist
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    cb(null, uploadDir);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + "-" + Math.round(Math.random() * 1e9);
    const ext = path.extname(file.originalname);
    cb(null, "evidence-" + uniqueSuffix + ext);
  },
});

const upload = multer({
  storage: storage,
  limits: { fileSize: 25 * 1024 * 1024 }, // 25MB limit
  fileFilter: function (req, file, cb) {
    const allowedTypes = /jpeg|jpg|png|gif|mp4|mov|avi/;
    const extname = allowedTypes.test(
      path.extname(file.originalname).toLowerCase()
    );
    const mimetype = allowedTypes.test(file.mimetype);

    if (mimetype && extname) {
      return cb(null, true);
    } else {
      cb(new Error("Only images and videos are allowed!"));
    }
  },
});

// Submit a new report
async function submitReport(req, res) {
  const connection = await db.getConnection();
  
  try {
    await connection.beginTransaction();

    const {
      title,
      crime_types,
      description,
      incident_date,
      is_anonymous,
      user_id,
      latitude,
      longitude,
      reporters_address,
    } = req.body;

    console.log("📝 Submitting report:", {
      title,
      crime_types,
      description,
      incident_date,
      is_anonymous,
      user_id,
      latitude,
      longitude,
      reporters_address,
      hasFile: !!req.file,
      fileDetails: req.file ? {
        fieldname: req.file.fieldname,
        originalname: req.file.originalname,
        encoding: req.file.encoding,
        mimetype: req.file.mimetype,
        size: req.file.size,
        destination: req.file.destination,
        filename: req.file.filename,
        path: req.file.path
      } : null
    });

    // Validation
    if (!title || !crime_types || !description || !incident_date || !user_id) {
      await connection.rollback();
      return res.status(422).json({
        success: false,
        message: "Missing required fields",
        errors: {
          title: !title ? ["Title is required"] : [],
          crime_types: !crime_types ? ["Crime type is required"] : [],
          description: !description ? ["Description is required"] : [],
          incident_date: !incident_date ? ["Incident date is required"] : [],
          user_id: !user_id ? ["User ID is required"] : [],
        },
      });
    }

    // Parse crime types if it's a JSON string
    let crimeTypesArray;
    try {
      crimeTypesArray =
        typeof crime_types === "string"
          ? JSON.parse(crime_types)
          : crime_types;
    } catch (e) {
      crimeTypesArray = [crime_types];
    }
    const reportType = Array.isArray(crimeTypesArray)
      ? crimeTypesArray.join(", ")
      : crime_types;

    // Create location record
    const lat = latitude ? parseFloat(latitude) : 0;
    const lng = longitude ? parseFloat(longitude) : 0;
    const barangay = lat !== 0 && lng !== 0 ? `Lat: ${lat}, Lng: ${lng}` : "Unknown";
    const address = reporters_address || null;

    const [locationResult] = await connection.query(
      "INSERT INTO locations (barangay, reporters_address, latitude, longitude, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())",
      [barangay, address, lat, lng]
    );

    const locationId = locationResult.insertId;
    console.log("✅ Location created with ID:", locationId);

    // Create report record
    const isAnon = is_anonymous === "1" || is_anonymous === true || is_anonymous === "true";
    
    const [reportResult] = await connection.query(
      `INSERT INTO reports 
       (user_id, location_id, title, report_type, description, date_reported, status, is_anonymous, created_at, updated_at) 
       VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW())`,
      [user_id, locationId, title, reportType, description, incident_date, isAnon]
    );

    const reportId = reportResult.insertId;
    console.log("✅ Report created with ID:", reportId);

    // Handle media upload if file exists
    let mediaData = null;
    if (req.file) {
      console.log("📸 Processing file upload...");
      console.log("   Original name:", req.file.originalname);
      console.log("   Saved as:", req.file.filename);
      console.log("   Size:", req.file.size, "bytes");
      console.log("   MIME type:", req.file.mimetype);
      console.log("   Saved to:", req.file.path);
      
      const mediaUrl = `/evidence/${req.file.filename}`;
      const mediaType = path.extname(req.file.originalname).substring(1).toLowerCase();

      console.log("   Media URL:", mediaUrl);
      console.log("   Media Type:", mediaType);

      try {
        const [mediaResult] = await connection.query(
          "INSERT INTO report_media (report_id, media_url, media_type, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())",
          [reportId, mediaUrl, mediaType]
        );

        mediaData = {
          media_id: mediaResult.insertId,
          media_url: mediaUrl,
          media_type: mediaType,
          file_size: req.file.size,
          original_name: req.file.originalname,
        };

        console.log("✅ Media uploaded successfully!");
        console.log("   Media ID:", mediaResult.insertId);
        console.log("   Stored in database: report_media table");
        console.log("   File location: evidence/", req.file.filename);
      } catch (dbError) {
        console.error("❌ Database insertion failed:", dbError);
        throw new Error("Failed to save media to database: " + dbError.message);
      }
    } else {
      console.log("⚠️  No file uploaded with this report");
    }

    // Commit transaction
    await connection.commit();

    // Return success response
    res.status(201).json({
      success: true,
      message: "Report submitted successfully",
      data: {
        report_id: reportId,
        title: title,
        report_type: reportType,
        status: "pending",
        is_anonymous: isAnon,
        date_reported: incident_date,
        location: {
          location_id: locationId,
          latitude: lat,
          longitude: lng,
          barangay: barangay,
          reporters_address: address,
        },
        media: mediaData,
      },
    });

    console.log("🎉 Report submitted successfully!");
  } catch (error) {
    await connection.rollback();
    console.error("❌ Error submitting report:", error);
    res.status(500).json({
      success: false,
      message: "Failed to submit report: " + error.message,
    });
  } finally {
    connection.release();
  }
}

// Get reports for a specific user
async function getUserReports(req, res) {
  try {
    const { userId } = req.params;

    const [reports] = await db.query(
      `SELECT 
        r.report_id,
        r.title,
        r.report_type,
        r.description,
        r.status,
        r.is_anonymous,
        r.date_reported,
        r.created_at,
        l.latitude,
        l.longitude,
        l.barangay,
        l.reporters_address,
        GROUP_CONCAT(CONCAT(rm.media_id, ':', rm.media_url, ':', rm.media_type) SEPARATOR '|') as media
      FROM reports r
      LEFT JOIN locations l ON r.location_id = l.location_id
      LEFT JOIN report_media rm ON r.report_id = rm.report_id
      WHERE r.user_id = ? 
        AND r.location_id IS NOT NULL 
        AND r.location_id != 0
        AND l.latitude IS NOT NULL 
        AND l.longitude IS NOT NULL
        AND l.latitude != 0
        AND l.longitude != 0
      GROUP BY r.report_id
      ORDER BY r.created_at DESC`,
      [userId]
    );

    // Parse media data
    const formattedReports = reports.map((report) => {
      let mediaArray = [];
      if (report.media) {
        mediaArray = report.media.split("|").map((m) => {
          const [media_id, media_url, media_type] = m.split(":");
          return { media_id: parseInt(media_id), media_url, media_type };
        });
      }

      return {
        report_id: report.report_id,
        title: report.title,
        report_type: report.report_type,
        description: report.description,
        status: report.status,
        is_anonymous: Boolean(report.is_anonymous),
        date_reported: report.date_reported,
        created_at: report.created_at,
        location: {
          latitude: report.latitude,
          longitude: report.longitude,
          barangay: report.barangay,
          reporters_address: report.reporters_address,
        },
        media: mediaArray,
      };
    });

    res.json({
      success: true,
      data: formattedReports,
    });
  } catch (error) {
    console.error("❌ Error fetching user reports:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch reports",
    });
  }
}

// Get all reports
async function getAllReports(req, res) {
  try {
    const [reports] = await db.query(
      `SELECT 
        r.report_id,
        r.title,
        r.report_type,
        r.description,
        r.status,
        r.is_anonymous,
        r.date_reported,
        r.created_at,
        r.user_id,
        l.latitude,
        l.longitude,
        l.barangay,
        l.reporters_address,
        u.firstname,
        u.lastname,
        u.email,
        GROUP_CONCAT(CONCAT(rm.media_id, ':', rm.media_url, ':', rm.media_type) SEPARATOR '|') as media
      FROM reports r
      LEFT JOIN locations l ON r.location_id = l.location_id
      LEFT JOIN users u ON r.user_id = u.id
      LEFT JOIN report_media rm ON r.report_id = rm.report_id
      WHERE r.location_id IS NOT NULL 
        AND r.location_id != 0
        AND l.latitude IS NOT NULL 
        AND l.longitude IS NOT NULL
        AND l.latitude != 0
        AND l.longitude != 0
      GROUP BY r.report_id
      ORDER BY r.created_at DESC`
    );

    // Parse media data
    const formattedReports = reports.map((report) => {
      let mediaArray = [];
      if (report.media) {
        mediaArray = report.media.split("|").map((m) => {
          const [media_id, media_url, media_type] = m.split(":");
          return { media_id: parseInt(media_id), media_url, media_type };
        });
      }

      return {
        report_id: report.report_id,
        title: report.title,
        report_type: report.report_type,
        description: report.description,
        status: report.status,
        is_anonymous: Boolean(report.is_anonymous),
        date_reported: report.date_reported,
        created_at: report.created_at,
        user: {
          user_id: report.user_id,
          firstname: report.firstname,
          lastname: report.lastname,
          email: report.email,
        },
        location: {
          latitude: report.latitude,
          longitude: report.longitude,
          barangay: report.barangay,
          reporters_address: report.reporters_address,
        },
        media: mediaArray,
      };
    });

    res.json({
      success: true,
      data: formattedReports,
    });
  } catch (error) {
    console.error("❌ Error fetching all reports:", error);
    res.status(500).json({
      success: false,
      message: "Failed to fetch reports",
    });
  }
}

module.exports = {
  upload,
  submitReport,
  getUserReports,
  getAllReports,
};
