<?php
/*
 * Page Name: Mood Tracker
 * Description: This page allows users to track their daily mood, feelings, and related activities. It features an interactive form for users to select their mood, 
 *      weather conditions, and set personal goals, along with a diary section for quick notes. The design includes visual feedback elements such as icons for mood and goals, 
 *      enhancing user experience. The page also tracks sleep time, providing insights into the relationship between sleep and mood. Data submitted through the form is 
 *      processed and stored in the database for later retrieval and analysis.
 * Author: Moa Burke
 * Date: 2024-10-28
 *
 * Notes:
 * - Utilizes PHP for server-side processing and MySQL for data storage.
 * - Includes dynamic elements that adapt based on user input, such as displaying the appropriate icons and text based on the selected mood and goals.
 *
 * Dependencies:
 * - Requires PHP for server-side processing and MySQL database connection.
 * - JavaScript for managing user interactions and dynamic content updates.
 * - FontAwesome for iconography.
 *
 */
session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); //Include the utiity functions
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "header/user_layout.php"); // Include the user header layout file

// Define constants for different feeling states
define("FEELING_POSITIVE", 1);
define("FEELING_NEUTRAL", 2);
define("FEELING_NEGATIVE", 3);

// Check if the user is logged in and get user data
$userData = check_login($con);
$userID = $userData['UserID'];

// Check if 'thismood' is set, and retrieve mood data from POST request
if(isset($_REQUEST['thismood'])){
    $selectedMoodID = $_POST['thismood'];
}

// Retrieve all moods from the database
$queryRetrieveMoods = mysqli_query($con,"SELECT * FROM Moods");
$totalMoods = mysqli_num_rows($queryRetrieveMoods);


// Retrieve positive feelings from the database (FeelingLoadingID = 1)
$queryRetrievePositiveFeelings = mysqli_query($con,"SELECT * FROM Feelings WHERE FeelingLoadingID = '" . FEELING_POSITIVE ."'");
$totalPositiveFeelings = mysqli_num_rows($queryRetrievePositiveFeelings);

// Retrieve neutral feelings from the database (FeelingLoadingID = 2)
$queryRetrieveNeutralFeelings = mysqli_query($con,"SELECT * FROM Feelings WHERE FeelingLoadingID = '" . FEELING_NEUTRAL ."'");
$totalNeutralFeelings = mysqli_num_rows($queryRetrieveNeutralFeelings);

// Retrieve negative feelings from the database (FeelingLoadingID = 3)
$queryRetrieveNegativeFeelings = mysqli_query($con,"SELECT * FROM Feelings WHERE FeelingLoadingID = '" . FEELING_NEGATIVE ."'");
$totalNegativeFeelings = mysqli_num_rows($queryRetrieveNegativeFeelings);
    
// Retrieve all activities from the database
$queryRetrieveActivities = mysqli_query($con,"SELECT * FROM Activities");
$totalNumberOfActivities = mysqli_num_rows($queryRetrieveActivities);

// Retrieve all company (people) data from the database
$queryRetrieveCompany = mysqli_query($con,"SELECT * FROM Company");
$totalNumberOfCompanies = mysqli_num_rows($queryRetrieveCompany);

// Retrieve all locations from the database
$queryRetrieveLocations = mysqli_query($con,"SELECT * FROM locations");
$totalLocations = mysqli_num_rows($queryRetrieveLocations);

// Retrieve all food options from the database
$queryRetrieveFoods = mysqli_query($con,"SELECT * FROM foods");
$totalFoods = mysqli_num_rows($queryRetrieveFoods);

// Retrieve weather data from the database
$queryRetrieveWeather = mysqli_query($con,"SELECT * FROM weather");
$totalWeather = mysqli_num_rows($queryRetrieveWeather);

// Retrieve user-specific goals from the database based on the current user
$queryRetrieveUserGoals = mysqli_query($con, "SELECT * FROM usergoals WHERE UserID = $userID");
$totalUserGoals = mysqli_num_rows($queryRetrieveUserGoals);

// Retrieve all goals from the database
// $queryRetrieveGoals = mysqli_query($con, "select * FROM goals");
// $rowsgoals = mysqli_num_rows($queryRetrieveGoals);

