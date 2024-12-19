/* ---------- Sleep Time Increment and Decrement ---------- */

// Initialize variables
let sleepTime = 0;
let click = 0;


// Function to add zero padding for single-digit numbers (e.g., 3 becomes 03)
function zeroPadding(number) {
    return ("00" + number).slice(-2);
}

// Increase Sleep Time by 30 minutes
document.getElementById("increase-sleep-time").addEventListener("click", () => {
    // Limit the increase to 48 times (for 24 hours)
    if (click < 48) {
        // Change selector color to indicate active state
        document.querySelector(".sleep-time-selector").style.color = "#40929099";

        sleepTime = sleepTime + 30; // Increase sleep time by 30 minutes
        click = click + 1; // Increment click count
        document.getElementById("sleep-time").setAttribute('value', sleepTime); // Update the displayed value

        // Update displayed minutes and seconds with zero padding
        document.querySelector(".minute").innerText = zeroPadding(parseInt(sleepTime / 60));
        document.querySelector(".second").innerText = zeroPadding(parseInt(sleepTime % 60));
    }
})

// Decrease Sleep Time by 30 minutes
document.getElementById("decrease-sleep-time").addEventListener("click", () => {
    // Prevent decreasing below 0
    if (click > 0) {
        // Change selector color
        document.querySelector(".sleep-time-selector").style.color = "#40929099";
        
        sleepTime = sleepTime - 30; // Decrease sleep time by 30 minutes
        click = click - 1; // Decrement click count
        document.getElementById("sleep-time").setAttribute('value', sleepTime); // Update the displayed value   
    
        // Update displayed minutes and seconds with zero padding
        document.querySelector(".minute").innerText = zeroPadding(parseInt(sleepTime / 60));
        document.querySelector(".second").innerText = zeroPadding(parseInt(sleepTime % 60));
    }

    // If no time is selected, reset selector color to inactive state
    if (click == 0) {
        document.querySelector(".sleep-time-selector").style.color = "#cdcdcd";    
    }       
})


/* ---------- Reset Sleep Time ---------- */

// Reset Sleep Time to 0
document.querySelector(".reset-sleep-time").addEventListener("click", () =>{ 
    click = 0; // Reset click count
    sleepTime = 0; // Reset sleep time to 0
    document.querySelector(".sleep-time-selector").style.color = "#cdcdcd";  // Reset selector color
    
    // Reset displayed minutes and seconds to 00:00
    document.querySelector(".minute").innerText = zeroPadding(parseInt(sleepTime / 60));
    document.querySelector(".second").innerText = zeroPadding(parseInt(sleepTime % 60))
})


/* ---------- Mood Selection Logic ---------- */

const moods = [
    { id: "mood1", emojiId: "mood-emoji1", emoji: '<i class="fa-solid fa-face-laugh-beam"></i>' },
    { id: "mood2", emojiId: "mood-emoji2", emoji: '<i class="fa-solid fa-face-smile-beam"></i>' },
    { id: "mood3", emojiId: "mood-emoji3", emoji: '<i class="fa-solid fa-face-meh"></i>' },
    { id: "mood4", emojiId: "mood-emoji4", emoji: '<i class="fa-solid fa-face-frown"></i>' },
    { id: "mood5", emojiId: "mood-emoji5", emoji: '<i class="fa-solid fa-face-tired"></i>' },
];

function resetEmojis() {
    moods.forEach(mood => {
        // Reset all emojis to their regular versions
        const regularEmoji = mood.emoji.replace("fa-solid", "fa-regular");
        document.getElementById(mood.emojiId).innerHTML = regularEmoji;
    });
}
function handleMoodSelection(selectedMood) {
    resetEmojis(); // Reset all emojis
    document.getElementById(selectedMood.emojiId).innerHTML = selectedMood.emoji; // Highlight selected emoji

    // Add necessary classes for selection prompt
    document.getElementById("mood-is-selected").classList.add("mood-is-selected");
    document.getElementById("mood-selection-prompt").classList.add("mood-selection-prompt");
}

// Attach event listeners to each mood button
moods.forEach(mood => {
    document.getElementById(mood.id).addEventListener("click", () => handleMoodSelection(mood));
});


/* ---------- Slide Navigation with Progress Indicators ---------- */

// Initialize the starting slide index
let slideIndex = 1;
showSlides(slideIndex); // Display the first slide

// Function to navigate to the next slid
function nextSlide() {
    if (slideIndex != 10) { // Prevent navigation beyond the last slide
        showSlides(slideIndex += 1);  // Increment the slide index and display the next slide
    }
}

