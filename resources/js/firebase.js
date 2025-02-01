import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyAy69WRqnldnIxZRklq-t-HgOgLLL3OpMA",
  authDomain: "pushnotificationlaravel-40dcd.firebaseapp.com",
  projectId: "pushnotificationlaravel-40dcd",
  storageBucket: "pushnotificationlaravel-40dcd.appspot.com",
  messagingSenderId: "589625807517",
  appId: "1:589625807517:web:32139b3a5ed13ab03efa74",
  measurementId: "G-34D4L1TD7Y"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

const vapidKey = "BAMjRMM0MyYnwvyU5Sa700hoK3KfzoNNRJazXLkfRemBZo1rInIQzvRN3E_4BSrUo2l6if9_SBdRQVDkfuTJ20Y";

// Function to request FCM token
const requestFCMToken = async () => {
    try {
        const token = await getToken(messaging, { vapidKey });
        if (token) {
            console.log("FCM Token:", token);
            // Hantar token ke server jika perlu
        } else {
            console.warn("No registration token available. Request permission to generate one.");
        }
    } catch (error) {
        console.error("An error occurred while retrieving token:", error);
    }
};

// Call the function
requestFCMToken();

// Handle incoming messages when the app is in the foreground
onMessage(messaging, (payload) => {
    console.log("Message received:", payload);
    // Tambah logik untuk paparan notifikasi di sini jika perlu
});
