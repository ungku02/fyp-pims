import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import axios from "axios";

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyAy69WRqnldnIxZRklq-t-HgOgLLL3OpMA",
    authDomain: "pushnotificationlaravel-40dcd.firebaseapp.com",
    projectId: "pushnotificationlaravel-40dcd",
    storageBucket: "pushnotificationlaravel-40dcd.appspot.com",
    messagingSenderId: "589625807517",
    appId: "1:589625807517:web:32139b3a5ed13ab03efa74",
    measurementId: "G-34D4L1TD7Y",
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

const vapidKey =
    "BAMjRMM0MyYnwvyU5Sa700hoK3KfzoNNRJazXLkfRemBZo1rInIQzvRN3E_4BSrUo2l6if9_SBdRQVDkfuTJ20Y";

// Function to request FCM token
const requestFCMToken = async () => {
    try {
        const token = await getToken(messaging, { vapidKey });
        if (token) {
            console.log("FCM Token:", token);

            // Get CSRF token from meta tag (Laravel automatically adds this in Blade)
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Send token using fetch()
            const response = await fetch("save-fcm-token", {
                // Jika guna API route
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ fcm_token: token }), // Pastikan key sama dengan backend
            });

            const data = await response.json();
            console.log("Server Response:", data);
        } else {
            console.warn("No registration token available.");
        }
    } catch (error) {
        console.error("Error retrieving token:", error);
    }
};

// Call the function
requestFCMToken();

// Handle incoming messages when the app is in the foreground
onMessage(messaging, (payload) => {
    console.log("Message received:", payload);
    onMessage(messaging, (payload) => {
    console.log("Notifikasi diterima:", payload);
    alert(`📢 ${payload.notification.title}\n${payload.notification.body}`);
});
    // Add logic to display notification if needed
});