// Get today's date or use the date from the POST request if available
if(!empty($_POST['sendDateNext'])){
    $date = $_POST['sendDateNext'];
} else {
    $date = date("Y-m-d");
}
$todayDate = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Including shared head elements from a PHP function -->
        <?php includeHeadAssets(); ?>
        <!-- Preconnecting to Google Fonts for faster load -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- Including the 'Graduate' font from Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Graduate&display=swap" rel="stylesheet">
        <!-- Linking JavaScript for registering mood -->
        <script src="../../assets/javascript/register_mood_interactions.js" defer></script>
    </head>

    <body>
        <!-- Header Section -->
        <header class="sidebar-navigation mood-register-navigation">
            <?php renderUserNavigation(); // Include the common user header ?>
        </header>

        <!-- Logout and sticky header for logged-in user -->
        <?php renderUserHeaderWithLogout($userData);?>

        <div class="main-wrapper">

            <h2>Register Mood</h2>
        
            <!-- Breadcrumbs for navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Register Mood</p>
            </div><!-- .breadcrumbs -->

            <!-- Progress bar showing the 10 steps of the mood registration process -->
            <section class="multi-step-progress-wrapper">
                <div id="stepProgressBar">

                    <!-- Step 1: Mood -->
                    <div class="progress-step" id="step1">
                        <p class="progress-step-text current-step">Mood</p>
                        <div class="progress-step-bullet current-bullet"><p>1</p></div>
                    </div><!-- .step -->
                    
                    <!-- Step 2: Feeling -->
                    <div class="progress-step" id="step2">
                        <p class="progress-step-text">Feeling</p>
                        <div class="progress-step-bullet"><p>2</p></div>
                    </div><!-- .step -->

                    <!-- Step 3: Activity -->
                    <div class="progress-step" id="step3">
                        <p class="progress-step-text">Activity</p>
                        <div class="progress-step-bullet"><p>3</p></div>
                    </div><!-- .step -->

                    <!-- Step 4: People (who you're with) -->
                    <div class="progress-step" id="step4">
                        <p class="progress-step-text">People</p>
                        <div class="progress-step-bullet"><p>4</p></div>
                    </div><!-- .step -->

                    <!-- Step 5: Location (where you are) -->
                    <div class="progress-step"  id="step5">
                        <p class="progress-step-text">Location</p>
                        <div class="progress-step-bullet"><p>5</p></div>
                    </div><!-- .step -->

                    <!-- Step 6: Food (what you're eating) -->
                    <div class="progress-step" id="step6">
                        <p class="progress-step-text">Food</p>
                        <div class="progress-step-bullet"><p>6</p></div>
                    </div><!-- .step -->

                    <!-- Step 7: Weather -->
                    <div class="progress-step" id="step7">
                        <p class="progress-step-text">Weather</p>
                        <div class="progress-step-bullet"><p>7</p></div>
                    </div><!-- .step -->

                    <!-- Step 8: Goal (what you're aiming to achieve) -->
                    <div class="progress-step" id="step8">
                        <p class="progress-step-text">Goal</p>
                        <div class="progress-step-bullet"><p>8</p></div>
                    </div><!-- .step -->

                    <!-- Step 9: Sleep (hours of sleep) -->
                    <div class="progress-step" id="step9">
                        <p class="progress-step-text">Sleep</p>
                        <div class="progress-step-bullet"><p>9</p></div>
                    </div><!-- .step -->

                    <!-- Step 10: Notes (additional comments or observations) -->
                    <div class="progress-step" id="step10">
                        <p class="progress-step-text">Note</p>
                        <div class="progress-step-bullet"><p>10</p></div>
                    </div><!-- .step -->
                </div><!-- #stepProgressBar -->
            </section><!-- .multi-step-progress-wrapper -->

            <!-- Slider to navigate through mood registration steps -->
            <section class="mood-registration-wrapper" id="slider">
                <div class="mood-registration-slider">

                    <!-- Buttons to navigate between slides -->
                    <button id="previous-slide-button" class="previous-slide-button" onclick="previousSlide()" disabled><i class="fa-solid fa-angles-left" ></i></button>
                    <button id="next-slide-button" class="next-slide-button" onclick="nextSlide()"><i class="fa-solid fa-angles-right"></i></button>
        
                    <!-- Form to submit mood registration -->
                    <form action="../../server-side/user/update_daily_tracking.php" method="post" name="myForm">

                        <!-- Slide 1: Select date and mood -->
                        <div class="slide">
                            <section class="mood-selector-step">
                                <!-- Heading for mood registration section with titles in Japanese and English -->
                                <h3>
                                    <span class="japanese-title">気分はどうですか？</span> <!-- Japanese: "How are you feeling?" -->
                                    <span class="english-title">How are you today?</span> <!-- English translation -->
                                </h3>

                                <!-- Date selection input -->
                                <div class="date-selector">
                                    <input type="date" name="date-selector" class="date-input" max="<?php echo $todayDate;?>" value="<?php echo $date; ?>"> <!-- Pre-fills the input with the selected date if available -->
                                </div><!-- .date-selector -->
                    
                                <!-- Mood selection container -->
                                <div class="mood-selector">
                                    <?php 
                                    // Loop through the total number of moods
                                    for ($moodID = 1; $moodID <= $totalMoods; $moodID++) {
                                        // Query to get mood data based on MoodID
                                        $moodQuery = "SELECT * FROM moods WHERE MoodID = $moodID";
                                        $moodResult = mysqli_query($con,$moodQuery);
                                        $moodData = mysqli_fetch_assoc($moodResult); ?>

                                        <!-- Individual mood option -->
                                        <div class="mood-option" id="mood<?php echo $moodData['MoodID'];?>">
                                            <label for="selected-mood<?php echo $moodData['MoodName'];?>">
                                                <!-- Display mood emoji based on selection state -->
                                                <p class="mood-emoji" id="mood-emoji<?php echo $moodData['MoodID'];?>">
                                                    <?php 
                                                    if (!empty($selectedMoodID)) { 
                                                        if ($selectedMoodID == $moodData['MoodID']) { 
                                                            echo $moodData['moodEmojiColor']; // Highlighted emoji for selected mood
                                                        } else { 
                                                            echo $moodData['moodEmoji']; // Default emoji for unselected mood
                                                        }
                                                    } else { 
                                                            echo $moodData['moodEmoji']; // Default emoji if no mood selected
                                                    }
                                                    ?>
                                                </p>
                                                
                                                <!-- Display mood name in Japanese -->
                                                <p class="mood-label" id="mood-text<?php echo $moodData['MoodID'];?>">
                                                    <span class="japanese-mood-name"><?php echo htmlspecialchars($moodData['JapaneseMoodName']);?></span>
                                                </p>
                                            </label>

                                            <!-- Radio button for mood selection -->
                                            <input type="radio" name="selected-mood" id="selected-mood<?php echo $moodData['MoodName'];?>" value="<?php echo $moodData['MoodID'];?>" <?php if (!empty($selectedMoodID)) { if($selectedMoodID == $moodData['MoodID']){ echo "checked";}}?> required>
                                        </div> <!-- .mood-option -->
                                    <?php } ?>
                                </div><!-- .mood-selector -->
                            </section><!-- .mood-selector-step -->
                        </div><!-- .slide -->

                        <!-- Slide 2: Feelings selection -->
                        <div class="slide">
                            <section class="feeling-selector-step">
                                <!-- Heading for the feelings registration section, displaying titles in both Japanese and English -->
                                <h3>
                                    <span class="japanese-title">感情</span> <!-- Japanese title for "Feeling" -->
                                    <span class="english-title">Feeling</span> <!-- English translation -->
                                </h3>

                                <div class="feeling-selector 
                                    <?php 
                                    // Check the total number of feelings being displayed (positive, neutral, and negative)
                                    // and apply a corresponding wrapper class based on predefined thresholds.
                                    // This helps in dynamically adjusting the styling of the feelings section based on the number of feelings.
                                    if ($totalPositiveFeelings > 8 || $totalNeutralFeelings > 8 || $totalNegativeFeelings > 8) { 
                                        echo "feelings-wrapper-large"; // Applies styling wrapper for more than 8 total feelings
                                    }?> 
                                    <?php 
                                    if ($totalPositiveFeelings > 10 || $totalNeutralFeelings > 10 || $totalNegativeFeelings > 10) {
                                        echo "feelings-wrapper-extra-large"; // Applies styling wrapper for more than 10 total feelings
                                    }?>
                                    <?php 
                                    if ($totalPositiveFeelings > 12 || $totalNeutralFeelings > 12 || $totalNegativeFeelings > 12) {
                                        echo "feelings-wrapper-max"; // Applies styling wrapper for more than 12 total feelings
                                    }?>">

                                    <!-- Section for displaying positive feelings -->
                                    <div class="positive-feeling 
                                        <?php 
                                        // Similar to the wrapper above, check the number of positive, neutral, and negative feelings
                                        // to determine the styling of the positive feelings section.
                                        // This is to ensure that the layout adapts based on the number of positive feelings present.
                                        if ($totalPositiveFeelings > 8 || $totalNeutralFeelings > 8 || $totalNegativeFeelings > 8) { 
                                            echo "large"; // Applies styling for more than 8 positive feelings
                                        }?> 
                                        <?php 
                                        if ($totalPositiveFeelings > 10 || $totalNeutralFeelings > 10 || $totalNegativeFeelings > 10) {  
                                            echo "extra-large"; // Applies styling for more than 10 positive feelings
                                        }?> 
                                        <?php 
                                        if ($totalPositiveFeelings > 12 || $totalNeutralFeelings > 12 || $totalNegativeFeelings > 12) {
                                        echo "max"; // Applies styling for more than 12 positive feelings
                                        }?>">


                                        <?php
                                        // Loop through each positive feeling retrieved from the database
                                        while ($positiveFeelingData = mysqli_fetch_assoc($queryRetrievePositiveFeelings)) {
                                            // Extract feeling details from the database row
                                            $positiveFeelingID = $positiveFeelingData['FeelingID']; // Unique ID for the feeling
                                            $positiveFeelingName = $positiveFeelingData['FeelingName']; // Name of the feeling
                                            ?>
                                            
                                            <!-- Label for the checkbox input --> 
                                            <label for="feeling<?php echo $positiveFeelingID;?>" >
                                                <div class="feeling-item positive" id="feelingID<?php echo $positiveFeelingID;?>">
                                                    <!-- Display the feeling name -->
                                                    <p class="feeling-text" id="feelingPo<?php echo $positiveFeelingID;?>"> 
                                                        <?php echo htmlspecialchars($positiveFeelingName);?>
                                                    </p>

                                                    <!-- Hidden checkbox input for the feeling -->
                                                    <input type="checkbox" name="feeling[]" id ="feeling<?php echo $positiveFeelingID;?>" value="<?php echo $positiveFeelingID;?>" style="display:none">
                                                </div><!-- .feeling-item positive -->
                                            </label>
                                        <?php } ?>
                                    </div><!-- .positive-feeling -->

                                    <!-- Neutral feelings section -->
                                    <div class="neutral-feeling 
                                        <?php 
                                        // Check the total number of feelings being displayed (positive, neutral, and negative)
                                        // and apply corresponding styling classes based on the thresholds of 8, 10, and 12.
                                        // These classes adjust the layout and visual representation when the count of neutral feelings exceeds certain limits.
                                        if ($totalPositiveFeelings > 8 || $totalNeutralFeelings > 8 || $totalNegativeFeelings > 8) { 
                                            echo "large"; // Applies styling for more than 8 total feelings
                                        } ?> 
                                        <?php 
                                        if ($totalPositiveFeelings > 10 || $totalNeutralFeelings > 10 || $totalNegativeFeelings > 10) {
                                            echo "extra-large"; // Applies styling for more than 10 total feelings
                                        }?> 
                                        <?php 
                                        if ($totalPositiveFeelings > 12 || $totalNeutralFeelings > 12 || $totalNegativeFeelings > 12) {
                                            echo "max"; // Applies styling for more than 12 total feelings
                                        }?>">

                                        <?php
                                        // Loop through each neutral feeling retrieved from the database
                                        while($neutralFeelingData = mysqli_fetch_assoc($queryRetrieveNeutralFeelings)){
                                            // Extract feeling details from the database row
                                            $neutralFeelingID = $neutralFeelingData['FeelingID']; // Unique ID for the neutral feeling
                                            $neutralFeelingName = $neutralFeelingData['FeelingName']; // Name of the neutral feeling
                                            ?>

                                            <!-- Label for the checkbox input --> 
                                            <label for="feeling<?php echo $neutralFeelingData['FeelingID'];?>" >
                                                <div class="feeling-item neutral" id="feelingID<?php echo $neutralFeelingID;?>">
                                                    <!-- Display the neutral feeling name -->
                                                    <p class="feeling-text" id="feelingPo<?php echo $neutralFeelingID;?>">
                                                        <?php echo htmlspecialchars($neutralFeelingName);?>
                                                    </p>

                                                    <!-- Hidden checkbox input for the neutral feeling -->
                                                    <input type="checkbox" name="feeling[]" id ="feeling<?php echo $neutralFeelingData['FeelingID'];?>" value="<?php echo $neutralFeelingID;?>" style="display:none">
                                                </div><!-- .feeling-item neutral -->
                                            </label>
                                        <?php } ?>
                                    </div><!-- .neutral-neutral -->

                                    <!-- Negative feelings section -->
                                    <div class="negative-feeling 
                                        // Check the total number of feelings being displayed (positive, neutral, and negative)
                                        // and apply corresponding styling classes based on the thresholds of 8, 10, and 12.
                                        // These classes adjust the layout and visual representation when the count of neutral feelings exceeds certain limits.
                                        <?php 
                                        if ($totalPositiveFeelings > 8 || $totalNeutralFeelings > 8 || $totalNegativeFeelings > 8) { 
                                            echo "large"; // Applies styling for more than 8 total feelings
                                        } ?> 
                                        <?php 
                                        if ($totalPositiveFeelings > 10 || $totalNeutralFeelings > 10 || $totalNegativeFeelings > 10) {
                                            echo "extra-large"; // Applies styling for more than 10 total feelings
                                        }?> 
                                        <?php 
                                        if ($totalPositiveFeelings > 12 || $totalNeutralFeelings > 12 || $totalNegativeFeelings > 12) {
                                            echo "max"; // Applies styling for more than 12 total feelings
                                            }?>">
                                        
                                        <?php
                                        // Loop through each negative feeling retrieved from the databas
                                        while ($negativeFeelingData = mysqli_fetch_assoc($queryRetrieveNegativeFeelings)) {
                                            // Extract feeling details from the database row
                                            $negativeFeelingID = $negativeFeelingData['FeelingID']; // Unique ID for the negative feeling
                                            $negativeFeelingName = $negativeFeelingData['FeelingName']; // Name of the negative feeling
                                            ?>

                                            <!-- Label for the checkbox input --> 
                                            <label for="feeling<?php echo $negativeFeelingID;?>" >
                                                <div class="feeling-item negative" id="feelingID<?php echo $negativeFeelingID;?>">
                                                    <!-- Display the negative feeling name -->
                                                    <p class="feeling-text" id="feelingPo<?php echo $negativeFeelingID;?>">
                                                        <?php echo htmlspecialchars($negativeFeelingName);?>
                                                    </p>

                                                    <!-- Hidden checkbox input for the negative feeling -->
                                                    <input type="checkbox" name="feeling[]" id ="feeling<?php echo $negativeFeelingID;?>" value="<?php echo $negativeFeelingID;?>" style="display:none">
                                                </div><!-- .feeling-item negative -->
                                            </label>
                                        <?php } ?>
                                    </div><!-- .negative-feeling -->

                                </div><!-- .feeling-selector -->
                            </section><!-- .feeling-selector-step -->
                        </div><!-- .slide -->

                        <!-- Slide 3: Activities selection -->
                        <div class="slide">
                            <section class="activity-selector-step">

                                <!-- Header for the activity selection section -->
                                <h3>
                                    <span class="japanese-title">アクティビティ</span> <!-- Japanese title for "Activity" -->
                                    <span class="english-title">Activity</span> <!-- English title -->
                                </h3>

                                <!-- Container for the activities with dynamic class assignment based on total activities -->
                                <div class="activity-selector <?php 
                                    // Apply different styling classes based on the total number of activities
                                    if ($totalNumberOfActivities > 18 && $totalNumberOfActivities <= 21) { 
                                        echo "large"; // Applies styling for more than 18 activities
                                    } elseif ($totalNumberOfActivities > 21 && $totalNumberOfActivities <= 27) { 
                                        echo "extra-large"; // Applies styling for more than 21 activities
                                    } elseif ($totalNumberOfActivities > 27) { 
                                        echo "max"; // Applies styling for more than 27 activities
                                    } ?>">
                                    
                                    <?php 
                                    // Loop through each activity retrieved from the database
                                    while ($activityData = mysqli_fetch_assoc($queryRetrieveActivities)) {
                                        // Extract activity details from the database row
                                        $currentActivityID = $activityData['ActivityID']; // Unique ID for the activity
                                        $currentActivityName = $activityData['ActivityName']; // Name of the activity
                                        $currentActivityIcon = $activityData['ActivityIcon']; // Icon associated with the activity
                                        ?>

                                        <!-- Label for the checkbox input representing an activity -->
                                        <label for="activity<?php echo $currentActivityID;?>" >
                                            <div class="entity-item activity-item" id="getActivity<?php echo $currentActivityID;?>">
                                                <!-- Display the activity icon and name -->
                                                <p class="entity-icon" id="activity-icon<?php echo $currentActivityID;?>">
                                                    <span><?php echo $currentActivityIcon; ?></span> <!-- Display the activity icon -->
                                                    <span class="entity-text"><?php echo htmlspecialchars($currentActivityName);?></span> <!-- Display the activity name -->
                                                </p>
                                                
                                                 <!-- Hidden checkbox input for the activity -->
                                                <input type="checkbox" name="activities[]" id="activity<?php echo $currentActivityID;?>" value="<?php echo $currentActivityID;?>" style="display:none">
                                            </div>
                                        </label>
                                    <?php } ?>
                                </div><!-- .activity-selector -->

                            </section><!-- .activity-selector-step third -->
                        </div><!-- .slide -->

                        <!-- Slide 4: People selection -->
                        <div class="slide">
                            <section class="company-selector-step">

                                <!-- Header for the company selection section -->
                                <h3>
                                    <span class="japanese-title">人々</span> <!-- Japanese title for "People" -->
                                    <span class="english-title">People</span> <!-- English title -->
                                </h3>

                                <!-- Container for companies with dynamic class assignment based on total companies -->
                                <div class="company-selector 
                                    <?php 
                                    // Apply different styling classes based on the total number of companies
                                    if ($totalNumberOfCompanies > 8 && $totalNumberOfCompanies <= 10) {
                                        echo "large"; // Applies styling for more than 8 companies
                                    } elseif ($totalNumberOfCompanies > 10 && $totalNumberOfCompanies <= 12) {
                                        echo "extra-large"; // Applies styling for more than 10 companies
                                    } elseif ($totalNumberOfCompanies > 12 && $totalNumberOfCompanies <= 18) {
                                        echo "max"; // Applies styling for more than 12 companies
                                    }?>">

                                    <?php 
                                    // Loop through each company retrieved from the database
                                    while ($companyData = mysqli_fetch_assoc($queryRetrieveCompany)) {
                                        // Extract company details from the database row
                                        $currentCompanyID = $companyData['CompanyID']; // Unique ID for the company
                                        $currentCompanyName = $companyData['CompanyName']; // Name of the company
                                        $currentCompanyIcon = $companyData['CompanyIcon']; // Icon associated with the company
                                        ?>

                                        <!-- Label for the checkbox input representing a company -->
                                        <label for="company<?php echo $currentCompanyID;?>">
                                            <div class="entity-item company-item" id="getCompany<?php echo $currentCompanyID;?>">
                                                <!-- Display the company icon and name -->
                                                <p class="entity-icon" id="company-icon<?php echo $currentCompanyID;?>">
                                                    <span><?php echo $currentCompanyIcon; ?></span> <!-- Display the company icon -->
                                                    <span class="entity-text"><?php echo htmlspecialchars($currentCompanyName);?></span> <!-- Display the company name -->
                                                </p>

                                                <!-- Hidden checkbox input for the company -->
                                                <input type="checkbox" name="company[]" id="company<?php echo $currentCompanyID;?>" value="<?php echo $currentCompanyID;?>" style="display:none">
                                            </div>
                                        </label>
                                    <?php } ?>
                                </div><!-- .company-selector -->

                            </section><!-- .company-selector-step -->
                        </div><!-- .slide -->

                        <!-- Slide 5: Location selection -->
                        <div class="slide">
                            <section class="location-selector-step">

                                <!-- Header for the location selection section -->
                                <h3>
                                    <span class="japanese-title">場所</span> <!-- Japanese title for "Location" -->
                                    <span class="english-title">Location</span> <!-- English title -->
                                </h3>

                                <!-- Container for locations with dynamic class assignment based on total locations -->
                                <div class="location-selector 
                                    <?php 
                                    // Apply different styling classes based on the total number of locations
                                    if ($totalLocations > 8 && $totalLocations <= 10) {
                                        echo "large"; // Applies styling for more than 8 locations
                                    } elseif ($totalLocations > 10 && $totalLocations <= 12) {
                                        echo "extra-large"; // Applies styling for more than 10 locations
                                    } elseif ($totalLocations > 12 && $totalLocations <= 18) {
                                        echo "max"; // Applies styling for more than 12 locations
                                    }?>">

                                    <?php 
                                    // Loop through each location retrieved from the database
                                    while ($currentLocationData = mysqli_fetch_assoc($queryRetrieveLocations)) {
                                        // Extract location details from the database row
                                        $currentLocationID = $currentLocationData['LocationID']; // Unique ID for the location
                                        $currentLocationName = $currentLocationData['LocationName']; // Name of the location
                                        $currentLocationIcon = $currentLocationData['LocationIcon']; // Icon associated with the location
                                        ?>

                                        <!-- Label for the checkbox input representing a location -->
                                        <label for="location<?php echo $currentLocationID;?>">
                                            <div class="entity-item location-item" id="getLocation<?php echo $currentLocationID;?>">
                                                <!-- Display the location icon and name -->
                                                <p class="entity-icon" id="location-icon<?php echo $currentLocationID;?>">
                                                    <span><?php echo $currentLocationIcon; ?></span> <!-- Display the location icon -->
                                                    <span class="entity-text"><?php echo htmlspecialchars($currentLocationName);?></span> <!-- Display the location name -->
                                                </p>

                                                <!-- Hidden checkbox input for the location -->
                                                <input type="checkbox" name="location[]" id="location<?php echo $currentLocationID;?>" value="<?php echo $currentLocationID;?>" style="display:none">
                                            </div><!-- .location -->
                                        </label>
                                    <?php } ?>
                                </div><!-- .location-selector -->

                            </section><!-- .location-selector-step -->
                        </div><!-- .slide -->

                        <!-- Slide 6: Food selection -->
                        <div class="slide">
                            <section class="food-selector-step">

                                <!-- Header for the food selection section -->
                                <h3>
                                    <span class="japanese-title">食事</span> <!-- Japanese title for "Food" -->
                                    <span class="english-title">Food</span> <!-- English title -->
                                </h3>
                                
                                <!-- Container for foods with dynamic class assignment based on total foods -->
                                <div class="food-selector 
                                    <?php 
                                    // Apply different styling classes based on the total number of foods
                                    if ($totalFoods > 8 && $totalFoods <= 10) {
                                        echo "large"; // Applies styling for more than 8 foods
                                    } elseif ($totalFoods > 10 && $totalFoods <= 12) {
                                        echo "extra-large"; // Applies styling for more than 10 foods
                                    } elseif ($totalFoods > 12 && $totalFoods <= 18) {
                                        echo "max"; // Applies styling for more than 12 foods
                                    }?>">

                                    <?php 
                                    // Loop through each food item retrieved from the database
                                    while ($currentFoodData = mysqli_fetch_assoc($queryRetrieveFoods)) {
                                        // Extract food details from the database row
                                        $currentFoodID = $currentFoodData['FoodID']; // Unique ID for the food item
                                        $currentFoodName = $currentFoodData['FoodName']; // Name of the food item
                                        $currentFoodIcon = $currentFoodData['FoodIcon']; // Icon associated with the food item
                                        ?> 

                                        <!-- Label for the checkbox input representing a food item -->
                                        <label for="food<?php echo $currentFoodID;?>">
                                            <div class="entity-item food-item" id="getFood<?php echo $currentFoodID;?>">
                                                <!-- Display the food icon and name -->
                                                <p class="entity-icon" id="food-icon<?php echo $currentFoodID;?>">
                                                    <span><?php echo $currentFoodIcon; ?></span> <!-- Display the food icon -->
                                                    <span class="entity-text"> <?php echo htmlspecialchars($currentFoodName);?></span> <!-- Display the food name -->
                                                </p>

                                                <!-- Hidden checkbox input for the food item -->
                                                <input type="checkbox" name="foods[]" id="food<?php echo $currentFoodID;?>" value="<?php echo $currentFoodID;?>" style="display:none">
                                            </div><!-- .food -->
                                        </label>
                                    <?php } ?>
                                </div><!-- .food-selector -->

                            </section><!-- .food-selector-step -->
                        </div><!-- .slide -->

                        <!-- Slide 7: Weather selection -->
                        <div class="slide">
                            <section class="weather-selector-step">

                                <!-- Header for the weather selection section -->
                                <h3>
                                    <span class="japanese-title">天候</span> <!-- Japanese title for "Weather" -->
                                    <span class="english-title">Weather</span> <!-- English title -->
                                </h3>

                                <!-- Container for weathers with dynamic class assignment based on total weather options -->
                                <div class="weather-selector 
                                    <?php 
                                     // Apply different styling classes based on the total number of weather options
                                    if ($totalWeather > 8 && $totalWeather <= 10) {
                                        echo "large"; // Applies styling for more than 8 weather options
                                    } elseif ($totalWeather > 10 && $totalWeather <= 12) {
                                        echo "extra-large"; // Applies styling for more than 10 weather options
                                    } elseif ($totalWeather > 12 && $totalWeather <= 18) {
                                        echo "max"; // Applies styling for more than 12 weather options
                                    }?>">
                                
                                    <?php 
                                    // Loop through each weather option retrieved from the database
                                    while ($currentWeatherData = mysqli_fetch_assoc($queryRetrieveWeather)) {
                                        // Extract weather details from the database row
                                        $currentWeatherID = $currentWeatherData['WeatherID']; // Unique ID for the weather option
                                        $currentWeatherName = $currentWeatherData['WeatherName']; // Name of the weather option
                                        $currentWeatherIcon = $currentWeatherData['WeatherIcon']; // Icon associated with the weather option
                                        ?>

                                        <!-- Label for the checkbox input representing a weather option -->
                                        <label for="weather<?php echo $currentWeatherID;?>">
                                            <div class="entity-item weather-item" id="getWeather<?php echo $currentWeatherID;?>">
                                                <!-- Display the weather icon and name -->
                                                <p class="entity-icon" id="weather-icon<?php echo $currentWeatherID;?>">
                                                    <span><?php echo $currentWeatherIcon; ?></span> <!-- Display the weather icon -->
                                                    <span class="entity-text"><?php echo htmlspecialchars($currentWeatherName);?></span> <!-- Display the weather name -->
                                                </p>

                                                <!-- Hidden checkbox input for the weather option -->
                                                <input type="checkbox" name="weather[]" id="weather<?php echo $currentWeatherID;?>" value="<?php echo $currentWeatherID;?>" style="display:none">
                                            </div><!-- .weather -->
                                        </label>
                                    <?php } ?>
                                </div><!-- .weather-selector -->

                            </section><!-- .weather-selector-step -->
                        </div><!-- .slide -->

                        <!-- Slide 8: Goal Section -->
                        <div class="slide">
                            <section class="goal-selector-step">

                                <!-- Header for the goal selection section -->
                                <h3>
                                    <span class="japanese-title">目標</span> <!-- Japanese title for "Goal" -->
                                    <span class="english-title">Goal</span> <!-- English title -->
                                </h3>

                                <!-- Container for user goals with dynamic class assignment based on total user goals -->
                                <div class="goal-selector total-set-goals-<?php echo htmlspecialchars($totalUserGoals);?>">

                                    <?php 
                                    // Check if there are no goals set by the user
                                    if ($totalUserGoals == 0) { ?>
                                        <!-- Message indicating no goals set -->
                                        <div class="no-set-goals">
                                            <p>目標はまだ設定していない</p> 
                                        </div> 
                                    <?php } else {
                                        // Loop through each user goal retrieved from the database
                                        while($currentUserGoalData = mysqli_fetch_assoc($queryRetrieveUserGoals)){
                                            $currentGoalID = $currentUserGoalData['GoalID']; // Store the unique ID for the user's goal

                                            // Query to retrieve details of the current goal using the unique goal ID
                                            $queryGoalName = "SELECT * FROM goals WHERE GoalID = $currentGoalID"; // Query to retrieve goal details
                                            $goalNameQueryResult = mysqli_query($con,$queryGoalName); // Execute the query to retrieve goal details from the database
                                            $getGoalName = mysqli_fetch_assoc($goalNameQueryResult); // Fetch the result as an associative array to access goal details
                                            $currentGoalIDFromQuery = $getGoalName['GoalID']; // Store the goal ID for further processing
                                            ?> 

                                            <!-- Label for the checkbox input representing a user goal -->
                                            <label for="goal<?php echo $getGoalName['GoalID'];?>">
                                                <div class="goal-item" id="getGoalID<?php echo $getGoalName['GoalID'];?>">
                                                    <!-- Indicator indicator for the goal -->
                                                    <div class="goal-indicator" id="goal-indicator<?php echo $currentUserGoalData['UserGoalID'];?>"></div> 

                                                    <p class="goal-icon" id="goal-icon<?php echo $getGoalName['GoalID'];?>">
                                                        <!-- Display the icon for the goal -->
                                                        <span><?php echo $getGoalName['GoalIcon']; ?></span> 

                                                        <span class="goal-text">
                                                            <!-- Display the name of the goal -->
                                                            <span><?php echo htmlspecialchars($getGoalName['GoalName']);?></span> 

                                                            <span class="goal-consecutive-days">
                                                        
                                                                <?php 
                                                                // Get today's date for tracking submissions
                                                                $todayDate = date("Y-m-d");

                                                                // Query to check if the user has submitted any mood entries for today
                                                                $queryTodayUpdateMood = "SELECT * FROM dailytracking WHERE UserID = $userID AND Date = '$todayDate'"; /// Filter by user ID and today's date
                                                                $resultTodayUpdateMood = mysqli_query($con, $queryTodayUpdateMood); // Execute the query
                                                                $rowsTodayUpdateMood = mysqli_num_rows($resultTodayUpdateMood); // Count entries for today

                                                                // If no submission today, check the previous day
                                                                if($rowsTodayUpdateMood == 0){
                                                                    $todayDate = date('Y-m-d', strtotime($todayDate .' -1 day')); // Move to the previous day
                                                                } 

                                                                // Query to get consecutive tracking days for the goal
                                                                $queryGetDates = 
                                                                    "SELECT t.UserGoalID, t.Date, u.GoalID, u.UserID 
                                                                    FROM usergoals u INNER JOIN trackgoals t
                                                                    ON u.UserGoalID = t.UserGoalID
                                                                    where GoalID = $currentGoalIDFromQuery AND UserID = $userID and Date <= '$todayDate' 
                                                                    ORDER BY Date desc"; // Order results by date in descending order
                                                                $resultGetDays = mysqli_query($con, $queryGetDates); // Execute the query
                                                                $rowsResultGetDay = mysqli_num_rows($resultGetDays); // Count tracking days available
                                                                        
                                                                $consecutiveDaysCount = 0;  // Initialize count of consecutive days

                                                                // Loop through the results to count consecutive days
                                                                while ($rowgetDate = mysqli_fetch_assoc($resultGetDays)) {
                                                                    $daysDatee = $rowgetDate['Date']; // Retrieve date for the current row

                                                                    // Check if the current date matches the date in the result set
                                                                    if ($todayDate == $daysDatee) {
                                                                        $consecutiveDaysCount += 1; // Increment count for each consecutive day
                                                                    } else {
                                                                        break; // Stop counting if the streak is broken
                                                                    }

                                                                    // Move to the previous day
                                                                    $todayDate = date('Y-m-d', strtotime($todayDate .' -1 day'));
                                                                } ?>

                                                                <?php echo $consecutiveDaysCount; ?>日の継続 <!-- Display the number of consecutive days -->
                                                            </span>
                                                        </span>
                                                    </p>

                                                    <!-- Hidden checkbox input for the goal -->
                                                    <input type="checkbox" name="goal[]" id="goal<?php echo $getGoalName['GoalID'];?>" value="<?php echo $currentUserGoalData['UserGoalID'];?>" style="display:none">
                                                </div><!-- .goal -->
                                            </label>
                                            
                                            <!-- Hidden input for tracking goals displayed on the page -->
                                            <input type="hidden" name="goalOnPage[]" value="<?php echo $currentUserGoalData['UserGoalID'];?>" checked>
                                            
                                        <?php } // End of while loop for user goals
                                    } ?>
                                </div><!-- .goals -->

                            </section><!-- .goal-selector-step eight -->
                        </div><!-- .slide -->

                        <!-- Slide 9: Sleep Time Section -->
                        <div class="slide">
                            <section class="sleep-time-selector-step">
                                <!-- Header for the sleep time input section -->
                                <h3>
                                    <span class="japanese-title">睡眠時間</span> <!-- Japanese title for "Sleep Time" -->
                                    <span class="english-title">Sleep Time</span> <!-- English title -->
                                </h3>

                                <!-- Wrapper for sleep time controls -->
                                <div class="sleep-time-wrapper">
                                    <!-- Button to increase sleep time -->
                                    <div class="sleep-time-buttons">
                                        <div id="increase-sleep-time" class="increase-sleep-time">
                                            <i class="fa-solid fa-angles-up"></i><!-- Icon for increasing time -->
                                        </div> 
                                    </div><!-- .buttons -->

                                    <!-- Display current sleep time -->
                                    <div class="sleep-time-selector">
                                        <p class="time">
                                            <span class="minute">00</span>: <!-- Minutes display -->
                                            <span class="second">00</span> <!-- Seconds display -->
                                        </p>
                                    </div><!-- .sleep-time-selector -->

                                    <!-- Button to decrease sleep time -->
                                    <div class="sleep-time-buttons">
                                        <div id="decrease-sleep-time" class="decrease-sleep-time">
                                            <i class="fa-solid fa-angles-down"></i> <!-- Icon for decreasing time -->
                                        </div>
                                    </div><!-- .buttons -->

                                    <!-- Button to reset sleep timer -->
                                    <div class="reset-sleep-time">
                                        <div class="teritary-btn">
                                            <p>Reset Timer</p><!-- Reset timer button text -->
                                        </div>
                                    </div><!-- .reset-sleep-time -->
                                </div><!-- .sleep-time-wrapper-->
                                
                                <!-- Hidden input to store sleep time value -->
                                <input type="hidden"  id="sleep-time" name="sleepingTime" value="" checked>

                            </section><!-- .sleep-time-selector-step -->
                        </div><!-- .slide -->

                        <!-- Slide 10: Daity Input Section -->
                        <div class="slide">
                            <section class="memo-input-section">
                                <!-- Header for the diary input section -->
                                <h3>
                                    <span class="japanese-title">日記</span> <!-- Japanese title for "Diary" -->
                                    <span class="english-title">Quick Note</span> <!-- English title -->
                                </h3>

                                <!-- Wrapper for diary entry controls -->
                                <div class="memo-textarea-wrapper">
                                    <label for="memo"></label>
                                    <!-- Text area for user to input their diary entry -->
                                    <textarea id="textarea" name="textarea" rows="10" cols="60" placeholder="なにかを書く"></textarea>
                                </div><!-- .memo-wrapper -->

                                <!-- Button for posting the diary entry -->
                                <div class="memo-post-button-wrapper <?php if (!empty($selectedMoodID)) {  echo " mood-is-selected";} ?>" id="mood-is-selected">
                                    <input type="hidden" name="sendDateReg" value="<?php echo $date;?>"> <!-- Hidden input for the date of the diary entry -->
                                    <button class="primary-btn" type="submit" value="postDay" id="subBut" disabled>Post</button> <!-- Post button, initially disabled -->
                                </div><!-- .memo-post-button-wrapper -->  

                                <!-- Prompt for selecting mood -->
                                <div id="mood-selection-prompt" class="<?php if (!empty($selectedMoodID)) {  echo "mood-selection-prompt";} ?>">
                                    <p>「Mood」を選択してください</p> <!-- Prompt to select a mood -->
                                </div>
                                
                            </section><!-- .RegisterMemo tenth -->
                        </div><!-- .slide -->
                        
                    </form><!-- End of the form -->

                </div><!-- .slider-->
            </section><!-- .mood-registration-wrapper -->
        </div><!-- .main-wrapper -->
                                    
        <!-- Form for submitting data related to a bad day -->
        <form method="post" action="./server-side/add_mood.php">
            <!-- Hidden input to send the date of the next entry; populated with PHP variable $date -->
            <input type="hidden" name="sendDateNext" value="<?php echo $date;?>">
        </form>

    </body>


    <script>
        <?php 
        // Feelings
        // Retrieve all feelings from the database
        $queryRetrieveFeelingsAll = mysqli_query($con,"SELECT * FROM Feelings");

        // Loop through each feeling retrieved from the database
        while ($rowFeelingClick = mysqli_fetch_assoc($queryRetrieveFeelingsAll)) {
            // Get the FeelingID for the current feeling
            $feeling = $rowFeelingClick['FeelingID']; ?>

            // Add a click event listener to the corresponding feeling icon
            document.getElementById("feelingPo<?php echo $feeling; ?>").addEventListener("click", () => {
                // Get the target element associated with the feeling
                const targetElement = document.getElementById("feelingID<?php echo $feeling; ?>");
                // Toggle the 'selected-feeling' class to show the selection
                targetElement.classList.toggle('selected-feeling');
            });
        <?php }


        // Activities
        // Retrieve all activities from the database
        $queryActivitiesAll = mysqli_query($con,"SELECT * FROM Activities");

        // Loop through each activity retrieved from the database
        while($rowActivityClick = mysqli_fetch_assoc($queryActivitiesAll)) {
            // Get the ActivityID for the current activity
            $activity = $rowActivityClick['ActivityID']; ?>

            // Add a click event listener to the corresponding activity icon
            document.getElementById("activity-icon<?php echo $activity; ?>").addEventListener("click", () => {
                // Get the target element associated with the activity
                const targetElement = document.getElementById("getActivity<?php echo $activity; ?>");
                // Toggle the 'selected-activity' class to show the selection
                targetElement.classList.toggle('selected-activity');
            });
        <?php }


        // Companies
        // Retrieve all companies from the database
        $queryCompanyAll = mysqli_query($con,"SELECT * FROM Company");

        // Loop through each company retrieved from the database
        while($rowCompanyClick = mysqli_fetch_assoc($queryCompanyAll)) {
            // Get the CompanyID for the current company
            $company = $rowCompanyClick['CompanyID']; ?>

            // Add a click event listener to the corresponding company icon
            document.getElementById("company-icon<?php echo $company; ?>").addEventListener("click", () => {
                // Get the target element associated with the company
                const targetElement = document.getElementById("getCompany<?php echo $company; ?>");
                // Toggle the 'selected-company' class to show the selection
                targetElement.classList.toggle('selected-company');
            });
        <?php }


        // Locations
        // Retrieve all locations from the database
        $queryLocationsAll = mysqli_query($con,"SELECT * FROM locations");

        // Loop through each location retrieved from the database
        while($rowLocationClick = mysqli_fetch_assoc($queryLocationsAll)) {
            // Get the LocationID for the current location
            $location = $rowLocationClick['LocationID']; ?>

            // Add a click event listener to the corresponding location icon
            document.getElementById("location-icon<?php echo $location; ?>").addEventListener("click", () => {
                // Get the target element associated with the location
                const targetElement = document.getElementById("getLocation<?php echo $location; ?>");
                // Toggle the 'selected-location' class to show the selection
                targetElement.classList.toggle('selected-location');
            });
        <?php }


        // Foods
        // Retrieve all foods from the database
        $queryFoodsAll = mysqli_query($con,"SELECT * FROM foods");

        // Loop through each food item retrieved from the database
        while($rowFoodClick = mysqli_fetch_assoc($queryFoodsAll)) {
            // Get the FoodID for the current food item
            $food = $rowFoodClick['FoodID']; ?>

            // Add a click event listener to the corresponding food icon
            document.getElementById("food-icon<?php echo $food; ?>").addEventListener("click", () => {
                // Get the target element associated with the food item
                const targetElement = document.getElementById("getFood<?php echo $food; ?>");
                // Toggle the 'selected-food' class to show the selection
                targetElement.classList.toggle('selected-food');
            });
        <?php }


        // Weather
        // Retrieve all weather conditions from the database
        $queryWeatherAll = mysqli_query($con,"SELECT * FROM weather");

        // Loop through each weather condition retrieved from the database
        while($rowWeatherClick = mysqli_fetch_assoc($queryWeatherAll)) {
            // Get the WeatherID for the current weather condition
            $weather = $rowWeatherClick['WeatherID']; ?>

            // Add a click event listener to the corresponding weather icon
            document.getElementById("weather-icon<?php echo $weather; ?>").addEventListener("click", () => {
                // Get the target element associated with the weather condition
                const targetElement = document.getElementById("getWeather<?php echo $weather; ?>");
                // Toggle the 'selected-weather' class to show the selection
                targetElement.classList.toggle('selected-weather');
            });
        <?php }


        // Goals
        // Retrieve all user goals from the database for the logged-in user
        $queryRetrieveGoals = mysqli_query($con, "SELECT * FROM usergoals WHERE UserID = $userID");

        // Loop through each user goal retrieved from the database
        while($row = mysqli_fetch_assoc($queryRetrieveGoals)){
            $currentGoalID = $row['GoalID']; // Get the GoalID for the current goal
            $queryGoalName = "select * from goals WHERE GoalID = $currentGoalID"; // Prepare a query to retrieve the goal name based on the GoalID
            $resultqueryGoalName = mysqli_query($con,$queryGoalName); // Prepare a query to retrieve the goal name based on the GoalID
            $getGoalName = mysqli_fetch_assoc($resultqueryGoalName); // Fetch the goal name from the result set
            $userGoalID = $row['UserGoalID']; // Get the UserGoalID for the current goal
            ?>
            
            // Add a click event listener to the corresponding goal icon
            document.getElementById("goal-icon<?php echo $getGoalName['GoalID']; ?>").addEventListener("click", () => {
                // Get the circle element associated with the user's goal
                const circleElement = document.getElementById("goal-indicator<?php echo $userGoalID; ?>");
                // Toggle the 'goal-indicator-checked' class and update the inner HTML based on the checked state
                const isChecked = circleElement.classList.toggle('goal-indicator-checked');
                circleElement.innerHTML = isChecked ? '<i class="fa-solid fa-check"></i>' : '';
            });
        <?php } ?>

    </script>
       
</html>