import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  Button,
  TextInput,
  Pressable,
  ScrollView,
} from "react-native";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from "expo-router";
import { getOptimalBackendUrl } from '../config/backend';

import styles from "./(tabs)/styles";

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false); // 👈 Toggle state
  const [isLoading, setIsLoading] = useState(false);

  const router = useRouter();

  // Check if user is already logged in
  useEffect(() => {
    const checkLoggedIn = async () => {
      try {
        const userData = await AsyncStorage.getItem('userData');
        if (userData) {
          // User is already logged in, redirect to tabs
          router.replace("/(tabs)");
        }
      } catch (error) {
        console.log("Error checking login status:", error);
      }
    };

    checkLoggedIn();
  }, []);

  const handlePress = async () => {
    if (isLoading) return;
    
    setIsLoading(true);
    try {
      // Get the optimal backend URL automatically
      const backendUrl = await getOptimalBackendUrl();
      console.log('Using backend URL:', backendUrl);
      
      const response = await fetch(`${backendUrl}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (response.ok) {
         console.log("✅ Login successful:", data);
         
         const user = data.user || data;
         
         // Check user role
         if (user.role === 'police' || user.role === 'admin') {
           // Police and Admin users should log in via AdminSide
           alert('Police and Admin users must log in through the AdminSide dashboard.');
           return;
         }
         
         // Store user data in AsyncStorage
         try {
           await AsyncStorage.setItem('userData', JSON.stringify(user));
           console.log('User data stored successfully');
         } catch (storageError) {
           console.error('Error storing user data:', storageError);
         }
         
         // Regular users go to the Tabs group root (index tab)
         router.replace("/(tabs)");
       } else {
         alert(data.message || "Login failed");
       }
    } catch (err: any) {
      console.error("❌ Error logging in:", err.message || err);
      alert("Failed to connect to server. Please ensure the backend is running and you're on the same network.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleForgotPassword = () => {
    console.log("Forgot Password clicked!");
    // router.push("/forgot-password"); // enable if the route exists
  };

  const handleSignUp = () => {
    console.log("Sign Up clicked!");
    // Navigate to the Register UI
    router.push("/register");
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.container}>
        {/* Title */}
        <Text style={styles.textTitle}>
          <Text style={styles.alertWelcome}>Alert</Text>
          <Text style={styles.davao}>Davao</Text>
        </Text>

        <Text style={styles.subheadingCenter}>Welcome back to AlertDavao!</Text>
        <Text style={styles.normalTxtCentered}>Sign in to your account</Text>

        {/* Email */}
        <Text style={styles.subheading2}>Email</Text>
        <TextInput
          style={styles.input}
          placeholder="Enter your email"
          value={email}
          onChangeText={setEmail}
          keyboardType="email-address"
          editable={!isLoading}
        />

        {/* Password with toggle */}
        <Text style={styles.subheading2}>Password</Text>
        <View style={styles.passwordContainer}>
          <TextInput
            style={styles.inputFlex}
            placeholder="Enter your password"
            value={password}
            onChangeText={setPassword}
            secureTextEntry={!showPassword}
            editable={!isLoading}
          />
          <Pressable
            onPress={() => setShowPassword(!showPassword)}
            style={styles.showPasswordButton}
            disabled={isLoading}
          >
            <Text style={styles.showPasswordText}>
              {showPassword ? "Hide" : "Show"}
            </Text>
          </Pressable>
        </View>

        {/* Login Button */}
        <View style={styles.buttonWrapper}>
          <Button 
            title={isLoading ? "Logging in..." : "Login"} 
            onPress={handlePress} 
            color="#1D3557" 
            disabled={isLoading}
          />
        </View>

        {/* OnClick Texts */}
        <Pressable onPress={handleForgotPassword} disabled={isLoading}>
          <Text style={styles.forgotPasswordText}>
            Forgot Password?
          </Text>
        </Pressable>

        <Pressable onPress={handleSignUp} disabled={isLoading}>
          <Text style={styles.signUpText}>
            Don't have an account? Sign Up
          </Text>
        </Pressable>
      </View>
    </ScrollView>
  );
};

export default Login;