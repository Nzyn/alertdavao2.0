import React, { useState } from "react";
import { View, Text, Button, TextInput, ScrollView, Platform, TouchableOpacity, Alert } from "react-native";
import Checkbox from "expo-checkbox";
import styles from "./styles";
import { useRouter } from 'expo-router';

const getBackendUrl = () => {
  if (Platform.OS === "android") {
    return "http://10.0.2.2:3000/register"; // Android Emulator
  } else if (Platform.OS === "ios") {
    return "http://localhost:3000/register"; // iOS Simulator
  } else {
    return "http://192.168.1.4:3000/register"; // Physical device
  }
};

const Register = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmpassword, setConfirmPassword] = useState("");
  const [firstname, setFirstname] = useState("");
  const [lastname, setLastname] = useState("");
  const [contact, setContact] = useState("");
  const [isChecked, setChecked] = useState(false);
  const router = useRouter();

  const handleRegister = async () => {
    if (!isChecked) {
      alert("You must accept the Terms & Conditions before registering.");
      return;
    }

    if (password !== confirmpassword) {
      alert("Passwords do not match!");
      return;
    }

    try {
      const response = await fetch(getBackendUrl(), {
        method: "POST",
        headers: {
          "Content-Type": "application/json", // ✅ ensures JSON body is parsed
        },
        body: JSON.stringify({
          firstname,
          lastname,
          email,
          contact,
          password,
        }),
      });

      const data = await response.json();
      console.log("Response from backend:", data);

      if (response.ok) {
        // Redirect to login page after successful registration
        Alert.alert(
          "Registration Successful!",
          "Your account has been created successfully. Please login to continue.",
          [
            {
              text: "OK",
              onPress: () => router.replace('/login')
            }
          ]
        );
      } else {
        alert(data.message || "Registration failed");
      }
    } catch (error) {
      console.error("Fetch error:", error);
      alert("Failed to connect to server");
    }
  };

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={{ paddingBottom: 50 }}
    >
      {/* Title */}
      <Text style={styles.textTitle}>
        <Text style={styles.alertWelcome}>Alert</Text>
        <Text style={styles.davao}>Davao</Text>
      </Text>

      <Text style={styles.subheadingCenter}>Welcome to AlertDavao!</Text>
      <Text style={styles.normalTxtCentered}>
        Register and Create an Account
      </Text>

      {/* Firstname */}
      <Text style={styles.subheading2}>Firstname</Text>
      <TextInput
        style={styles.input}
        placeholder="Enter your first name"
        value={firstname}
        onChangeText={setFirstname}
      />

      {/* Lastname */}
      <Text style={styles.subheading2}>Lastname</Text>
      <TextInput
        style={styles.input}
        placeholder="Enter your last name"
        value={lastname}
        onChangeText={setLastname}
      />

      {/* Email */}
      <Text style={styles.subheading2}>Email</Text>
      <TextInput
        style={styles.input}
        placeholder="Enter your email"
        value={email}
        onChangeText={setEmail}
      />

      {/* Contact */}
      <Text style={styles.subheading2}>Contact Number</Text>
      <TextInput
        style={styles.input}
        placeholder="Enter your contact number"
        value={contact}
        onChangeText={setContact}
        keyboardType="phone-pad"
        maxLength={11}
      />

      {/* Password */}
      <Text style={styles.subheading2}>Password</Text>
      <TextInput
        style={styles.input}
        placeholder="Enter your password"
        value={password}
        onChangeText={setPassword}
        secureTextEntry
      />

      {/* Confirm Password */}
      <Text style={styles.subheading2}>Confirm Password</Text>
      <TextInput
        style={styles.input}
        placeholder="Re-enter your password"
        value={confirmpassword}
        onChangeText={setConfirmPassword}
        secureTextEntry
      />

      {/* Checkbox with disclaimer */}
      <View
        style={{
          flexDirection: "row",
          alignItems: "flex-start",
          marginTop: 10,
        }}
      >
        <Checkbox
          value={isChecked}
          onValueChange={setChecked}
          color={isChecked ? "#1D3557" : undefined}
        />
        <Text
          style={{
            fontSize: 12,
            color: "#555",
            marginLeft: 8,
            marginBottom: 15,
            flex: 1,
          }}
        >
          By clicking you agree to accept our{" "}
          <Text style={{ color: "#1D3557", fontWeight: "bold" }}>
            Terms & Conditions
          </Text>
          ,{"\n"}that you are over 18 and aware of our reporting policies!
        </Text>
      </View>

      <Button
        title="Register"
        onPress={handleRegister}
        disabled={!isChecked}
        color="#1D3557"
      />

      {/* Link for users who already have an account */}
      <View style={{ marginTop: 20, alignItems: 'center' }}>
        <Text style={{ color: '#555' }}>I already have an account? </Text>
        <TouchableOpacity onPress={() => router.push('/login')}>
          <Text style={{ color: '#1D3557', fontWeight: 'bold', textDecorationLine: 'underline' }}>
            Login here
          </Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
};

export default Register;