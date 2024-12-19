<?php
/**
 * Page Name: Mood Associations Display
 * Description: This page retrieves and displays popular associations related to the user's current mood, including companies, locations, foods, and weather. 
 *      It checks if there are any popular associations for the selected mood and retrieves their details from the database. Each association is displayed 
 *      with an icon, name, and the count of times it has been tracked, providing users with insights into their mood patterns. If no associations are found, 
 *      a message encourages users to add more tags for better insights into their behavior.
 * Author: Moa Burke
 * Date: '" . MINIMUM_TRACK_COUNT . "'024-10-29
 *
 * Notes:
 * - Utilizes PHP for server-side processing and MySQL for data retrieval.
 * - Features dynamic content that adapts based on the selected mood and associated tags.
 *
 * Dependencies:
 * - Requires PHP for server-side processing and MySQL database connection.
 * - JavaScript may be used for enhancing user interactions and dynamic updates.
 * - FontAwesome for icons representation.
 *
 */
session_start();// Start the session

// Define a constant for the base path
define('BASE_PATH', '../../server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); //Include the utiity functions
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
include(BASE_PATH . "constants.php"); // Include the constants file
include(BASE_PATH . "header/user_layout.php"); // Include the user header layout file

define('MINIMUM_TRACK_COUNT', 2);

// Check if the user is logged in and retrieve user data
$userData = check_login($con); // Retrieve user data FROM the login check
$userID = $userData['UserID'];  // Get the UserID of the logged-in user

// Execute a query to retrieve all records FROM the "moods" table
$queryGetMoods = "SELECT * FROM moods";
$resultGetMoods = mysqli_query($con, $queryGetMoods); // Store the query result
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Load mutual head components and necessary scripts/styles -->
        <?php includeHeadAssets(); ?>
        <!-- This script handles the tab interactions in the user interface -->
        <script src="../../assets/javascript/tab_interactions.js" defer></script> 
    </head>

    <body>
        <header class="sidebar-navigation insights-navigation">
            <!-- Include the mutual header for user navigation -->
            <?php renderUserNavigation(); ?>
        </header>
        <!-- Logout button and sticky header for logged-in user -->
        <?php renderUserHeaderWithLogout($userData); ?>
        
        <div class="main-wrapper">
            <h2>Insights</h2>

            <!-- Breadcrumb navigation -->
            <div class="breadcrumbs">
                <a href="../user_home.php"><p>Top Page</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <a href="./insights.php"><p>Insights</p></a>
                <i class="fa-solid fa-angle-right fa-sm"></i>
                <p class="bread-active">Mood Associations</p>
            </div><!--. breadcrumbs -->
           
            <!-- Wrapper for the 'Commonly Together' section, with tab navigation for different mood categories -->
            <section class="mood-association-wrapper">

                <!-- Header section for the 'Commonly Together' tabs -->
                <div class="mood-association-header">

                    <!-- Title area displaying both Japanese and English titles -->
                    <div class="mood-associations-title">
                        <!-- Main title for the section: displays "Commonly Together" in both Japanese and English -->
                        <div class="text">
                            <h3>
                                <span class="japanese-title">気分の関連付け</span> <!-- Japanese title for "Mood Associations" -->
                                <span class="english-title">Mood Associations</span> <!-- English title -->
                            </h3>
                        </div><!-- .mood-associations-title -->

                        <div class="mood-association-tab-navigation">

                            <!-- Tab buttons for each mood category, displayed as a navigation bar -->
                            <div class="mood-tabs mood-associations-page-tabs">
                                <ul>
                                    <!-- Tab for 'Great Mood', set as the default active tab -->
                                    <li data-tab-target="#great-mood" class="active tab mood-great">
                                        <div class="navigation-content" id="active-navigation"><p>最高</p></div> 
                                    </li>

                                    <!-- Tab for 'Good Mood' -->
                                    <li data-tab-target="#good-mood" class = "tab mood-good">
                                        <div class="navigation-content"><p>良い</p></div> 
                                    </li>

                                    <!-- Tab for 'Okay Mood' -->
                                    <li data-tab-target="#okay-mood" class="tab mood-okay">
                                        <div class="navigation-content"><p>普通</p></div> 
                                    </li>

                                    <!-- Tab for 'Bad Mood' -->
                                    <li data-tab-target="#bad-mood" class="tab mood-bad">
                                        <div class="navigation-content bad-mood"><p>悪い</p></div> 
                                    </li>

                                    <!-- Tab for 'Awful Mood' -->
                                    <li data-tab-target="#awful-mood" class="tab mood-awful">
                                        <div class="navigation-content awful-mood"><p>最悪</p></div> 
                                    </li>
                                </ul>
                            </div><!-- .mood-tabs -->

                        </div><!-- .mood-association-tab-navigation -->

                    </div><!-- .title -->
                </div><!-- .mood-associated-item-top -->

                <div class="mood-content-wrapper mood-associations-page">

                    <!-- Section for displaying activities associated with the 'great mood' category -->
                    <section id="great-mood" data-tab-content class="active">
                        <?php 
                        // Fetch mood details FROM the database query result
                        $moodData = mysqli_fetch_assoc($resultGetMoods);

                        // ID representing the selected mood 
                        $moodID = MOOD_GREAT;
                
                        // Query to retrieve activities that are commonly tracked with the specified great mood
                        // The query filters activities associated with this mood based on the user ID and activity count threshold
                        $queryPopularActivities = 
                            "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                            FROM trackactivities 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY ActivityID 
                            HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular activities
                        $resultPopularActivities = mysqli_query($con, $queryPopularActivities); 
                        // Count the number of activities found
                        $popularActivitiesCount = mysqli_num_rows($resultPopularActivities); 

                        // Query to retrieve data on companies associated with the specified great mood
                        $queryPopularCompanies = 
                            "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                            FROM trackcompany 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY CompanyID 
                            HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";       
                        // Execute the query and retrieve the result set for popular companies
                        $resultPopularCompanies = mysqli_query($con, $queryPopularCompanies);
                        // Count the number of companies found
                        $popularCompaniesCount = mysqli_num_rows($resultPopularCompanies);


                        // Query to retrieve data on locations associated with the specified great mood
                        $queryPopularLocations = 
                            "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                            FROM tracklocations 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY LocationID 
                            HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular locations
                        $resultPopularLocations = mysqli_query($con, $queryPopularLocations);
                        // Count the number of locations found
                        $popularLocationsCount = mysqli_num_rows($resultPopularLocations); 

                        // Query to retrieve data on foods associated with the specified great mood
                        $queryPopularFood = 
                            "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                            FROM trackfoods 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY FoodID 
                            HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular food
                        $resultPopularFood = mysqli_query($con, $queryPopularFood);
                        // Count the number of foods found
                        $popularFoodCount = mysqli_num_rows($resultPopularFood); 

                        // Query to retrieve data on weather associated with the specified great mood
                        $queryPopularWeather = 
                            "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                            FROM trackweather 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY WeatherID 
                            HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular weather
                        $resultPopularWeather= mysqli_query($con, $queryPopularWeather);
                        // Count the number of weathers found
                        $popularWeatherCount = mysqli_num_rows($resultPopularWeather); 

                        // Calculate the total number of tracked entities associated with the specified great mood
                        $totalTrackedEntities = $popularActivitiesCount + $popularCompaniesCount + $popularLocationsCount + $popularFoodCount + $popularWeatherCount; // Sum counts of activities, companies, locations, foods, and weather for the total

                        // Check if there are any tracked entities associated with the specified mood.
                        // This condition ensures that we only display the following content if there are
                        // activities, companies, locations, foods, or weather records that match the user's mood.
                        // If no tracked entities exist for the current mood, the content below will not be rendered.
                        if ($totalTrackedEntities > 0) { ?> 
                            <div class="mood-<?php echo $moodID; ?>">
                                <div class=" mood-content-section">
                                    
                                    <!-- Display a message indicating the activities associated with the current mood -->
                                    <p class="mood-content-intro">
                                        <span>気分が<span class="great-mood"><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></span>時によくタグしていること</span>
                                    </p>
                                    
                                    <div class="mood-associated-list">
                                        <?php
                                        // Check if there are any popular activities associated with the current mood
                                        // If the count of popular activities is greater than 0, proceed to output the data
                                        if ($popularActivitiesCount > 0) {
                                            while ($activityCount = mysqli_fetch_assoc($resultPopularActivities)) {?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the Activity ID for the current activity
                                                    $activityID = $activityCount['ActivityID'];

                                                    // Prepare a query to get detailed information about the activity using its ID
                                                    $queryActivityData = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                    // Execute the query to get the activity details
                                                    $resultActivityData = mysqli_query($con, $queryActivityData);
                                                    // Fetch the associated activity data
                                                    $activityData = mysqli_fetch_assoc($resultActivityData); ?>

                                                    <!-- Display the activity icon -->
                                                    <div class="mood-item-icon"><?php echo $activityData['ActivityIcon']; ?> </div>
                                                    <!-- Display the name of the activity -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($activityData['ActivityName']); ?></p>
                                                    <!-- Display the count of times this activity has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $activityCount['cntTrackAct'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular companies associated with the current mood
                                        // If the count of popular companies is greater than 0, proceed to output the data
                                        if ($popularCompaniesCount > 0) {
                                            while ($companyCount = mysqli_fetch_assoc($resultPopularCompanies)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the CompanyID for the current company
                                                    $companyID = $companyCount['CompanyID'];

                                                    // Prepare a query to get detailed information about the company using its ID
                                                    $queryCompanyData = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                    // Execute the query to get the company details
                                                    $resultCompanyData = mysqli_query($con, $queryCompanyData);
                                                    // Fetch the associated company data
                                                    $companyData = mysqli_fetch_assoc($resultCompanyData); ?>

                                                    <!-- Display the company icon -->
                                                    <div class="mood-item-icon"><?php echo  $companyData['CompanyIcon']; ?> </div>
                                                    <!-- Display the name of the company -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($companyData['CompanyName']); ?></p>
                                                    <!-- Display the count of times this company has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $companyCount['cntTrackCom'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular locations associated with the current mood
                                        // If the count of popular locations is greater than 0, proceed to output the data
                                        if ($popularLocationsCount > 0) {
                                            while ($locationCount = mysqli_fetch_assoc($resultPopularLocations)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the LocationID for the current location
                                                    $locationID = $locationCount['LocationID'];

                                                    // Prepare a query to get detailed information about the location using its ID
                                                    $queryLocationData = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                    // Execute the query to get the location details
                                                    $resultLocationData = mysqli_query($con, $queryLocationData);
                                                    // Fetch the associated location data
                                                    $locationData = mysqli_fetch_assoc($resultLocationData); ?>

                                                    <!-- Display the location icon -->
                                                    <div class="mood-item-icon"><?php echo $locationData['LocationIcon']; ?> </div>
                                                    <!-- Display the name of the location -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($locationData['LocationName']);  ?></p>
                                                    <!-- Display the count of times this location has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $locationCount['cntTrackLoc']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }
                                
                                        // Check if there are any popular food associated with the current mood
                                        // If the count of popular food is greater than 0, proceed to output the data
                                        if ($popularFoodCount > 0) {
                                            while ($foodCount = mysqli_fetch_assoc($resultPopularFood)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the FoodID for the current food
                                                    $foodID = $foodCount['FoodID'];

                                                    // Prepare a query to get detailed information about the food using its ID
                                                    $queryFoodData = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                    // Execute the query to get the food details
                                                    $resultFoodData = mysqli_query($con, $queryFoodData);
                                                    // Fetch the associated food data
                                                    $foodData = mysqli_fetch_assoc($resultFoodData); ?>

                                                    <!-- Display the food icon -->
                                                    <div class="mood-item-icon"><?php echo $foodData['FoodIcon']; ?> </div>
                                                    <!-- Display the name of the food -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($foodData['FoodName']); ?></p>
                                                    <!-- Display the count of times this food has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $foodCount['cntTrackFood']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular weather associated with the current mood
                                        // If the count of popular weather is greater than 0, proceed to output the data
                                        if ($popularWeatherCount > 0) {
                                            while ($weatherCount = mysqli_fetch_assoc($resultPopularWeather)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the WeatherID for the current weather
                                                    $weatherID = $weatherCount['WeatherID'];

                                                    // Prepare a query to get detailed information about the weather using its ID
                                                    $queryWeatherData = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                    // Execute the query to get the weather details
                                                    $resultWeatherData = mysqli_query($con, $queryWeatherData);
                                                    // Fetch the associated weather data
                                                    $weatherData = mysqli_fetch_assoc($resultWeatherData); ?>

                                                        <!-- Display the weather icon -->
                                                    <div class="mood-item-icon"><?php echo $weatherData['WeatherIcon']; ?> </div>
                                                    <!-- Display the name of the weather -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($weatherData['WeatherName']); ?></p>
                                                    <!-- Display the count of times this weather has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $weatherCount['cntTrackWea']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        } ?>

                                    </div><!-- .mood-associated-list -->

                                </div><!-- . mood-content-section -->
                            </div><!-- . mood-content-section-wrap -->

                        <?php } else { ?>  
                            
                        <!-- Container for displaying a message when no activities are associated with the current mood -->
                        <div class=" mood-content-section-wrap-empty">
                            <!-- Text indicating the need for more tags to gain insights about behavior patterns -->
                            <div class="no-mood-records-wrapper">
                                <p>気分が<b><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>
                            </div><!-- .no-mood-records-wrapper -->
                        </div><!-- . mood-content-section-wrap-empty -->

                        <?php } ?>
                    </section> <!-- #greatMood -->

                    <section id="good-mood" data-tab-content>
                        <?php 
                        // Fetch mood details FROM the database query result
                        $moodData = mysqli_fetch_assoc($resultGetMoods);
          
                        // ID representing the selected mood 
                        $moodID = MOOD_GOOD;
     
                        // Query to retrieve activities that are commonly tracked with the specified good mood
                        // The query filters activities associated with this mood based on the user ID and activity count threshold
                        $queryPopularActivities = 
                            "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                            FROM trackactivities 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY ActivityID 
                            HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular activities
                        $resultPopularActivities = mysqli_query($con, $queryPopularActivities); 
                        // Count the number of activities found
                        $popularActivitiesCount = mysqli_num_rows($resultPopularActivities); 

                        // Query to retrieve data on companies associated with the specified good mood
                        $queryPopularCompanies = 
                            "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                            FROM trackcompany 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY CompanyID 
                            HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";       
                        // Execute the query and retrieve the result set for popular companies
                        $resultPopularCompanies = mysqli_query($con, $queryPopularCompanies);
                        // Count the number of companies found
                        $popularCompaniesCount = mysqli_num_rows($resultPopularCompanies);


                        // Query to retrieve data on locations associated with the specified good mood
                        $queryPopularLocations = 
                            "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                            FROM tracklocations 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY LocationID 
                            HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular locations
                        $resultPopularLocations = mysqli_query($con, $queryPopularLocations);
                        // Count the number of locations found
                        $popularLocationsCount = mysqli_num_rows($resultPopularLocations); 

                        // Query to retrieve data on foods associated with the specified good mood
                        $queryPopularFood = 
                            "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                            FROM trackfoods 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY FoodID 
                            HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular food
                        $resultPopularFood = mysqli_query($con, $queryPopularFood);
                        // Count the number of foods found
                        $popularFoodCount = mysqli_num_rows($resultPopularFood); 

                        // Query to retrieve data on weather associated with the specified good mood
                        $queryPopularWeather = 
                            "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                            FROM trackweather 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY WeatherID 
                            HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular weather
                        $resultPopularWeather= mysqli_query($con, $queryPopularWeather);
                        // Count the number of weathers found
                        $popularWeatherCount = mysqli_num_rows($resultPopularWeather); 


                        // Calculate the total number of tracked entities associated with the specified good mood
                        $totalTrackedEntities = $popularActivitiesCount + $popularCompaniesCount + $popularLocationsCount + $popularFoodCount + $popularWeatherCount; // Sum counts of activities, companies, locations, foods, and weather for the total

                        // Check if there are any tracked entities associated with the specified mood.
                        // This condition ensures that we only display the following content if there are
                        // activities, companies, locations, foods, or weather records that match the user's mood.
                        // If no tracked entities exist for the current mood, the content below will not be rendered.
                        if ($totalTrackedEntities > 0) { ?> 
                            <div class="mood-<?php echo $mood; ?>">
                                <div class=" mood-content-section">

                                    <!-- Display a message indicating the activities associated with the current mood -->
                                    <p class="mood-content-intro">
                                        <span>気分が<span class="good-mood"><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></span>時によくタグしていること</span>
                                    </p>

                                    <div class="mood-associated-list">
                                        <?php
                                        // Check if there are any popular activities associated with the current mood
                                        // If the count of popular activities is greater than 0, proceed to output the data
                                        if ($popularActivitiesCount > 0) {
                                            while ($activityCount = mysqli_fetch_assoc($resultPopularActivities)) {?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the Activity ID for the current activity
                                                    $activityID = $activityCount['ActivityID'];

                                                    // Prepare a query to get detailed information about the activity using its ID
                                                    $queryActivityData = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                    // Execute the query to get the activity details
                                                    $resultActivityData = mysqli_query($con, $queryActivityData);
                                                    // Fetch the associated activity data
                                                    $activityData = mysqli_fetch_assoc($resultActivityData); ?>

                                                    <!-- Display the activity icon -->
                                                    <div class="mood-item-icon"><?php echo $activityData['ActivityIcon']; ?> </div>
                                                    <!-- Display the name of the activity -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($activityData['ActivityName']); ?></p>
                                                    <!-- Display the count of times this activity has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $activityCount['cntTrackAct'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular companies associated with the current mood
                                        // If the count of popular companies is greater than 0, proceed to output the data
                                        if ($popularCompaniesCount > 0) {
                                            while ($companyCount = mysqli_fetch_assoc($resultPopularCompanies)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the CompanyID for the current company
                                                    $companyID = $companyCount['CompanyID'];

                                                    // Prepare a query to get detailed information about the company using its ID
                                                    $queryCompanyData = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                    // Execute the query to get the company details
                                                    $resultCompanyData = mysqli_query($con, $queryCompanyData);
                                                    // Fetch the associated company data
                                                    $companyData = mysqli_fetch_assoc($resultCompanyData); ?>

                                                    <!-- Display the company icon -->
                                                    <div class="mood-item-icon"><?php echo  $companyData['CompanyIcon']; ?> </div>
                                                    <!-- Display the name of the company -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($companyData['CompanyName']); ?></p>
                                                    <!-- Display the count of times this company has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $companyCount['cntTrackCom'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular locations associated with the current mood
                                        // If the count of popular locations is greater than 0, proceed to output the data
                                        if ($popularLocationsCount > 0) {
                                            while ($locationCount = mysqli_fetch_assoc($resultPopularLocations)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the LocationID for the current location
                                                    $locationID = $locationCount['LocationID'];

                                                    // Prepare a query to get detailed information about the location using its ID
                                                    $queryLocationData = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                    // Execute the query to get the location details
                                                    $resultLocationData = mysqli_query($con, $queryLocationData);
                                                    // Fetch the associated location data
                                                    $locationData = mysqli_fetch_assoc($resultLocationData); ?>

                                                    <!-- Display the location icon -->
                                                    <div class="mood-item-icon"><?php echo $locationData['LocationIcon']; ?> </div>
                                                    <!-- Display the name of the location -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($locationData['LocationName']);  ?></p>
                                                    <!-- Display the count of times this location has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $locationCount['cntTrackLoc']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }
                                
                                        // Check if there are any popular food associated with the current mood
                                        // If the count of popular food is greater than 0, proceed to output the data
                                        if ($popularFoodCount > 0) {
                                            while ($foodCount = mysqli_fetch_assoc($resultPopularFood)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the FoodID for the current food
                                                    $foodID = $foodCount['FoodID'];

                                                    // Prepare a query to get detailed information about the food using its ID
                                                    $queryFoodData = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                    // Execute the query to get the food details
                                                    $resultFoodData = mysqli_query($con, $queryFoodData);
                                                    // Fetch the associated food data
                                                    $foodData = mysqli_fetch_assoc($resultFoodData); ?>

                                                    <!-- Display the food icon -->
                                                    <div class="mood-item-icon"><?php echo $foodData['FoodIcon']; ?> </div>
                                                    <!-- Display the name of the food -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($foodData['FoodName']); ?></p>
                                                    <!-- Display the count of times this food has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $foodCount['cntTrackFood']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular weather associated with the current mood
                                        // If the count of popular weather is greater than 0, proceed to output the data
                                        if ($popularWeatherCount > 0) {
                                            while ($weatherCount = mysqli_fetch_assoc($resultPopularWeather)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the WeatherID for the current weather
                                                    $weatherID = $weatherCount['WeatherID'];

                                                    // Prepare a query to get detailed information about the weather using its ID
                                                    $queryWeatherData = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                    // Execute the query to get the weather details
                                                    $resultWeatherData = mysqli_query($con, $queryWeatherData);
                                                    // Fetch the associated weather data
                                                    $weatherData = mysqli_fetch_assoc($resultWeatherData); ?>

                                                     <!-- Display the weather icon -->
                                                    <div class="mood-item-icon"><?php echo $weatherData['WeatherIcon']; ?> </div>
                                                    <!-- Display the name of the weather -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($weatherData['WeatherName']); ?></p>
                                                    <!-- Display the count of times this weather has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $weatherCount['cntTrackWea']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        } ?>

                                    </div><!-- .mood-associated-list -->

                                </div><!-- . mood-content-section -->
                            </div><!-- . mood-content-section-wrap -->

                        <?php } else { ?> 

                        <!-- Container for displaying a message when no activities are associated with the current mood -->
                        <div class=" mood-content-section-wrap-empty">
                            <!-- Text indicating the need for more tags to gain insights about behavior patterns -->
                            <div class="no-mood-records-wrapper">
                                <p>気分が<b><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>
                            </div><!-- .no-mood-records-wrapper -->
                        </div><!-- . mood-content-section-wrap-empty -->

                        <?php } ?>
                    </section> <!-- #good-mood -->

                    <section id="okay-mood" data-tab-content>
                        <?php 
                        // Fetch mood details FROM the database query result
                        $moodData = mysqli_fetch_assoc($resultGetMoods);

                        // ID representing the selected mood 
                        $moodID = MOOD_OKAY;
                        
                        // Query to retrieve activities that are commonly tracked with the specified okay mood
                        // The query filters activities associated with this mood based on the user ID and activity count threshold
                        $queryPopularActivities = 
                            "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                            FROM trackactivities 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY ActivityID 
                            HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular activities
                        $resultPopularActivities = mysqli_query($con, $queryPopularActivities); 
                        // Count the number of activities found
                        $popularActivitiesCount = mysqli_num_rows($resultPopularActivities); 

                        // Query to retrieve data on companies associated with the specified okay mood
                        $queryPopularCompanies = 
                            "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                            FROM trackcompany 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY CompanyID 
                            HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";       
                        // Execute the query and retrieve the result set for popular companies
                        $resultPopularCompanies = mysqli_query($con, $queryPopularCompanies);
                        // Count the number of companies found
                        $popularCompaniesCount = mysqli_num_rows($resultPopularCompanies);


                        // Query to retrieve data on locations associated with the specified okay mood
                        $queryPopularLocations = 
                            "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                            FROM tracklocations 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY LocationID 
                            HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular locations
                        $resultPopularLocations = mysqli_query($con, $queryPopularLocations);
                        // Count the number of locations found
                        $popularLocationsCount = mysqli_num_rows($resultPopularLocations); 

                        // Query to retrieve data on foods associated with the specified okay mood
                        $queryPopularFood = 
                            "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                            FROM trackfoods 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY FoodID 
                            HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular food
                        $resultPopularFood = mysqli_query($con, $queryPopularFood);
                        // Count the number of foods found
                        $popularFoodCount = mysqli_num_rows($resultPopularFood); 

                        // Query to retrieve data on weather associated with the specified okay mood
                        $queryPopularWeather = 
                            "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                            FROM trackweather 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY WeatherID 
                            HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular weather
                        $resultPopularWeather= mysqli_query($con, $queryPopularWeather);
                        // Count the number of weathers found
                        $popularWeatherCount = mysqli_num_rows($resultPopularWeather);  

                        // Calculate the total number of tracked entities associated with the specified okay mood
                        $totalTrackedEntities = $popularActivitiesCount + $popularCompaniesCount + $popularLocationsCount + $popularFoodCount + $popularWeatherCount; // Sum counts of activities, companies, locations, foods, and weather for the total

                        // Check if there are any tracked entities associated with the specified mood.
                        // This condition ensures that we only display the following content if there are
                        // activities, companies, locations, foods, or weather records that match the user's mood.
                        // If no tracked entities exist for the current mood, the content below will not be rendered.
                        if ($totalTrackedEntities > 0) { ?> 
                            <div class="mood-<?php echo $mood; ?>">
                                <div class=" mood-content-section">

                                    <!-- Display a message indicating the activities associated with the current mood -->
                                    <p class="mood-content-intro">
                                        <span>気分が<span class="okay-mood"><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></span>時によくタグしていること</span>
                                    </p>
                                    
                                    <div class="mood-associated-list">
                                        <?php
                                        // Check if there are any popular activities associated with the current mood
                                        // If the count of popular activities is greater than 0, proceed to output the data
                                        if ($popularActivitiesCount > 0) {
                                            while ($activityCount = mysqli_fetch_assoc($resultPopularActivities)) {?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the Activity ID for the current activity
                                                    $activityID = $activityCount['ActivityID'];

                                                    // Prepare a query to get detailed information about the activity using its ID
                                                    $queryActivityData = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                    // Execute the query to get the activity details
                                                    $resultActivityData = mysqli_query($con, $queryActivityData);
                                                    // Fetch the associated activity data
                                                    $activityData = mysqli_fetch_assoc($resultActivityData); ?>

                                                    <!-- Display the activity icon -->
                                                    <div class="mood-item-icon"><?php echo $activityData['ActivityIcon']; ?> </div>
                                                    <!-- Display the name of the activity -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($activityData['ActivityName']); ?></p>
                                                    <!-- Display the count of times this activity has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $activityCount['cntTrackAct'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular companies associated with the current mood
                                        // If the count of popular companies is greater than 0, proceed to output the data
                                        if ($popularCompaniesCount > 0) {
                                            while ($companyCount = mysqli_fetch_assoc($resultPopularCompanies)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the CompanyID for the current company
                                                    $companyID = $companyCount['CompanyID'];

                                                    // Prepare a query to get detailed information about the company using its ID
                                                    $queryCompanyData = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                    // Execute the query to get the company details
                                                    $resultCompanyData = mysqli_query($con, $queryCompanyData);
                                                    // Fetch the associated company data
                                                    $companyData = mysqli_fetch_assoc($resultCompanyData); ?>

                                                    <!-- Display the company icon -->
                                                    <div class="mood-item-icon"><?php echo  $companyData['CompanyIcon']; ?> </div>
                                                    <!-- Display the name of the company -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($companyData['CompanyName']); ?></p>
                                                    <!-- Display the count of times this company has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $companyCount['cntTrackCom'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular locations associated with the current mood
                                        // If the count of popular locations is greater than 0, proceed to output the data
                                        if ($popularLocationsCount > 0) {
                                            while ($locationCount = mysqli_fetch_assoc($resultPopularLocations)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the LocationID for the current location
                                                    $locationID = $locationCount['LocationID'];

                                                    // Prepare a query to get detailed information about the location using its ID
                                                    $queryLocationData = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                    // Execute the query to get the location details
                                                    $resultLocationData = mysqli_query($con, $queryLocationData);
                                                    // Fetch the associated location data
                                                    $locationData = mysqli_fetch_assoc($resultLocationData); ?>

                                                    <!-- Display the location icon -->
                                                    <div class="mood-item-icon"><?php echo $locationData['LocationIcon']; ?> </div>
                                                    <!-- Display the name of the location -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($locationData['LocationName']);  ?></p>
                                                    <!-- Display the count of times this location has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $locationCount['cntTrackLoc']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }
                                
                                        // Check if there are any popular food associated with the current mood
                                        // If the count of popular food is greater than 0, proceed to output the data
                                        if ($popularFoodCount > 0) {
                                            while ($foodCount = mysqli_fetch_assoc($resultPopularFood)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the FoodID for the current food
                                                    $foodID = $foodCount['FoodID'];

                                                    // Prepare a query to get detailed information about the food using its ID
                                                    $queryFoodData = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                    // Execute the query to get the food details
                                                    $resultFoodData = mysqli_query($con, $queryFoodData);
                                                    // Fetch the associated food data
                                                    $foodData = mysqli_fetch_assoc($resultFoodData); ?>

                                                    <!-- Display the food icon -->
                                                    <div class="mood-item-icon"><?php echo $foodData['FoodIcon']; ?> </div>
                                                    <!-- Display the name of the food -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($foodData['FoodName']); ?></p>
                                                    <!-- Display the count of times this food has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $foodCount['cntTrackFood']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular weather associated with the current mood
                                        // If the count of popular weather is greater than 0, proceed to output the data
                                        if ($popularWeatherCount > 0) {
                                            while ($weatherCount = mysqli_fetch_assoc($resultPopularWeather)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the WeatherID for the current weather
                                                    $weatherID = $weatherCount['WeatherID'];

                                                    // Prepare a query to get detailed information about the weather using its ID
                                                    $queryWeatherData = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                    // Execute the query to get the weather details
                                                    $resultWeatherData = mysqli_query($con, $queryWeatherData);
                                                    // Fetch the associated weather data
                                                    $weatherData = mysqli_fetch_assoc($resultWeatherData); ?>

                                                        <!-- Display the weather icon -->
                                                    <div class="mood-item-icon"><?php echo $weatherData['WeatherIcon']; ?> </div>
                                                    <!-- Display the name of the weather -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($weatherData['WeatherName']); ?></p>
                                                    <!-- Display the count of times this weather has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $weatherCount['cntTrackWea']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        } ?>

                                    </div><!-- .mood-associated-list -->
                                </div><!-- . mood-content-section -->
                            </div><!-- . mood-content-section-wrap -->

                        <?php } else { ?>  

                        <!-- Container for displaying a message when no activities are associated with the current mood -->
                        <div class=" mood-content-section-wrap-empty">
                            <div class="no-mood-records-wrapper">
                                <!-- Text indicating the need for more tags to gain insights about behavior patterns -->
                                <p>気分が<b><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>
                            </div><!-- .no-mood-records-wrapper -->
                        </div><!-- . mood-content-section-wrap-empty -->

                        <?php } ?>
                    </section> <!-- #okay-mood -->

                    <section id="bad-mood" data-tab-content>
                        <?php 
                        // Fetch mood details FROM the database query result
                        $moodData = mysqli_fetch_assoc($resultGetMoods);

                        // ID representing the selected mood
                        $moodID = MOOD_BAD;
                  
                        // Query to retrieve activities that are commonly tracked with the specified bad mood
                        // The query filters activities associated with this mood based on the user ID and activity count threshold
                        $queryPopularActivities = 
                            "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                            FROM trackactivities 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY ActivityID 
                            HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular activities
                        $resultPopularActivities = mysqli_query($con, $queryPopularActivities); 
                        // Count the number of activities found
                        $popularActivitiesCount = mysqli_num_rows($resultPopularActivities); 

                        // Query to retrieve data on companies associated with the specified bad mood
                        $queryPopularCompanies = 
                            "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                            FROM trackcompany 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY CompanyID 
                            HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";       
                        // Execute the query and retrieve the result set for popular companies
                        $resultPopularCompanies = mysqli_query($con, $queryPopularCompanies);
                        // Count the number of companies found
                        $popularCompaniesCount = mysqli_num_rows($resultPopularCompanies);


                        // Query to retrieve data on locations associated with the specified bad mood
                        $queryPopularLocations = 
                            "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                            FROM tracklocations 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY LocationID 
                            HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular locations
                        $resultPopularLocations = mysqli_query($con, $queryPopularLocations);
                        // Count the number of locations found
                        $popularLocationsCount = mysqli_num_rows($resultPopularLocations); 

                        // Query to retrieve data on foods associated with the specified bad mood
                        $queryPopularFood = 
                            "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                            FROM trackfoods 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY FoodID 
                            HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular food
                        $resultPopularFood = mysqli_query($con, $queryPopularFood);
                        // Count the number of foods found
                        $popularFoodCount = mysqli_num_rows($resultPopularFood); 

                        // Query to retrieve data on weather associated with the specified bad mood
                        $queryPopularWeather = 
                            "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                            FROM trackweather 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY WeatherID 
                            HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular weather
                        $resultPopularWeather= mysqli_query($con, $queryPopularWeather);
                        // Count the number of weathers found
                        $popularWeatherCount = mysqli_num_rows($resultPopularWeather);  


                        // Calculate the total number of tracked entities associated with the specified bad mood
                        $totalTrackedEntities = $popularActivitiesCount + $popularCompaniesCount + $popularLocationsCount + $popularFoodCount + $popularWeatherCount; // Sum counts of activities, companies, locations, foods, and weather for the total

                        // Check if there are any tracked entities associated with the specified mood.
                        // This condition ensures that we only display the following content if there are
                        // activities, companies, locations, foods, or weather records that match the user's mood.
                        // If no tracked entities exist for the current mood, the content below will not be rendered.
                        if ($totalTrackedEntities > 0) { ?> 
                            <div class="mood-<?php echo $mood; ?>">
                                <div class=" mood-content-section">

                                    <!-- Display a message indicating the activities associated with the current mood -->
                                    <p class="mood-content-intro">
                                        <span>気分が<span class="bad-mood"><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></span>時によくタグしていること</span>
                                    </p>

                                    <div class="mood-associated-list">
                                        <?php
                                        // Check if there are any popular activities associated with the current mood
                                        // If the count of popular activities is greater than 0, proceed to output the data
                                        if ($popularActivitiesCount > 0) {
                                            while ($activityCount = mysqli_fetch_assoc($resultPopularActivities)) {?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the Activity ID for the current activity
                                                    $activityID = $activityCount['ActivityID'];

                                                    // Prepare a query to get detailed information about the activity using its ID
                                                    $queryActivityData = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                    // Execute the query to get the activity details
                                                    $resultActivityData = mysqli_query($con, $queryActivityData);
                                                    // Fetch the associated activity data
                                                    $activityData = mysqli_fetch_assoc($resultActivityData); ?>

                                                    <!-- Display the activity icon -->
                                                    <div class="mood-item-icon"><?php echo $activityData['ActivityIcon']; ?> </div>
                                                    <!-- Display the name of the activity -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($activityData['ActivityName']); ?></p>
                                                    <!-- Display the count of times this activity has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $activityCount['cntTrackAct'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular companies associated with the current mood
                                        // If the count of popular companies is greater than 0, proceed to output the data
                                        if ($popularCompaniesCount > 0) {
                                            while ($companyCount = mysqli_fetch_assoc($resultPopularCompanies)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the CompanyID for the current company
                                                    $companyID = $companyCount['CompanyID'];

                                                    // Prepare a query to get detailed information about the company using its ID
                                                    $queryCompanyData = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                    // Execute the query to get the company details
                                                    $resultCompanyData = mysqli_query($con, $queryCompanyData);
                                                    // Fetch the associated company data
                                                    $companyData = mysqli_fetch_assoc($resultCompanyData); ?>

                                                    <!-- Display the company icon -->
                                                    <div class="mood-item-icon"><?php echo  $companyData['CompanyIcon']; ?> </div>
                                                    <!-- Display the name of the company -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($companyData['CompanyName']); ?></p>
                                                    <!-- Display the count of times this company has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $companyCount['cntTrackCom'];?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular locations associated with the current mood
                                        // If the count of popular locations is greater than 0, proceed to output the data
                                        if ($popularLocationsCount > 0) {
                                            while ($locationCount = mysqli_fetch_assoc($resultPopularLocations)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the LocationID for the current location
                                                    $locationID = $locationCount['LocationID'];

                                                    // Prepare a query to get detailed information about the location using its ID
                                                    $queryLocationData = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                    // Execute the query to get the location details
                                                    $resultLocationData = mysqli_query($con, $queryLocationData);
                                                    // Fetch the associated location data
                                                    $locationData = mysqli_fetch_assoc($resultLocationData); ?>

                                                    <!-- Display the location icon -->
                                                    <div class="mood-item-icon"><?php echo $locationData['LocationIcon']; ?> </div>
                                                    <!-- Display the name of the location -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($locationData['LocationName']);  ?></p>
                                                    <!-- Display the count of times this location has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $locationCount['cntTrackLoc']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }
                                
                                        // Check if there are any popular food associated with the current mood
                                        // If the count of popular food is greater than 0, proceed to output the data
                                        if ($popularFoodCount > 0) {
                                            while ($foodCount = mysqli_fetch_assoc($resultPopularFood)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the FoodID for the current food
                                                    $foodID = $foodCount['FoodID'];

                                                    // Prepare a query to get detailed information about the food using its ID
                                                    $queryFoodData = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                    // Execute the query to get the food details
                                                    $resultFoodData = mysqli_query($con, $queryFoodData);
                                                    // Fetch the associated food data
                                                    $foodData = mysqli_fetch_assoc($resultFoodData); ?>

                                                    <!-- Display the food icon -->
                                                    <div class="mood-item-icon"><?php echo $foodData['FoodIcon']; ?> </div>
                                                    <!-- Display the name of the food -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($foodData['FoodName']); ?></p>
                                                    <!-- Display the count of times this food has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $foodCount['cntTrackFood']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        }

                                        // Check if there are any popular weather associated with the current mood
                                        // If the count of popular weather is greater than 0, proceed to output the data
                                        if ($popularWeatherCount > 0) {
                                            while ($weatherCount = mysqli_fetch_assoc($resultPopularWeather)) { ?>
                                                <div class="mood-associated-item">
                                                    <?php
                                                    // Retrieve the WeatherID for the current weather
                                                    $weatherID = $weatherCount['WeatherID'];

                                                    // Prepare a query to get detailed information about the weather using its ID
                                                    $queryWeatherData = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                    // Execute the query to get the weather details
                                                    $resultWeatherData = mysqli_query($con, $queryWeatherData);
                                                    // Fetch the associated weather data
                                                    $weatherData = mysqli_fetch_assoc($resultWeatherData); ?>

                                                        <!-- Display the weather icon -->
                                                    <div class="mood-item-icon"><?php echo $weatherData['WeatherIcon']; ?> </div>
                                                    <!-- Display the name of the weather -->
                                                    <p class="mood-item-name"><?php echo htmlspecialchars($weatherData['WeatherName']); ?></p>
                                                    <!-- Display the count of times this weather has been tracked for the specified mood -->
                                                    <div class="mood-item-count"><?php echo $weatherCount['cntTrackWea']; ?> </div>
                                                </div>
                                            <?php 
                                            }
                                        } ?>

                                    </div><!-- .mood-associated-list -->
                                </div><!-- . mood-content-section -->
                            </div><!-- . mood-content-section-wrap -->

                        <?php } else { ?>  

                        <!-- Container for displaying a message when no activities are associated with the current mood -->
                        <div class=" mood-content-section-wrap-empty">
                            <!-- Text indicating the need for more tags to gain insights about behavior patterns -->
                            <div class="no-mood-records-wrapper">
                                <p>気分が<b><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>
                            </div><!-- .no-mood-records-wrapper -->
                        </div><!-- . mood-content-section-wrap-empty -->

                        <?php } ?>
                    </section> <!-- #bad-mood -->

                    <section id="awful-mood" data-tab-content>
                        <?php 
                        // Fetch mood details FROM the database query result
                        $moodData = mysqli_fetch_assoc($resultGetMoods);

                        // ID representing the selected mood 
                        $moodID = MOOD_AWFUL;
                      
                        // Query to retrieve activities that are commonly tracked with the specified awful mood
                        // The query filters activities associated with this mood based on the user ID and activity count threshold
                        $queryPopularActivities = 
                            "SELECT ActivityID, TrackingID, count(ActivityID) AS cntTrackAct 
                            FROM trackactivities 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY ActivityID 
                            HAVING count(ActivityID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular activities
                        $resultPopularActivities = mysqli_query($con, $queryPopularActivities); 
                        // Count the number of activities found
                        $popularActivitiesCount = mysqli_num_rows($resultPopularActivities); 

                        // Query to retrieve data on companies associated with the specified afwul mood
                        $queryPopularCompanies = 
                            "SELECT CompanyID, TrackingID, count(CompanyID) AS cntTrackCom 
                            FROM trackcompany 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY CompanyID 
                            HAVING count(CompanyID) > '" . MINIMUM_TRACK_COUNT . "'";       
                        // Execute the query and retrieve the result set for popular companies
                        $resultPopularCompanies = mysqli_query($con, $queryPopularCompanies);
                        // Count the number of companies found
                        $popularCompaniesCount = mysqli_num_rows($resultPopularCompanies);


                        // Query to retrieve data on locations associated with the specified awful mood
                        $queryPopularLocations = 
                            "SELECT LocationID, TrackingID, count(LocationID) AS cntTrackLoc 
                            FROM tracklocations 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY LocationID 
                            HAVING count(LocationID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular locations
                        $resultPopularLocations = mysqli_query($con, $queryPopularLocations);
                        // Count the number of locations found
                        $popularLocationsCount = mysqli_num_rows($resultPopularLocations); 

                        // Query to retrieve data on foods associated with the specified awful mood
                        $queryPopularFood = 
                            "SELECT FoodID, TrackingID, count(FoodID) AS cntTrackFood 
                            FROM trackfoods 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY FoodID 
                            HAVING count(FoodID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular food
                        $resultPopularFood = mysqli_query($con, $queryPopularFood);
                        // Count the number of foods found
                        $popularFoodCount = mysqli_num_rows($resultPopularFood); 

                        // Query to retrieve data on weather associated with the specified awful mood
                        $queryPopularWeather = 
                            "SELECT WeatherID, TrackingID, count(WeatherID) AS cntTrackWea 
                            FROM trackweather 
                            WHERE TrackingID IN (
                                SELECT TrackingID FROM dailytracking WHERE UserID = $userID 
                                AND TrackingID IN (
                                    SELECT TrackingID FROM trackmoods WHERE MoodID = $moodID
                                )
                            ) 
                            GROUP BY WeatherID 
                            HAVING count(WeatherID) > '" . MINIMUM_TRACK_COUNT . "'";
                        // Execute the query and retrieve the result set for popular weather
                        $resultPopularWeather= mysqli_query($con, $queryPopularWeather);
                        // Count the number of weathers found
                        $popularWeatherCount = mysqli_num_rows($resultPopularWeather); 

                        // Calculate the total number of tracked entities associated with the specified awful mood
                        $totalTrackedEntities = $popularActivitiesCount + $popularCompaniesCount + $popularLocationsCount + $popularFoodCount + $popularWeatherCount; // Sum counts of activities, companies, locations, foods, and weather for the total

                        // Check if there are any tracked entities associated with the specified mood.
                        // This condition ensures that we only display the following content if there are
                        // activities, companies, locations, foods, or weather records that match the user's mood.
                        // If no tracked entities exist for the current mood, the content below will not be rendered.
                        if ($totalTrackedEntities > 0) { ?> 
                        <div class="mood-<?php echo $mood; ?>">
                            <div class=" mood-content-section">

                                <!-- Display a message indicating the activities associated with the current mood -->
                                <p class="mood-content-intro">
                                    <span>気分が<span class="awful-mood"><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></span>時によくタグしていること</span>
                                </p>

                                <div class="mood-associated-list">
                                    <?php
                                    // Check if there are any popular activities associated with the current mood
                                    // If the count of popular activities is greater than 0, proceed to output the data
                                    if ($popularActivitiesCount > 0) {
                                        while ($activityCount = mysqli_fetch_assoc($resultPopularActivities)) {?>
                                            <div class="mood-associated-item">
                                                <?php
                                                // Retrieve the Activity ID for the current activity
                                                $activityID = $activityCount['ActivityID'];

                                                // Prepare a query to get detailed information about the activity using its ID
                                                $queryActivityData = "SELECT * FROM activities WHERE ActivityID = $activityID";
                                                // Execute the query to get the activity details
                                                $resultActivityData = mysqli_query($con, $queryActivityData);
                                                // Fetch the associated activity data
                                                $activityData = mysqli_fetch_assoc($resultActivityData); ?>

                                                <!-- Display the activity icon -->
                                                <div class="mood-item-icon"><?php echo $activityData['ActivityIcon']; ?> </div>
                                                <!-- Display the name of the activity -->
                                                <p class="mood-item-name"><?php echo htmlspecialchars($activityData['ActivityName']); ?></p>
                                                <!-- Display the count of times this activity has been tracked for the specified mood -->
                                                <div class="mood-item-count"><?php echo $activityCount['cntTrackAct'];?> </div>
                                            </div>
                                        <?php 
                                        }
                                    }

                                    // Check if there are any popular companies associated with the current mood
                                    // If the count of popular companies is greater than 0, proceed to output the data
                                    if ($popularCompaniesCount > 0) {
                                        while ($companyCount = mysqli_fetch_assoc($resultPopularCompanies)) { ?>
                                            <div class="mood-associated-item">
                                                <?php
                                                // Retrieve the CompanyID for the current company
                                                $companyID = $companyCount['CompanyID'];

                                                // Prepare a query to get detailed information about the company using its ID
                                                $queryCompanyData = "SELECT * FROM company WHERE CompanyID = $companyID";
                                                // Execute the query to get the company details
                                                $resultCompanyData = mysqli_query($con, $queryCompanyData);
                                                // Fetch the associated company data
                                                $companyData = mysqli_fetch_assoc($resultCompanyData); ?>

                                                <!-- Display the company icon -->
                                                <div class="mood-item-icon"><?php echo  $companyData['CompanyIcon']; ?> </div>
                                                <!-- Display the name of the company -->
                                                <p class="mood-item-name"><?php echo htmlspecialchars($companyData['CompanyName']); ?></p>
                                                <!-- Display the count of times this company has been tracked for the specified mood -->
                                                <div class="mood-item-count"><?php echo $companyCount['cntTrackCom'];?> </div>
                                            </div>
                                        <?php 
                                        }
                                    }

                                    // Check if there are any popular locations associated with the current mood
                                    // If the count of popular locations is greater than 0, proceed to output the data
                                    if ($popularLocationsCount > 0) {
                                        while ($locationCount = mysqli_fetch_assoc($resultPopularLocations)) { ?>
                                            <div class="mood-associated-item">
                                                <?php
                                                // Retrieve the LocationID for the current location
                                                $locationID = $locationCount['LocationID'];

                                                // Prepare a query to get detailed information about the location using its ID
                                                $queryLocationData = "SELECT * FROM locations WHERE LocationID = $locationID";
                                                // Execute the query to get the location details
                                                $resultLocationData = mysqli_query($con, $queryLocationData);
                                                // Fetch the associated location data
                                                $locationData = mysqli_fetch_assoc($resultLocationData); ?>

                                                <!-- Display the location icon -->
                                                <div class="mood-item-icon"><?php echo $locationData['LocationIcon']; ?> </div>
                                                <!-- Display the name of the location -->
                                                <p class="mood-item-name"><?php echo htmlspecialchars($locationData['LocationName']);  ?></p>
                                                <!-- Display the count of times this location has been tracked for the specified mood -->
                                                <div class="mood-item-count"><?php echo $locationCount['cntTrackLoc']; ?> </div>
                                            </div>
                                        <?php 
                                        }
                                    }
                            
                                    // Check if there are any popular food associated with the current mood
                                    // If the count of popular food is greater than 0, proceed to output the data
                                    if ($popularFoodCount > 0) {
                                        while ($foodCount = mysqli_fetch_assoc($resultPopularFood)) { ?>
                                            <div class="mood-associated-item">
                                                <?php
                                                // Retrieve the FoodID for the current food
                                                $foodID = $foodCount['FoodID'];

                                                // Prepare a query to get detailed information about the food using its ID
                                                $queryFoodData = "SELECT * FROM foods WHERE FoodID = $foodID";
                                                // Execute the query to get the food details
                                                $resultFoodData = mysqli_query($con, $queryFoodData);
                                                // Fetch the associated food data
                                                $foodData = mysqli_fetch_assoc($resultFoodData); ?>

                                                <!-- Display the food icon -->
                                                <div class="mood-item-icon"><?php echo $foodData['FoodIcon']; ?> </div>
                                                <!-- Display the name of the food -->
                                                <p class="mood-item-name"><?php echo htmlspecialchars($foodData['FoodName']); ?></p>
                                                <!-- Display the count of times this food has been tracked for the specified mood -->
                                                <div class="mood-item-count"><?php echo $foodCount['cntTrackFood']; ?> </div>
                                            </div>
                                        <?php 
                                        }
                                    }

                                    // Check if there are any popular weather associated with the current mood
                                    // If the count of popular weather is greater than 0, proceed to output the data
                                    if ($popularWeatherCount > 0) {
                                        while ($weatherCount = mysqli_fetch_assoc($resultPopularWeather)) { ?>
                                            <div class="mood-associated-item">
                                                <?php
                                                // Retrieve the WeatherID for the current weather
                                                $weatherID = $weatherCount['WeatherID'];

                                                // Prepare a query to get detailed information about the weather using its ID
                                                $queryWeatherData = "SELECT * FROM weather WHERE WeatherID = $weatherID";
                                                // Execute the query to get the weather details
                                                $resultWeatherData = mysqli_query($con, $queryWeatherData);
                                                // Fetch the associated weather data
                                                $weatherData = mysqli_fetch_assoc($resultWeatherData); ?>

                                                    <!-- Display the weather icon -->
                                                <div class="mood-item-icon"><?php echo $weatherData['WeatherIcon']; ?> </div>
                                                <!-- Display the name of the weather -->
                                                <p class="mood-item-name"><?php echo htmlspecialchars($weatherData['WeatherName']); ?></p>
                                                <!-- Display the count of times this weather has been tracked for the specified mood -->
                                                <div class="mood-item-count"><?php echo $weatherCount['cntTrackWea']; ?> </div>
                                            </div>
                                        <?php 
                                        }
                                    } ?>

                                </div><!-- .mood-associated-list -->

                            </div><!-- . mood-content-section -->
                        </div><!-- . mood-content-section-wrap -->

                        <?php } else { ?>  

                        <!-- Container for displaying a message when no activities are associated with the current mood -->
                        <div class=" mood-content-section-wrap-empty">
                            <!-- Text indicating the need for more tags to gain insights about behavior patterns -->
                            <p>気分が<b><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></b>ときに、よりタグを追加して自分の行動パターンについての洞察を得る。</p>
                        </div><!-- . mood-content-section-wrap-empty -->

                        <?php } ?>
                    </section> <!-- #awful-mood -->

                </div><!-- . mood-content-sections -->
            </section><!-- .mood-associated-listper -->

        </div><!-- .main-wrapper -->
    </body>

</html>