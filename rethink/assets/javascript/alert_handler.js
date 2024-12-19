/* ---------- Alert Box Functionality ---------- */
/**
 * This script handles the functionality of an alert box on the webpage. 
 * It allows the user to close the alert by clicking the close button. 
 * When the button is clicked, the alert's display is set to 'none', 
 * effectively hiding it from the view.
 */

// Get the alert element
var alert = document.querySelector(".alert");

// Get the close button element
var closeBtn = document.querySelector(".btn-close");

// Close the alert when the button is clicked
closeBtn.onclick = function() {
    // Set the alert's display property to 'none' to hide it
    alert.style.display = "none";
}

