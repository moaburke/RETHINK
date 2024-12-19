<?php
/**
 * File: common_head.php
 * Author: Moa Burke
 * Date: 2024-11-03
 * Description: Provides utility functions for the login and sign-up pages used by both user and admin.
 * 
 * Functions:
 * - hsc($hsc): Escapes HTML special characters for secure display.
 * - includeHeadAssets(): Outputs the <head> section with essential meta tags, stylesheets, and JavaScript files 
 *   needed for styling and functionality across login and sign-up pages.
 * 
 * Usage: Include this file in login and sign-up pages for consistent layout, styling, and security.
 */

// Utility function to shorten the usage of htmlspecialchars
// Special characters in a string are converted to HTML entitites
function hsc($hsc)
{
    // Convert special characters to HTML entities (like <, >, &, etc.) and handle both single and double quotes
	return htmlspecialchars($hsc, ENT_QUOTES);
}



/**
 * Function: shared_head
 * Description: Outputs the HTML head section for the application.
 * This includes essential meta tags, links to stylesheets, and scripts 
 * required for the proper rendering and functionality of the login 
 * and sign-up pages.
 */
function includeHeadAssets(){
    echo '
        <!-- Set the character encoding for the document -->
        <meta charset="utf-8">

        <!-- Set the title of the page -->
        <title>RE:THINK</title>

        <!-- Load font Awesome for icons -->
        <script src="https://kit.fontawesome.com/4f1988a159.js" crossorigin="anonymous"></script>

        <!-- Preconnect to Google Fonts to optimize loading time -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Load the Gudea font from Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Gudea&display=swap" rel="stylesheet">
        
        <!-- Link to the custom CSS stylesheet for page styling -->

        <link rel="stylesheet" href="../assets/css/auth.css">
        <link rel="stylesheet" href="../assets/css/shared.css">
        
        <!-- Link to custom JavaScript file to handle user input and interactions -->
        <script src="../assets/javascript/input_handler.js" defer></script>
    ';
}

?>