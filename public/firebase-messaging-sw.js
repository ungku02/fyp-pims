importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyAy69WRqnldnIxZRklq-t-HgOgLLL3OpMA",
  authDomain: "pushnotificationlaravel-40dcd.firebaseapp.com",
  projectId: "pushnotificationlaravel-40dcd",
  storageBucket: "pushnotificationlaravel-40dcd.appspot.com",
  messagingSenderId: "589625807517",
  appId: "1:589625807517:web:32139b3a5ed13ab03efa74",
  measurementId: "G-34D4L1TD7Y"
});

const messaging = firebase.messaging();
