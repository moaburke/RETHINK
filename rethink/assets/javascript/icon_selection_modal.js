/* ----------Activity Icon Selection Modal Functionality ---------- */
/**
 * This script manages the functionality of an activity selection modal.
 * It allows users to select an activity icon by clicking on a corresponding button.
 * The selected icon is displayed in the modal and can be confirmed for use. 
 * The modal can be opened and closed using designated buttons.
 */

// Get modal element
var activityModal = document.getElementById("myModal");

// Get button that opens the modal
var iconSelectionButton = document.getElementById("view-all-icons-button");

// Get the close button for the modal
var closeModalButton = document.getElementsByClassName("cancel-button")[0];

// Get the confirm selection button
var confirmSelectionButton = document.querySelector(".select-button");

// Get the element where the selected icon will be displayed
var displayIconElement = document.getElementById("display-icon");

// Get all radio buttons with the name 'selectedIcon'
var iconRadioButtons = document.getElementsByName('selectedIcon');

// Array to hold references to all show buttons
var showButtons = [
    document.getElementById("show1"),
    document.getElementById("show2"),
    document.getElementById("show3"),
    document.getElementById("show4"),
    document.getElementById("show5"),
    document.getElementById("show6"),
    document.getElementById("show7"),
    document.getElementById("show8")
];

// Show modal when the activity icon button is clicked
iconSelectionButton.onclick = function() {
    activityModal.style.display = "block"; // Set modal display to block to make it visible
}

// Close modal when the close button is clicked
closeModalButton.onclick = function() {
    activityModal.style.display = "none"; // Set modal display to none to hide it
}

// Function to display the selected icon in the modal
function displaySelectedIcon() {
    displayIconElement.innerHTML = ""; // Clear previous content
    // Loop through radio buttons to find the selected one
    iconRadioButtons.forEach((radioButton, index) => {
        if (radioButton.checked) {
            // Add the selected icon's value to the display element
            displayIconElement.innerHTML += radioButton.value + "<br>";
        }
    });
}

// Set up click event for the confirm selection button
confirmSelectionButton.onclick = function() {
    activityModal.style.display = "none"; // Hide the modal
    displaySelectedIcon(); // Call function to update the displayed icon
};

// Add click events to each show button to display the selected icon
showButtons.forEach(button => {
    button.onclick = displaySelectedIcon; // Call function when button is clicked
});