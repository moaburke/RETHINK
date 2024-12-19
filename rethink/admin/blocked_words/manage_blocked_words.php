<?php
/**
 * File: mannage_block_words.php
 * Author: Moa Burke
 * Date: 2024-11-06
 * Description: This PHP script manages blocked words for the admin panel. It allows the admin to:
 *      - View all currently blocked words.
 *      - Add new blocked words to the database.
 *      - Delete existing blocked words using the provided interface.
 * 
 * Includes:
 * - Session management to ensure the admin is logged in.
 * - Database connections and verification of user authentication.
 * - Feedback messages upon successful or failed actions.
*/
session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/admin_layout.php"); // Include the admin header layout file

// Check if the admin is logged in 
$adminData = check_login($con);
// Retrieve the UserID of the logged-in admin
$adminID = $adminData['UserID']; 

// Get today's date in Y-m-d format
$date = date("Y-m-d");

// Query the total number of blocked words from the database
$totalBlockedWordsQuery = mysqli_query($con,"SELECT * FROM blockedWords");
$totalBlockedWordsCount = mysqli_num_rows($totalBlockedWordsQuery);

// Check if a new blocked word has been submitted
if(isset($_POST['blockedWord'])){
    $blockedWordInput = $_POST['blockedWord'];  // Retrieve the submitted word

    // Check if the input word is not empty
    if ($blockedWordInput != null){
        // Search for the blocked word in the database to prevent duplicates
        $searchBlockedWordQuery = mysqli_query($con,"SELECT * FROM blockedWords WHERE blockedWord LIKE '%$blockedWordInput%'");
        $existingBlockedWordCount = mysqli_num_rows($searchBlockedWordQuery);

        // Only insert if the word doesn't already exist
        if($existingBlockedWordCount == 0){
            // Prepare the SQL statement to insert the new blocked word
            $insertBlockedWordQuery = "INSERT INTO blockedWords (blockedWord) VALUES('$blockedWordInput')";
            $insertBlockedWordResult = mysqli_query($con, $insertBlockedWordQuery);

            // Check if the insertion was successful
            if ($insertBlockedWordResult) {
                // Set a success feedback message for the admin
                $_SESSION['feedbackMessage'] = "<div class='message-text success-alert'><p>禁止用語が正常に追加されました</p></div>"; // "Blocked word added successfully."
            } else {
                // Set a failure feedback message if the insertion failed
                $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
            }

        }
    } else {
        // Set a failure message if the input was empty
        $_SESSION['feedbackMessage'] = "<div class='message-text fail-alert'><p>何か問題が発生しました。</p></div>"; // "Something went wrong."
    }
    
    // Redirect to the blocked words management page to display feedback
    header('Location: ./manage_blocked_words.php');
    exit(0);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>
    </head>

    <body>
        <header class="sidebar-navigation blocked-words-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>

        <!-- Main wrapper for the blocked words content section -->
        <div class="admin-main-wrapper">
            <h2>Manage Blocked Words</h2>

            <!-- Display feedback messages, such as success or error notifications -->
            <div>
                <?php include('../../server-side/shared/feedback_messages.php'); ?>
            </div>

            <!-- Wrapper for displaying blocked words content -->
            <div class="blocked-words-container">
                <!-- Section header for Blocked Words, displayed in both Japanese and English -->
                <h3>
                    <span class="japanese-title">禁止用語</span>
                    <span class="english-title">Blocked Words</span>
                </h3>
      
                <!-- Display the total number of blocked words -->
                <div class="total-blocked-words">
                    <p>Total:</p>
                    <p><?php echo $totalBlockedWordsCount . "件"; ?></p>
                </div><!-- .total-blocked-words -->

                <!-- Form to add a new blocked word -->
                <div class="add-blocked-word-form">
                    <form action="" method="post">
                        <div>
                            <!-- Input field for entering a new blocked word -->
                            <input type="text" name="blockedWord">
                            <!-- Submit button to add the blocked word -->
                            <button type="submit" value="word">Add</button>
                        </div>
                    </form>
                </div><!-- .add-blocked-word-form -->
                   
                <!-- Blocked words table section -->
                <div class="blocked-words-table">
                    <table>
                        <?php 
                        // Fetch all blocked words from the database
                        $blockedWordsQueryResult = mysqli_query($con,"SELECT * FROM blockedWords");

                        // Loop through each blocked word retrieved from the database
                        while($blockedWordData =  mysqli_fetch_assoc($blockedWordsQueryResult)){
                            $blockedWordContent = $blockedWordData['blockedWord']; ?>

                            <!-- Display each blocked word and a delete button in a new table row -->
                            <tr>
                                <!-- Display the blocked word in a table cell -->
                                <td><?php echo htmlspecialchars($blockedWordContent); ?></td>

                                <!-- Delete button for the blocked word -->
                                <td>
                                    <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                        <!-- Submit button to delete the blocked word, with confirmation -->
                                        <button type="submit" name="word-delete" value="<?=$blockedWordData['blockedWordID'];?>" onclick="return checkDelete()">
                                            <i class="fa-solid fa-trash"></i> <!-- Trash icon for delete -->
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php } ?>
                    </table>
                </div><!-- .blocked-words-table -->

            </div><!-- .blocked-words-container -->
                  
        </div><!-- .admin-main-wrapper -->
    </body>
    
</html>