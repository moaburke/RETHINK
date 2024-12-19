// Select all input elements with the class "input"
const inputs = document.querySelectorAll(".input");

/* ---------- Focus and Blur Handlers ---------- */
/**
 * Adds the 'focus' class to the parent element when the input field is focused.
 */
function focusFunc() {
   // Get the parent element of the input
   let parent = this.parentNode;
   // Add 'focus' class to the parent
   parent.classList.add("focus");
}

/**
 * Removes the 'focus' class from the parent element if the input field is empty on blur.
 */
function blurFunc() {
   // Get the parent element of the input
   let parent = this.parentNode;

   // Check if the input is empty
   if (this.value=="") {
      // Remove 'focus' class if input is empty
      parent.classList.remove("focus");
   }
}

// Attach focus and blur event listeners to each input element
inputs.forEach((input) => {
   // Add focus event listener
   input.addEventListener("focus", focusFunc);
   // Add blur event listener
   input.addEventListener("blur", blurFunc);

});


/* ---------- Password Visibility Toggle ---------- */
/**
 * Toggles the visibility of the password input field.
 * Changes the input type between 'password' and 'text' 
 * and updates the icon accordingly to indicate the current state.
 */
function togglePasswordVisibility(passwordInput, passwordToggleIcon) {
   // Get the input field element using the provided input ID
   var inputField = document.getElementById(passwordInput);
   // Get the icon element using the provided icon ID
   var icon = document.getElementById(passwordToggleIcon);

   // Check if the current input type is 'password'
   if (inputField.type === "password") {
       inputField.type = "text"; // Change input type to text
       icon.classList.remove('fa-eye'); // Remove eye icon
       icon.classList.add('fa-eye-slash'); // Add eye-slash icon
   } else {
       inputField.type = "password"; // Change input type to password
       icon.classList.remove('fa-eye-slash'); // Remove eye-slash icon
       icon.classList.add('fa-eye'); // Add eye icon
   }
}