import React from 'react';
import { View, Text, ScrollView, Pressable } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import styles from '../app/(tabs)/styles';
import type { Notification } from '../services/notificationService';

interface NotificationPopupProps {
  visible: boolean;
  notifications: Notification[];
  onClose: () => void;
  onNotificationPress: (notification: Notification) => void;
}

const NotificationPopup: React.FC<NotificationPopupProps> = ({ 
  visible, 
  notifications, 
  onClose,
  onNotificationPress
}) => {
  if (!visible) {
    return null;
  }

  // Sort notifications by timestamp (newest first)
  const sortedNotifications = [...notifications].sort((a, b) => 
    new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
  );

  return (
    <View style={styles.notificationContainer}>
      <View style={styles.notificationPopup}>
        <View style={styles.notificationHeader}>
          <Text style={styles.notificationTitle}>Notifications</Text>
          <Pressable 
            style={styles.closeButton}
            onPress={onClose}
          >
            <Ionicons name="close" size={24} color="#1D3557" />
          </Pressable>
        </View>
        
        {sortedNotifications.length === 0 ? (
          <Text style={styles.noNotificationsText}>No notifications</Text>
        ) : (
          <ScrollView style={styles.notificationList}>
            {sortedNotifications.map((notification) => (
              <Pressable
                key={notification.id}
                style={({ pressed }) => [
                  styles.notificationItem,
                  !notification.read && styles.notificationItemUnread,
                  pressed && !notification.read && { backgroundColor: '#e3f2fd' }, // Light blue when pressed (unread only)
                  pressed && notification.read && { backgroundColor: '#e8e8e8' }, // Light gray when pressed (read only)
                ]}
                onPress={() => onNotificationPress(notification)}
              >
                <Text style={[
                  styles.notificationItemTitle,
                  notification.read && { color: '#888888' } // Gray text for read notifications
                ]}>
                  {notification.title}
                </Text>
                <Text style={[
                  styles.notificationItemMessage,
                  notification.read && { color: '#999999' } // Gray text for read notifications
                ]}>
                  {notification.message}
                </Text>
                <Text style={[
                  styles.notificationItemTime,
                  notification.read && { color: '#aaaaaa' } // Light gray text for read notifications
                ]}>
                  {new Date(notification.timestamp).toLocaleDateString('en-PH', {
                    timeZone: 'Asia/Manila'
                  })} at {new Date(notification.timestamp).toLocaleTimeString('en-PH', {
                    timeZone: 'Asia/Manila',
                    hour: '2-digit',
                    minute: '2-digit'
                  })}
                </Text>
              </Pressable>
            ))}
          </ScrollView>
        )}
      </View>
    </View>
  );
};

export default NotificationPopup;