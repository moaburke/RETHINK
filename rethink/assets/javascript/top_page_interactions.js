/* ---------- Page Load Animation ---------- */
/**
 * This script handles the page load animation by showing a loading overlay when the page is accessed,
 *  and then removing it after a delay of 2.4 seconds.
*/

// Animation when accessing the website
$(window).on('load', function() {
    setTimeout(removeLoader, 2400); // Wait for 2.4 seconds before removing the loader
});

// Function to remove the loader
function removeLoader() {
    $( ".loading-overlay" ).fadeOut(500, function() {
        $( ".loading-overlay" ).remove(); // Remove the loader from the DOM
    });   
}


/* ---------- Back to Top Button ---------- */
// This script implements the "Back to Top" button functionality.

// Add a scroll event listener to the window
$(window).scroll(function() {
    // Check if the scroll position is 300 pixels or more from the top
    if ($(this).scrollTop() >= 300) {        
    // If true, fade in the "return to top" arrow
        $('#return-to-top').fadeIn(200); // Fade in duration: 200ms
    } else {
    // If false (scroll position is less than 300 pixels), fade out the arrow
        $('#return-to-top').fadeOut(200); // Fade out duration: 200ms
    }
});

// Add a click event listener to the "return to top" arrow
$('#return-to-top').click(function() { 
    // When the arrow is clicked, animate the scroll to the top of the body
    $('body,html').animate({
        scrollTop : 0 // Scroll to the top of the body
    }, 500); // Animation duration: 500ms
});


/* ---------- Smooth Scroll Functions ---------- */
// This script provides smooth scrolling functionality to different sections of the page.

// Function to smoothly scroll to the "What Is RETHINK" section
function scrollToAboutSection() {
    // Get the element with the ID "WhatIsRETHINK"
    var access = document.getElementById("about-rethink-section");
    // Scroll smoothly to the element
    access.scrollIntoView({behavior: 'smooth'}, true);
}

// Function to smoothly scroll to the "How It Works" section
function scrollToHowItWorksSection() {
    // Get the element with the ID "HowItWorks"
    var access = document.getElementById("how-it-works-section");
    // Scroll smoothly to the element
    access.scrollIntoView({behavior: 'smooth'}, true);
}


/* ---------- Reveal Animation on Scroll ---------- */
// This script adds animation effects to elements when they are scrolled into view.

// Event listener to trigger the fadeInLeft function when the user scrolls
window.addEventListener('scroll', fadeInLeft);

// Function to reveal elements with the class 'fade-in-left' from the left side when scrolled into view
function fadeInLeft() {
    // Select all elements with the class 'fade-in-left'
    var revealsLeft = document.querySelectorAll('.fade-in-left');

    // Loop through each element
    for (var i = 0; i < revealsLeft.length; i++) {
        // Get the height of the window
        var windowHeight = window.innerHeight;
        // Get the distance from the top of the viewport to the top of the element
        var revealTop = revealsLeft[i].getBoundingClientRect().top;
        // Set a point at which the element will start to be revealed
        var revealPoint = 350;

        // If the element is within the viewport (less than revealPoint)
        if (revealTop < windowHeight - revealPoint) {
            // Add the class 'fade-in-left-active' to trigger the reveal animation
            revealsLeft[i].classList.add('fade-in-left-active');
        }
    }
}

// Event listener to trigger the fadeInRight function when the user scrolls
window.addEventListener('scroll', fadeInRight);

// Function to reveal elements with the class 'fade-in-left' from the right side when scrolled into view
function fadeInRight() {
    // Select all elements with the class 'fade-in-right'
    var revealsRight = document.querySelectorAll('.fade-in-right');

    // Loop through each element
    for (var i = 0; i < revealsRight.length; i++) {
        // Get the height of the window
        var windowHeight = window.innerHeight;
        // Get the distance from the top of the viewport to the top of the element
        var revealTop = revealsRight[i].getBoundingClientRect().top;
        // Set a point at which the element will start to be revealed
        var revealPoint = 350;

        // If the element is within the viewport (less than revealPoint)
        if (revealTop < windowHeight - revealPoint) {
            // Add the class 'fade-in-right-active' to trigger the reveal animation
            revealsRight[i].classList.add('fade-in-right-active');
        }
    }
}
