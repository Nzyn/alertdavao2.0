// API service for report-related operations
import { API_URL } from '../config/backend';

// Use the local Node.js backend instead of Laravel
const BACKEND_URL = API_URL;

export interface ReportSubmissionData {
  title: string;
  crimeTypes: string[];
  description: string;
  incidentDate: string;
  isAnonymous: boolean;
  latitude?: number;
  longitude?: number;
  media?: {
    uri: string;
    fileName?: string;
    fileSize?: number;
    type?: string;
  } | null;
  userId: string;
}

export const reportService = {
  // Submit a new report
  async submitReport(reportData: ReportSubmissionData) {
    try {
      console.log('Submitting report:', reportData);
      
      // Create FormData for multipart upload (supports file upload)
      const formData = new FormData();
      
      // Add basic report fields
      formData.append('title', reportData.title);
      formData.append('crime_types', JSON.stringify(reportData.crimeTypes));
      formData.append('description', reportData.description);
      formData.append('incident_date', reportData.incidentDate);
      formData.append('is_anonymous', reportData.isAnonymous ? '1' : '0');
      formData.append('user_id', reportData.userId);
      
      // Add location data if available
      if (reportData.latitude !== undefined && reportData.longitude !== undefined) {
        formData.append('latitude', reportData.latitude.toString());
        formData.append('longitude', reportData.longitude.toString());
      }
      
      // Add media file if available
      if (reportData.media && reportData.media.uri) {
        console.log('📎 Preparing media file for upload...');
        console.log('   URI:', reportData.media.uri);
        console.log('   File name:', reportData.media.fileName);
        console.log('   File type:', reportData.media.type);
        console.log('   File size:', reportData.media.fileSize);
        
        // Extract file extension from URI or filename
        const getFileExtension = (uri: string, fileName?: string): string => {
          if (fileName) {
            const parts = fileName.split('.');
            if (parts.length > 1) return parts[parts.length - 1];
          }
          const uriParts = uri.split('.');
          if (uriParts.length > 1) return uriParts[uriParts.length - 1];
          return 'jpg'; // default
        };
        
        const fileExtension = getFileExtension(reportData.media.uri, reportData.media.fileName);
        const fileName = reportData.media.fileName || `evidence_${Date.now()}.${fileExtension}`;
        
        // Determine MIME type based on file extension
        // Don't trust reportData.media.type as it might be just 'image' or 'video'
        const ext = fileExtension.toLowerCase();
        let mimeType = 'image/jpeg'; // default
        
        // Map file extension to proper MIME type
        if (ext === 'png') mimeType = 'image/png';
        else if (ext === 'jpg' || ext === 'jpeg') mimeType = 'image/jpeg';
        else if (ext === 'gif') mimeType = 'image/gif';
        else if (ext === 'webp') mimeType = 'image/webp';
        else if (ext === 'mp4') mimeType = 'video/mp4';
        else if (ext === 'mov') mimeType = 'video/quicktime';
        else if (ext === 'avi') mimeType = 'video/x-msvideo';
        else if (ext === 'webm') mimeType = 'video/webm';
        
        console.log('🔍 File extension:', ext);
        console.log('📋 Determined MIME type:', mimeType);
        
        // CRITICAL FIX FOR REACT NATIVE WEB:
        // For web platform, we need to convert the file URI to a Blob/File
        // React Native Web's FormData doesn't work with uri/name/type objects like native does
        console.log('🌐 Platform: Web - Converting URI to Blob...');
        
        try {
          // Fetch the file from the URI and convert to Blob
          const fileResponse = await fetch(reportData.media.uri);
          const fileBlob = await fileResponse.blob();
          
          // Create a proper File object from the Blob
          const file = new File([fileBlob], fileName, { type: mimeType });
          
          console.log('📦 File object created:', {
            name: file.name,
            type: file.type,
            size: file.size
          });
          
          // Append the actual File object to FormData
          formData.append('media', file, fileName);
          
          console.log('✅ Media file added to FormData');
          console.log('   Field name: media');
          console.log('   File name:', fileName);
          console.log('   MIME type:', mimeType);
          console.log('   File size:', file.size, 'bytes');
        } catch (fileError) {
          console.error('❌ Error converting file:', fileError);
          throw new Error('Failed to process media file. Please try again.');
        }
      } else {
        console.log('⚠️  No media file to upload');
      }
      
      console.log('\n' + '='.repeat(50));
      console.log('🚀 Sending report to backend...');
      console.log('API URL:', `${BACKEND_URL}/reports`);
      console.log('Has media:', reportData.media ? 'YES' : 'NO');
      
      // CRITICAL DEBUG: Log FormData info
      console.log('\n📦 FormData Info:');
      console.log('   Media file included:', reportData.media ? 'YES' : 'NO');
      if (reportData.media) {
        console.log('   File URI:', reportData.media.uri);
        console.log('   File name:', reportData.media.fileName);
        console.log('   File type:', reportData.media.type);
      }
      console.log('='.repeat(50) + '\n');
      
      const response = await fetch(`${BACKEND_URL}/reports`, {
        method: 'POST',
        headers: {
          // Don't set Content-Type for FormData - let browser set it with boundary
          'Accept': 'application/json',
        },
        body: formData,
      });

      console.log('Response status:', response.status);
      
      if (!response.ok) {
        const errorText = await response.text();
        console.error('Server error response:', errorText);
        
        try {
          const errorJson = JSON.parse(errorText);
          const errorMessage = errorJson.message || `HTTP ${response.status}: ${response.statusText}`;
          throw new Error(errorMessage);
        } catch (parseError) {
          throw new Error(`Failed to submit report: HTTP ${response.status} - ${response.statusText}`);
        }
      }

      const data = await response.json();
      console.log('Report submitted successfully:', data);
      
      return data;
    } catch (error) {
      console.error('Error submitting report:', error);
      
      if (error instanceof TypeError && error.message.includes('fetch')) {
        throw new Error('Unable to connect to server. Please check your internet connection and try again.');
      }
      
      throw error;
    }
  },

  // Get user's report history
  async getUserReports(userId: string) {
    try {
      console.log('Fetching reports for user:', userId);
      
      const response = await fetch(`${BACKEND_URL}/reports/user/${userId}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        },
      });

      console.log('Response status:', response.status);
      
      if (!response.ok) {
        const errorText = await response.text();
        console.error('Server error response:', errorText);
        throw new Error(`Failed to fetch reports: HTTP ${response.status}`);
      }

      const data = await response.json();
      console.log('Reports fetched successfully:', data);
      
      return data;
    } catch (error) {
      console.error('Error fetching reports:', error);
      
      if (error instanceof TypeError && error.message.includes('fetch')) {
        throw new Error('Unable to connect to server. Please check your internet connection and try again.');
      }
      
      throw error;
    }
  },
};
