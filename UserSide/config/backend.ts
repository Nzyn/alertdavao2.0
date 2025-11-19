import { Platform } from 'react-native';
import Constants from 'expo-constants';
import { getBackendUrlSync, findWorkingBackendUrl } from '../utils/networkUtils';

/**
 * Backend API Configuration
 * 
 * NOW AUTOMATICALLY DETECTS YOUR NETWORK!
 * No more manual IP updates needed - works on any network
 * 
 * The system will:
 * 1. Detect if you're on emulator/simulator (uses localhost)
 * 2. For physical devices, auto-discovers the backend server
 * 3. Tests multiple common network configurations
 * 4. Falls back to safe defaults if needed
 */

const BACKEND_PORT = 3000;

/**
 * Get the backend URL synchronously
 * Uses smart platform detection
 */
function getBackendUrl(): string {
  return getBackendUrlSync();
}

export const BACKEND_URL = getBackendUrl();
export const API_URL = `${BACKEND_URL}/api`;

// Log the configuration for debugging
console.log('🔧 Backend Configuration:');
console.log(`   Platform: ${Platform.OS}`);
console.log(`   Is Device: ${Constants.isDevice}`);
console.log(`   Backend URL: ${BACKEND_URL}`);
console.log(`   API URL: ${API_URL}`);

// Auto-detect best backend URL in background
let detectedBackendUrl: string | null = null;
findWorkingBackendUrl().then(url => {
  detectedBackendUrl = url;
  console.log('✅ Auto-detected backend URL:', url);
}).catch(err => {
  console.warn('⚠️ Could not auto-detect backend:', err);
});

/**
 * Get the auto-detected backend URL (async)
 * Use this for important operations to ensure connection
 */
export async function getOptimalBackendUrl(): Promise<string> {
  if (detectedBackendUrl) {
    return detectedBackendUrl;
  }
  return await findWorkingBackendUrl();
}

export default {
  BACKEND_URL,
  API_URL,
  BACKEND_PORT,
  getOptimalBackendUrl,
};
