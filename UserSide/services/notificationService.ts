// API service for notification-related operations
import { API_URL } from '../config/backend';
import { reportService } from './reportService';

// Use the local Node.js backend
const BACKEND_URL = API_URL;

export interface Notification {
  id: number;
  title: string;
  message: string;
  timestamp: string;
  read: boolean;
  type: 'report' | 'verification' | 'message';
  relatedId?: number; // ID of the related report or other entity
}

export const notificationService = {
  // Get user notifications from the backend
  async getUserNotifications(userId: string) {
    try {
      console.log('Fetching notifications for user:', userId);
      
      const response = await fetch(`${BACKEND_URL}/notifications/${userId}`);
      
      if (!response.ok) {
        throw new Error(`Failed to fetch notifications: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data.success) {
        console.log('Notifications fetched successfully:', data.data);
        // Ensure notifications are sorted by timestamp (newest first)
        const sortedNotifications = data.data.sort((a: Notification, b: Notification) => 
          new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
        );
        return sortedNotifications as Notification[];
      } else {
        throw new Error(data.message || 'Failed to fetch notifications');
      }
    } catch (error) {
      console.error('Error fetching notifications:', error);
      throw error;
    }
  },
  
  // Mark a notification as read (this updates the backend)
  async markAsRead(notificationId: number, userId: string) {
    try {
      console.log('Marking notification as read:', notificationId);
      
      const response = await fetch(`${BACKEND_URL}/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ userId }),
      });
      
      if (!response.ok) {
        throw new Error(`Failed to mark notification as read: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (!data.success) {
        throw new Error(data.message || 'Failed to mark notification as read');
      }
      
      return { success: true };
    } catch (error) {
      console.error('Error marking notification as read:', error);
      throw error;
    }
  }
};