// Function to navigate to the previous slide
function previousSlide() {
    if (slideIndex != 1) { // Prevent navigation before the first slide
        showSlides(slideIndex -= 1); // Decrement the slide index and display the previous slide
    }
}

// Function to navigate to a specific slide based on its index
function currentSlide(n) {
    showSlides(slideIndex = n); // Update slide index to the specified slide and display it
}

// Function to handle slide transitions and display the current slide
function showSlides(n) {
    let slides = document.getElementsByClassName("slide"); // Get all slide elements
    
    // Check if the index exceeds the total number of slides and reset to the first slide
    if (n > slides.length) {
      slideIndex = 1
    }

    // Check if the index is less than 1 and reset to the last slide
    if (n < 1) {
        slideIndex = slides.length
    }
  
    // Hide all slides initially by setting their display to "none"
    for (let slide of slides) {
        slide.style.display = "none";
    }   

    // Display the current slide using a flex layout
    slides[slideIndex - 1].style.display = "flex"; 
}

// Get references to navigation buttons and progress indicators
const previousBtn = document.getElementById('previous-slide-button'); // Button to go to the previous slide
const nextBtn = document.getElementById('next-slide-button'); // Button to go to the next slide
const submitButton = document.getElementById('subBut'); // Button to submit the final step
const bullets = [...document.querySelectorAll('.progress-step-bullet')]; // Progress step bullets

// Get references to individual steps by their IDs
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const step3 = document.getElementById('step3');
const step4 = document.getElementById('step4');
const step5 = document.getElementById('step5');
const step6 = document.getElementById('step6');
const step7 = document.getElementById('step7');
const step8 = document.getElementById('step8');
const step9 = document.getElementById('step9');
const step10 = document.getElementById('step10');

// Define the maximum number of steps
const MAX_STEPS = 10; // Total number of steps/slides in the navigation

// Function to update the current slide and navigation state
function updateSteps(stepIndex) {
    slideIndex = stepIndex; // Set the global slide index to the specified step
    showSlides(slideIndex); // Display the slide corresponding to the step

    //Enable/Disable navigation buttons
    previousBtn.disabled = stepIndex === 1; // Disable the previous button if on the first step
    nextBtn.disabled = stepIndex === 10; // Disable the next button if on the last step

    // Update the classes for the progress step bullets
    bullets.forEach((bullet, index) => {
        bullet.classList.toggle('completed-step', index < stepIndex - 1); // Mark steps before the current one as completed
        bullet.classList.toggle('current-bullet', index === stepIndex - 1); // Highlight the current step bullet
    });

    // If the slide index reaches 10, enable the submit button
    if (slideIndex == 10) {
        submitButton.disabled = false;
    }
}

// Attach event listeners to each step for direct navigation
[step1, step2, step3, step4, step5, step6, step7, step8, step9, step10].forEach((step, index) => {
    step.addEventListener('click', () => updateSteps(index + 1)); // Update to the clicked step's index
});

// Helper function to update the bullet indicators
function updateBullets(currentIndex, prevIndex, nextIndex) {
    // Highlight the current bullet
    bullets[currentIndex]?.classList.add('current-bullet');
    bullets[currentIndex]?.classList.remove('completed-step');

    // Remove highlight from the previous and next bullets
    bullets[prevIndex]?.classList.remove('current-bullet');
    bullets[nextIndex]?.classList.remove('current-bullet');

    // Mark the previous bullet as completed
    bullets[prevIndex]?.classList.add('completed-step');

    // If the current index reaches 9, enable the submit button
    if (currentIndex == 9) {
        submitButton.disabled = false;
    }
}

// Event listener for the Next button
nextBtn.addEventListener('click', () => {
    previousBtn.disabled = false; // Enable the previous button when navigating forward

    if (slideIndex === MAX_STEPS) { // If the last step is reached
        nextBtn.disabled = true; // Disable the next button
        submitButton.disabled = false; // Enable the submit button
    }

    updateBullets(slideIndex - 1, slideIndex - 2, slideIndex); // Update bullet indicators
});

// Event listener for the Previous button
previousBtn.addEventListener('click', () => {
    if (slideIndex === 1) { // If the first step is reached
        previousBtn.disabled = true; // Disable the previous button
    }

    nextBtn.disabled = false; // Enable the next button
    submitButton.disabled = true; // Disable the submit button

    updateBullets(slideIndex - 1, slideIndex - 2, slideIndex); // Update bullet indicators
});