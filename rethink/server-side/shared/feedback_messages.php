<?php
/**
 * Page Name: alert_messages.php
 * Author: Moa Burke
 * Date: 2024-10-30 
 * Description: This code checks if there is a feedback message stored in the session, such as a success or error message.
 *      If a message exists, it displays it within an alert div, which automatically disappears after 3 seconds.
 *      The alert includes:
 *      - The message content
 *      - A close button for manual dismissal
 * 
 *      Once displayed, the message is removed from the session to prevent it from reappearing on subsequent page loads.
*/

// Check if there is a general message stored in the session
if (isset($_SESSION['feedbackMessage'])) { ?>
    <!-- Alert div for displaying the general message -->
    <div class="alert" id="autoCloseAlert">
        <!-- Output the general message -->
        <?= $_SESSION['feedbackMessage']; ?>

        <!-- Close button icon for dismissing the alert -->
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <i class='fa-solid fa-xmark'></i>
        </button>
    </div>
    
    <?php
    // Remove the general message from the session after displaying it
    unset($_SESSION['feedbackMessage']);
}
?>

<script>
    /**
     * Auto-Close Script for Feedback Alert
     * 
     * This JavaScript code automatically closes the feedback alert after 3 seconds (3000 milliseconds).
     * - First, it waits for 3 seconds.
     * - Then, it applies a fade-out effect to make the alert disappear smoothly.
     * - Finally, after the fade-out completes, the alert is completely hidden using 'display: none'.
     */
    setTimeout(function() {
        // Find the alert element by its ID
        const alert = document.getElementById('autoCloseAlert');

        // If the alert exists, apply fade-out effect and then hide it
        if (alert) {
            alert.classList.add('fade-out');  // Add fade-out class for smooth transition
            setTimeout(() => alert.style.display = 'none', 500);  // Hide after fade-out
        }
    }, 3000);  // 3000ms = 3 seconds
</script>

<style>
    /* Optional: Add a fade-out effect for a smooth transition */
    .fade-out {
        opacity: 0;  /* Set opacity to 0 to make it transparent */
        transition: opacity 0.5s ease; /* 0.5-second transition effect for smooth fade-out */
    }
</style>