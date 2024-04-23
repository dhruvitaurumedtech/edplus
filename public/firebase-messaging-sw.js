// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyAZittFsZ4EQfCEMHK50Y2R8selMyu43SQ",
    authDomain: "test-4462c.firebaseapp.com",
    projectId: "test-4462c",
    storageBucket: "test-4462c.appspot.com",
    messagingSenderId: "280056884517",
    appId: "1:280056884517:web:ff410ca893c25f35cb7ecc",
    measurementId: "G-DLCRQLDVFW"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);