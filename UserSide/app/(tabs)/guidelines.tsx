import React, { useState } from 'react';
import { View, Text, ScrollView, Button, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import styles from "./styles"; // import your global styles
import Checkbox from 'expo-checkbox';
import UpdateSuccessDialog from '../../components/UpdateSuccessDialog';

const Guidelines = () => {
    const handlePress = () => {
    console.log("Agreed to Guidelines pressed!");
    };
    const [isChecked, setChecked] = useState(false);
    const [showSuccessDialog, setShowSuccessDialog] = useState(false);
  return (
    <ScrollView 
      style={styles.container}
      contentContainerStyle={{ paddingBottom: 50 }}
    >
    <View>
      {/* Header with Back Button and Title */}
      <View style={styles.headerHistory}>
        <TouchableOpacity onPress={() => router.push('/')}>
          <Ionicons name="chevron-back" size={24} color="#000" />
        </TouchableOpacity>
        <View style={{ flex: 1, alignItems: 'center' }}>
          <Text style={styles.textTitle}>
            <Text style={styles.alertWelcome}>Alert</Text>
            <Text style={styles.davao}>Davao</Text>
          </Text>
          <Text style={styles.subheadingCenter}>Guidelines</Text>
        </View>
        <View style={{ width: 24 }} />
      </View>
      
    {/* Welcome */}
    <Text style = {styles.normalTxtJustify}>User Guidelines for Incident Reporting
This mobile application is provided to facilitate the timely reporting of incidents and promote community safety. 
Users are expected to submit reports responsibly and in accordance with the guidelines outlined below.</Text>
    
    <Text>1. Acceptable Use of the Application The application is intended for reporting the following types of incidents:</Text>
    <Text style = {styles.normalTxtJustify}>
    Physical crimes or suspicious activities
    Emergency or urgent public safety concerns
    Community disturbances (e.g., theft, vandalism)
    Safety-related issues requiring police presence</Text>

    <Text style = {styles.normalTxtJustify}>Users are encouraged to provide clear, factual, and accurate information to assist authorities in responding appropriately.</Text>
        
    <Text>Users are encouraged to provide clear, factual, and accurate information to assist authorities in responding appropriately.</Text>
    <Text style = {styles.normalTxt}>
    Scandal-related or sensitive matters (e.g., private or explicit content, defamatory materials, cases involving minors) {'\n'}

    These reports require confidential handling and official documentation, which must be conducted in person at the nearest police station or designated cybercrime unit.
    </Text>

    <Text>3. Prohibited Submissions </Text>
    <Text style={styles.normalTxtJustify}>The following types of content must not be uploaded to the app as proof of an incident:</Text>
    <Text style={styles.normalTxt}>
    • Leaked or unauthorized recordings {'\n'}
    • Content involving minors or individuals whose identity must be protected {'\n'}
    • Uploading such materials through the app may violate data privacy and legal standards. {'\n'}
    </Text>

    <Text>4. Commitment to Data Privacy</Text>
    <Text style={styles.normalTxtJustify}>All reports and user data submitted through this platform are handled with the highest standard of confidentiality and care. The system complies with Republic Act No. 10173, also known as the Data Privacy Act of 2012, ensuring the protection of personal data against unauthorized access, disclosure, and misuse. {'\n'}
    Users can be assured that: {'\n'}
    • Personal information is used only for legitimate law enforcement purposes. {'\n'}
    • Reports are transmitted securely and encrypted. {'\n'}
    • No sensitive content is stored without user consent or outside legal protocols {'\n'}
    </Text>

    <Text>5. Legal Notice on Misuse</Text>
    <Text style={styles.normalTxtJustify}>Misuse of this application—including false reporting, uploading prohibited content, or malicious intent—may result in legal consequences, including criminal liability under existing Philippine laws. {'\n'}</Text>

     {/* Checkbox + Agree */}
      <View style={styles.checkboxRow}>
        <Checkbox value={isChecked} onValueChange={setChecked} color={isChecked ? "#1D3557" : undefined} />
        <Text style={styles.checkboxText}>I have read and agreed to the Guidelines</Text>
      </View>
      
     {/* Button (disabled until checked) */}
      <Button
        title="I Understand"
        onPress={() => {
          setShowSuccessDialog(true);
        }}
        disabled={!isChecked}
        color="#1D3557"
      />
      <Text>{'\n'}</Text>
    </View>
    
    {/* Success Dialog */}
    <UpdateSuccessDialog
      visible={showSuccessDialog}
      title="Guidelines Accepted!"
      message="You have successfully accepted the guidelines. Thank you for helping us maintain a safe community."
      okText="OK"
      onOk={() => {
        setShowSuccessDialog(false);
        router.push('/');
      }}
    />
    </ScrollView>
    
  );
};

export default Guidelines;
