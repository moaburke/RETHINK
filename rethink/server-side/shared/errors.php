<?php
/*
 * Page Name: errors.php
 * Author: Moa Burke
 * Date: 2024-11-02
 * Description: This code checks if there are any error messages stored in the $errors array.
 *      If there are errors, it loops through the array and displays each error message
 *      within a <p> tag inside a <div>. The error messages are displayed only if there 
 *      is at least one error present.
 */
// Check if there are any errors in the $errors array
// Only display the error messages if the array has one or more items
if(count($errors) > 0 ) : ?>
    <!-- Error messages container div -->
    <div>
        <?php 
        // Loop through each error in the $errors array and display it
        foreach($errors as $error) : ?>
            <!-- Display each error message in a paragraph tag -->
            <p><?php echo $error ?></p>

        <?php endforeach // End of the errors loop ?>
        
    </div>
<?php endif // End of the if condition checking for errors ?>