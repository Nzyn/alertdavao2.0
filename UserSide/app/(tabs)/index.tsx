import { View, Text, StyleSheet, Pressable, Button, Animated, Platform, ScrollView } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import AsyncStorage from '@react-native-async-storage/async-storage';
import React, { useState, useEffect, useRef } from 'react';
import { router, Redirect } from 'expo-router';
import styles from "./styles"; 
import { Link } from 'expo-router';
import ConfirmDialog from '../../components/ConfirmDialog';
import NotificationPopup from '../../components/NotificationPopup';
import { notificationService } from '../../services/notificationService';
import type { Notification } from '../../services/notificationService';
import { useFocusEffect } from '@react-navigation/native';
import { useUser } from '../../contexts/UserContext';
import { messageService } from '../../services/messageService';

const App = () => {
  const { clearUser } = useUser();
  const [userFirstname, setUserFirstname] = useState('User');
  const [showLogoutDialog, setShowLogoutDialog] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [showNotifications, setShowNotifications] = useState(false);
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [userId, setUserId] = useState<string>('');
  const [lastUnreadCount, setLastUnreadCount] = useState(0); // Track last known unread count
  const [badgeHidden, setBadgeHidden] = useState(false); // Track if badge should be hidden permanently
  const [unreadChatCount, setUnreadChatCount] = useState(0); // Track unread chat messages
  
  // State for press effects
  const [pressStates, setPressStates] = useState({
    history: false,
    chat: false,
    profile: false,
    guidelines: false,
    location: false,
    logout: false,
    report: false,
  });

  // Animation values for each card
  const historyScale = useRef(new Animated.Value(1)).current;
  const chatScale = useRef(new Animated.Value(1)).current;
  const profileScale = useRef(new Animated.Value(1)).current;
  const guidelinesScale = useRef(new Animated.Value(1)).current;
  const locationScale = useRef(new Animated.Value(1)).current;
  const logoutScale = useRef(new Animated.Value(1)).current;
  const reportScale = useRef(new Animated.Value(1)).current;

  // Load user data on component mount
  useEffect(() => {
    const checkAuthStatus = async () => {
      try {
        const userData = await AsyncStorage.getItem('userData');
        if (userData) {
          const user = JSON.parse(userData);
          setUserFirstname(user.firstname || user.first_name || 'User');
          setIsLoggedIn(true);
          setUserId(user.id || user.user_id || '');
        } else {
          // No user data found, redirect to login
          router.replace('/login');
        }
      } catch (error) {
        console.error('Error checking auth status:', error);
        // In case of error, redirect to login
        router.replace('/login');
      } finally {
        setIsLoading(false);
      }
    };
    
    checkAuthStatus();
  }, []);

  // Load notifications when screen comes into focus or when userId changes
  useFocusEffect(
    React.useCallback(() => {
      if (userId) {
        loadNotifications(userId);
        loadUnreadChatCount(userId);
      }
    }, [userId])
  );

  // Load notifications for the user
  const loadNotifications = async (userId: string) => {
    try {
      if (!userId) return;
      
      console.log('Loading notifications for user:', userId);
      const userNotifications = await notificationService.getUserNotifications(userId);
      console.log('Received notifications:', userNotifications);
      setNotifications(userNotifications);
      
      // Check if there are new unread notifications
      const currentUnreadCount = userNotifications.filter(n => !n.read).length;
      
      // Only show badge if there are new unread notifications and badge isn't permanently hidden
      if (currentUnreadCount > lastUnreadCount && !badgeHidden) {
        // New notifications arrived, so we should show the badge
        setBadgeHidden(false);
      }
      
      // Update the last unread count
      setLastUnreadCount(currentUnreadCount);
    } catch (error) {
      console.error('Error loading notifications:', error);
    }
  };

  // Load unread chat count
  const loadUnreadChatCount = async (userId: string) => {
    try {
      if (!userId) return;
      
      console.log('Loading unread chat count for user:', userId);
      const response = await messageService.getUnreadCount(parseInt(userId));
      if (response.success) {
        console.log('Unread chat count:', response.count);
        setUnreadChatCount(response.count);
      }
    } catch (error) {
      console.error('Error loading unread chat count:', error);
    }
  };

  // Handle notification press - redirect to appropriate page
  const handleNotificationPress = async (notification: Notification) => {
    // Mark notification as read
    try {
      await notificationService.markAsRead(notification.id, userId);
      
      // Update the local state to mark the notification as read
      setNotifications(prevNotifications => 
        prevNotifications.map(n => 
          n.id === notification.id ? { ...n, read: true } : n
        )
      );
    } catch (error) {
      console.error('Failed to mark notification as read:', error);
    }
    
    // Close the popup
    setShowNotifications(false);
    
    // Redirect based on notification type
    switch (notification.type) {
      case 'report':
        // Redirect to history page
        router.push('/history');
        break;
      case 'verification':
        // Redirect to profile page
        router.push('/profile');
        break;
      case 'message':
        // Redirect to chat page
        router.push('/chatlist');
        break;
      default:
        // Do nothing for unknown types
        break;
    }
  };

  // If still loading, show nothing
  if (isLoading) {
    return null;
  }

  // If not logged in, redirect to login
  if (!isLoggedIn) {
    return <Redirect href="/login" />;
  }

  // Animation functions
  const animatePressIn = (scaleValue: Animated.Value) => {
    Animated.spring(scaleValue, {
      toValue: 0.95,
      useNativeDriver: true,
      friction: 8,
    }).start();
  };

  const animatePressOut = (scaleValue: Animated.Value) => {
    Animated.spring(scaleValue, {
      toValue: 1,
      useNativeDriver: true,
      friction: 8,
    }).start();
  };

  const handlePress = (name: string) => {
    //to be edited, this is where we put the backend for the logic of each pressable tiles
    console.log(name + " pressed!");
  };

  // Press handlers
  const handlePressIn = (component: string) => {
    setPressStates(prev => ({ ...prev, [component]: true }));
  };

  const handlePressOut = (component: string) => {
    setPressStates(prev => ({ ...prev, [component]: false }));
  };

  // Calculate unread notifications count
  const unreadCount = notifications.filter(n => !n.read).length;

  // Handle notification icon press
  const handleNotificationIconPress = () => {
    setShowNotifications(true);
    // Permanently hide the badge when notification icon is clicked
    setBadgeHidden(true);
  };

  return (
    <View style={{ flex: 1 }}>
      <ScrollView style={styles.container}>
        {/* Title */}
        <Text style={styles.textTitle}>
          <Text style={styles.alert}>Alert</Text>
          <Text style={styles.davao}>Davao</Text>
        </Text>

        {/* Subheading with Notification Icon */}
        <View style={styles.welcomeContainer}>
          <Text style={styles.subheading}>Welcome, {userFirstname}!</Text>
          <Pressable 
            style={styles.notificationIconContainer}
            onPress={handleNotificationIconPress}
          >
            <Ionicons 
              name="notifications-outline" 
              size={40} // Set to 40px as per project requirements
              color="#1D3557" 
            />
            {unreadCount > 0 && !badgeHidden && (
              <View style={styles.notificationBadge}>
                <Text style={styles.notificationBadgeText}>
                  {unreadCount}
                </Text>
              </View>
            )}
          </Pressable>
        </View>

        {/* Report Now Button */}
        <Link href="/report" asChild>
          <Pressable 
            style={pressStates.report ? styles.reportButtonPressed : styles.reportButton}
            onPressIn={() => handlePressIn('report')}
            onPressOut={() => handlePressOut('report')}
          >
            <Text style={styles.reportButtonText}>
              Report Now
            </Text>
          </Pressable>
        </Link>

        {/* Grid Container */}
        <View style={styles.grid}>
          {/* History */}
          <Link href="/history" asChild>
            <Pressable 
              style={pressStates.history ? styles.cardGridPressed : styles.cardGrid}
              onPressIn={() => handlePressIn('history')}
              onPressOut={() => handlePressOut('history')}
            >
              <View style={{ transform: [{ scale: pressStates.history ? 0.95 : 1 }] }}>
                <Ionicons name="time-outline" size={40} color="#1D3557" />
                <Text style={styles.cardText}>History</Text>
              </View>
            </Pressable>
          </Link>

          <Link href="/chatlist" asChild>
            <Pressable 
              style={pressStates.chat ? styles.cardGridPressed : styles.cardGrid}
              onPressIn={() => handlePressIn('chat')}
              onPressOut={() => handlePressOut('chat')}
            >
              <View style={{ transform: [{ scale: pressStates.chat ? 0.95 : 1 }] }}>
                <View style={{ position: 'relative' }}>
                  <Ionicons name="chatbox-outline" size={40} color="#1D3557" />
                  {unreadChatCount > 0 && (
                    <View style={styles.chatBadge}>
                      <Text style={styles.chatBadgeText}>
                        {unreadChatCount}
                      </Text>
                    </View>
                  )}
                </View>
                <Text style={styles.cardText}>Chat</Text>
              </View>
            </Pressable>
          </Link>

          {/* Profile */}
          <Link href="/profile" asChild>
            <Pressable 
              style={pressStates.profile ? styles.cardGridPressed : styles.cardGrid}
              onPressIn={() => handlePressIn('profile')}
              onPressOut={() => handlePressOut('profile')}
            >
              <View style={{ transform: [{ scale: pressStates.profile ? 0.95 : 1 }] }}>
                <Ionicons name="person-outline" size={40} color="#1D3557" />
                <Text style={styles.cardText}>Profile</Text>
              </View>
            </Pressable>
          </Link>

          {/* Guidelines */}
          <Link href="/guidelines" asChild>
            <Pressable 
              style={pressStates.guidelines ? styles.cardGridPressed : styles.cardGrid}
              onPressIn={() => handlePressIn('guidelines')}
              onPressOut={() => handlePressOut('guidelines')}
            >
              <View style={{ transform: [{ scale: pressStates.guidelines ? 0.95 : 1 }], alignItems: 'center', justifyContent: 'center' }}>
                <Ionicons
                  name="information-circle-outline"
                  size={40}
                  color="#1D3557"
                />
                <Text style={styles.cardText}>Guidelines</Text>
              </View>
            </Pressable>
          </Link>

          {/* Location */}
          <Link href="/(tabs)/location" asChild>
            <Pressable 
              style={pressStates.location ? styles.cardGridPressed : styles.cardGrid}
              onPressIn={() => handlePressIn('location')}
              onPressOut={() => handlePressOut('location')}
            >
              <View style={{ transform: [{ scale: pressStates.location ? 0.95 : 1 }] }}>
                <Ionicons name="business-outline" size={40} color="#1D3557" />
                <Text style={styles.cardText}>Station</Text>
              </View>
            </Pressable>
          </Link>

          {/* Logout */}
          <Pressable
            style={pressStates.logout ? styles.cardGridPressed : styles.cardGrid}
            onPress={() => setShowLogoutDialog(true)}
            onPressIn={() => handlePressIn('logout')}
            onPressOut={() => handlePressOut('logout')}
          >
            <View style={{ transform: [{ scale: pressStates.logout ? 0.95 : 1 }] }}>
              <Ionicons name="log-out-outline" size={40} color="#1D3557" />
              <Text style={styles.cardText}>Logout</Text>
            </View>
          </Pressable>

        </View>

        <ConfirmDialog
          visible={showLogoutDialog}
          title="Confirm Logout"
          message="Are you sure you want to log out?"
          cancelText="Cancel"
          confirmText="Logout"
          onCancel={() => {
            setShowLogoutDialog(false);
            // User stays on current page (index)
          }}
          onConfirm={async () => {
            setShowLogoutDialog(false);
            try {
              // Clear user data from AsyncStorage
              await AsyncStorage.removeItem('userData');
              console.log('✅ User logged out - AsyncStorage cleared');
              
              // Clear user context
              clearUser();
              console.log('✅ User context cleared');
              
              // Redirect to login
              router.replace('/login');
            } catch (error) {
              console.error('❌ Error logging out:', error);
            }
          }}
        />
      </ScrollView>
      
      {/* Notification Popup */}
      <NotificationPopup
        visible={showNotifications}
        notifications={notifications}
        onClose={() => {
          setShowNotifications(false);
          // Refresh notifications to update the count
          if (userId) {
            loadNotifications(userId);
          }
        }}
        onNotificationPress={handleNotificationPress}
      />
    </View>
  );
};

export default App;