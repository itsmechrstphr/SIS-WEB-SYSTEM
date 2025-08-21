// 🟩 Handles toggling between Login and Sign Up forms for user authentication

// Get references to form containers and toggle buttons
const loginContainer = document.getElementById('Login');
const signUpContainer = document.getElementById('signUp');
const signUpBtn = document.getElementById('sUp');
const signInBtn = document.getElementById('sIn');

// Show Sign Up form and hide Login form when "Sign Up" is clicked
signUpBtn.addEventListener('click', function() {
    loginContainer.style.display = 'none';
    signUpContainer.style.display = 'block';
});

// Show Login form and hide Sign Up form when "Sign In" is clicked
signInBtn.addEventListener('click', function() {
    signUpContainer.style.display = 'none';
    loginContainer.style.display = 'block';
});

// By default, show Sign Up form and hide Login form
window.onload = function() {
    loginContainer.style.display = 'none';
    signUpContainer.style.display = 'block';
};

// You can add further logic here for form validation, AJAX requests, etc.