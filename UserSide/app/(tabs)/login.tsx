import React, { useState } from "react";
import {
  View,
  Text,
  Button,
  TextInput,
  Pressable,
  ScrollView,
  Platform,
} from "react-native";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from "expo-router";

import styles from "./styles";

const BASE_URL =
  Platform.OS === "android"
    ? "http://10.0.2.2:3000" // For Android Emulator
    : "http://192.168.1.4:3000"; // For Physical devices / iOS simulator

const Login = () => {
   const [email, setEmail] = useState("");
   const [password, setPassword] = useState("");
   const [showPassword, setShowPassword] = useState(false); // 👈 Toggle state
   const [isLoading, setIsLoading] = useState(false);

   const router = useRouter();
   
   console.log('🔐 Login component loaded, BASE_URL:', BASE_URL);

  const handlePress = async () => {
    if (isLoading) return;
    
    if (!email || !password) {
      alert("Please enter email and password");
      return;
    }
    
    setIsLoading(true);
    console.log('🔐 Attempting login with:', { email, password: '***' });
    console.log('📍 Using backend URL:', BASE_URL);
    
    try {
      console.log('🌐 Sending login request...');
      const response = await fetch(`${BASE_URL}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      console.log('📦 Response status:', response.status);
      const data = await response.json();
      console.log('📥 Response data:', data);

      if (response.ok) {
         console.log("✅ Login successful:", data);
         
         const user = data.user || data;
         
         // Check user role
         if (user.role === 'police' || user.role === 'admin') {
           // Police and Admin users should log in via AdminSide
           alert('Police and Admin users must log in through the AdminSide dashboard.');
           setIsLoading(false);
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
         setIsLoading(false);
       }
    } catch (err: any) {
      console.error("❌ Error logging in:", err);
      console.error("Error message:", err.message);
      console.error("Error stack:", err.stack);
      alert("Network error: " + (err.message || "Unknown"));
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
        />

        {/* Password with toggle */}
        <Text style={styles.subheading2}>Password</Text>
        <View style={{ flexDirection: "row", alignItems: "center" }}>
          <TextInput
            style={[styles.input, { flex: 1 }]}
            placeholder="Enter your password"
            value={password}
            onChangeText={setPassword}
            secureTextEntry={!showPassword}
          />
          <Pressable
            onPress={() => setShowPassword(!showPassword)}
            style={{ marginLeft: 10 }}
          >
            <Text style={{ color: "#1D3557" }}>
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
        <Pressable onPress={handleForgotPassword}>
          <Text
            style={{ color: "#1D3557", marginTop: 10, textAlign: "center" }}
          >
            Forgot Password?
          </Text>
        </Pressable>

        <Pressable onPress={handleSignUp}>
          <Text
            style={{ color: "#457b9d", marginTop: 10, textAlign: "center" }}
          >
            Don’t have an account? Sign Up
          </Text>
        </Pressable>
      </View>
    </ScrollView>
  );
};

export default Login;
