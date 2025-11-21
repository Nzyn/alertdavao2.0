import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, FlatList, StyleSheet, ActivityIndicator, KeyboardAvoidingView, Platform } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router, useLocalSearchParams } from 'expo-router';
import styles from "./styles";
import { useUser } from '../../contexts/UserContext';
import { messageService, Message } from '../../services/messageService';

const ChatScreen = () => {
  const { user } = useUser();
  const params = useLocalSearchParams();
  const otherUserId = params.otherUserId as string;
  const otherUserName = params.otherUserName as string;
  
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const [isOtherUserTyping, setIsOtherUserTyping] = useState(false);
  let typingTimeout: ReturnType<typeof setTimeout> | null = null;
  let typingCheckInterval: ReturnType<typeof setInterval> | null = null;

  const fetchMessages = async () => {
    if (!user || !user.id || !otherUserId) return;

    try {
      const response = await messageService.getMessages(parseInt(user.id), parseInt(otherUserId));
      
      if (response.success) {
        setMessages(response.data);
        // Mark conversation as read
        await messageService.markConversationAsRead(parseInt(user.id), parseInt(otherUserId));
      }
    } catch (error) {
      console.error('Error fetching messages:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    // Fetch immediately
    fetchMessages();
    
    // Poll for new messages every 2 seconds for better real-time feel
    const interval = setInterval(() => {
      console.log('🔄 Auto-refreshing messages from', otherUserName);
      fetchMessages();
    }, 2000);

    // Check typing status every 800ms
    typingCheckInterval = setInterval(() => {
      checkTypingStatus();
    }, 800);
    
    return () => {
      clearInterval(interval);
      if (typingCheckInterval) clearInterval(typingCheckInterval);
    };
  }, [user, otherUserId]);

  const sendMessage = async () => {
    if (newMessage.trim() === '' || !user || !user.id) {
      console.log('❌ Cannot send message - validation failed:', { 
        messageEmpty: newMessage.trim() === '', 
        userExists: !!user, 
        userId: user?.id 
      });
      return;
    }

    setSending(true);
    try {
      console.log('📨 Attempting to send message:', { 
        senderId: user.id, 
        receiverId: otherUserId, 
        message: newMessage.trim() 
      });
      
      const response = await messageService.sendMessage(
        parseInt(user.id),
        parseInt(otherUserId),
        newMessage.trim()
      );

      console.log('📨 Send message response:', response);

      if (response.success) {
        console.log('✅ Message sent, clearing input and refreshing...');
        setNewMessage('');
        // Refresh messages
        await fetchMessages();
      } else {
        console.error('❌ Message send failed:', response);
      }
    } catch (error) {
      console.error('❌ Error sending message:', error);
    } finally {
      setSending(false);
    }
  };

  const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  };

  const sendTypingStatus = async (isTyping: boolean) => {
    if (!user || !user.id || !otherUserId) return;

    try {
      const response = await fetch('http://192.168.1.4:3000/api/messages/typing', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          sender_id: user.id,
          receiver_id: parseInt(otherUserId),
          is_typing: isTyping
        })
      });
    } catch (error) {
      // Silent fail
    }
  };

  const checkTypingStatus = async () => {
    if (!user || !user.id || !otherUserId) return;

    try {
      const response = await fetch(`http://192.168.1.4:3000/api/messages/typing-status/${otherUserId}/${user.id}`, {
        headers: {
          'Content-Type': 'application/json',
        }
      });

      const data = await response.json();
      if (data.success) {
        setIsOtherUserTyping(data.is_typing);
      }
    } catch (error) {
      // Silent fail
    }
  };

  const renderMessage = ({ item }: { item: Message }) => {
    // Check if current user is the sender
    const isUserMessage = item.sender_id.toString() === user?.id;
    
    return (
      <View style={[
        styles.messageContainer,
        isUserMessage ? styles.userMsg : styles.officerMsg
      ]}>
        <Text style={[styles.messageText, isUserMessage && { color: '#fff' }]}>
          {item.message}
        </Text>
        <Text style={[styles.timeText, isUserMessage && { color: '#e0e0e0' }]}>
          {formatTime(item.sent_at)}
        </Text>
      </View>
    );
  };

  if (loading) {
    return (
      <View style={[styles.container, { justifyContent: 'center', alignItems: 'center' }]}>
        <ActivityIndicator size="large" color="#1D3557" />
      </View>
    );
  }

  return (
    <KeyboardAvoidingView 
      style={{ flex: 1 }} 
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
    >
      <View style={styles.container}>
        {/* Header with Back Button and Title */}
        <View style={styles.headerHistory}>
          <TouchableOpacity onPress={() => router.push('/chatlist')}>
            <Ionicons name="chevron-back" size={24} color="#000" />
          </TouchableOpacity>
          <View style={{ flex: 1, alignItems: 'center' }}>
            <Text style={styles.textTitle}>
              <Text style={styles.alertWelcome}>Alert</Text>
              <Text style={styles.davao}>Davao</Text>
            </Text>
            <Text style={styles.subheadingCenter}>{otherUserName || 'Chat'}</Text>
          </View>
          <View style={{ width: 24 }} />
        </View>

        {/* Chat Messages */}
        <FlatList
          data={messages}
          keyExtractor={(item) => item.message_id.toString()}
          renderItem={renderMessage}
          style={styles.chatArea}
          contentContainerStyle={{ paddingBottom: 10 }}
        />

        {/* Typing Indicator */}
        {isOtherUserTyping && (
          <View style={{ paddingHorizontal: 10, paddingVertical: 5 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 4 }}>
              <View style={{ 
                width: 8, 
                height: 8, 
                borderRadius: 4, 
                backgroundColor: '#999',
                opacity: 0.3
              }} />
              <View style={{ 
                width: 8, 
                height: 8, 
                borderRadius: 4, 
                backgroundColor: '#999',
                opacity: 0.6
              }} />
              <View style={{ 
                width: 8, 
                height: 8, 
                borderRadius: 4, 
                backgroundColor: '#999'
              }} />
              <Text style={{ fontSize: 12, color: '#999', marginLeft: 4, fontStyle: 'italic' }}>typing...</Text>
            </View>
          </View>
        )}

        {/* Input Area */}
         <View style={styles.inputContainer}>
           <TextInput
             style={styles.chatInput}
             placeholder="Write a message"
             value={newMessage}
             onChangeText={(text) => {
               setNewMessage(text);
               // Send typing status
               sendTypingStatus(true);
               
               // Clear previous timeout
               if (typingTimeout) clearTimeout(typingTimeout);
               
               // Set timeout to clear typing status after 3 seconds
               typingTimeout = setTimeout(() => {
                 sendTypingStatus(false);
               }, 3000);
             }}
             multiline
             editable={!sending}
           />
          <TouchableOpacity 
            style={[styles.sendButton, sending && { opacity: 0.5 }]} 
            onPress={sendMessage}
            disabled={sending || newMessage.trim() === ''}
          >
            {sending ? (
              <ActivityIndicator size="small" color="#fff" />
            ) : (
              <Ionicons name="send" size={20} color="#fff" />
            )}
          </TouchableOpacity>
        </View>
      </View>
    </KeyboardAvoidingView>
  );
};

export default ChatScreen;