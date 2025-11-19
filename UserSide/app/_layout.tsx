import { DarkTheme, DefaultTheme, ThemeProvider } from '@react-navigation/native';
import { useFonts } from 'expo-font';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import React, { useState, useEffect } from 'react';
import 'react-native-reanimated';
import * as SplashScreen from 'expo-splash-screen';

import { useColorScheme } from '@/hooks/useColorScheme';
import LoadingScreen from '../components/LoadingScreen';
import { UserProvider } from '../contexts/UserContext';

// Prevent auto-hiding splash screen
SplashScreen.preventAutoHideAsync();

export default function RootLayout() {
  const colorScheme = useColorScheme();
  const [fontsLoaded, fontError] = useFonts({
    SpaceMono: require('../assets/fonts/SpaceMono-Regular.ttf'),
  });
  const [isAppReady, setIsAppReady] = useState(false);

  useEffect(() => {
    async function prepare() {
      try {
        // Wait for fonts to load (no timeout)
        // The LoadingScreen animation will play while waiting
        if (fontsLoaded || fontError) {
          // Simulate minimum loading time for smooth UX
          await new Promise(resolve => setTimeout(resolve, 2000));
          setIsAppReady(true);
          await SplashScreen.hideAsync();
        }
      } catch (e) {
        console.warn('Error during app initialization:', e);
      }
    }

    prepare();
  }, [fontsLoaded, fontError]);

  // Show loading screen until app is ready
  if (!isAppReady) {
    return <LoadingScreen visible={true} />;
  }

  return (
    <UserProvider>
      <ThemeProvider value={colorScheme === 'dark' ? DarkTheme : DefaultTheme}>
        <Stack
          screenOptions={{
            // Add page transition animations
            animation: 'simple_push',
            animationDuration: 300,
          }}
        >
          <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
          <Stack.Screen 
            name="login" 
            options={{ 
              headerShown: false,
              animation: 'slide_from_right',
              animationDuration: 300,
            }} 
          />
          <Stack.Screen 
            name="register" 
            options={{ 
              headerShown: false,
              animation: 'slide_from_right',
              animationDuration: 300,
            }} 
          />
          <Stack.Screen 
            name="edit-profile" 
            options={{ 
              headerShown: false,
              animation: 'slide_from_right',
              animationDuration: 300,
            }} 
          />
          <Stack.Screen 
            name="+not-found" 
            options={{
              animation: 'fade_from_bottom',
              animationDuration: 200,
            }}
          />
        </Stack>
        <StatusBar style="auto" />
      </ThemeProvider>
    </UserProvider>
  );
}