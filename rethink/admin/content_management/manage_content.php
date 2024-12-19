<?php
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

// Retrieve all moods
$moodsQuery =  mysqli_query($con, "SELECT * from moods");
$totalMoods = mysqli_num_rows($moodsQuery);

// Retrieve all feelings
$feelingsQuery =  mysqli_query($con, "SELECT * from feelings");
$totalFeelings = mysqli_num_rows($feelingsQuery);

// Retrieve all activities
$activitiesQuery =  mysqli_query($con, "SELECT * from activities");
$totalActivities = mysqli_num_rows($activitiesQuery);

// Retrieve all people (company)
$companyQuery =  mysqli_query($con, "SELECT * from company");
$totalCompany = mysqli_num_rows($companyQuery);

// Retrieve all locations
$locationsQuery =  mysqli_query($con, "SELECT * from locations");
$totalLocations = mysqli_num_rows($locationsQuery);

// Retrieve all food items
$foodsQuery =  mysqli_query($con, "SELECT * from foods");
$totalFoods = mysqli_num_rows($foodsQuery);

// Retrieve all weather conditions
$weatherQuery =  mysqli_query($con, "SELECT * from weather");
$totalWeather = mysqli_num_rows($weatherQuery);

// Retrieve all goals, ordered by category
$goalsQuery =  mysqli_query($con, "SELECT * from goals order by GoalCategoriesID");
$totalGoals = mysqli_num_rows($goalsQuery);

// Retrieve all goal categories
$goalCategoriesQuery =  mysqli_query($con, "SELECT * from goalcategories");
$totalGoalCategories = mysqli_num_rows($goalCategoriesQuery);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Include the necessary assets for the head section via the includeHeadAssets function -->
        <?php includeHeadAssets(); ?>
        <script src="../../assets/javascript/tab_interactions.js" defer></script>
    </head>

    <body>
        <header class="sidebar-navigation manage-contents-navigation">
            <!-- Render the sidebar navigation specifically for the admin section -->
            <?php renderAdminNavigation(); ?>
        </header>

        <!-- Render the header for the admin dashboard with logout functionality, using the admin's data -->
        <?php renderAdminHeaderWithLogout($adminData); ?>
        
        <!-- Main wrapper for the content management section -->
        <div class="admin-main-wrapper">
            <section class="content-management-navigation-wrapper">
                <!-- Header for the Manage Content section -->
                <h2>Manage Content</h2>

                <!-- Navigation for editing different content categories -->
                <div class="content-management-navigation">
                    <ul>
                        <!-- Tab for managing 'Moods' content, initially active -->
                        <li data-tab-target="#moodsData" class="active tabs">
                            <div class="navigation-content" id="active-navigation">
                                <p>
                                    <!-- Japanese label for 'Moods' -->
                                    <span class="japanese-label">気分</span>
                                    <!-- English label for 'Moods' -->
                                    <span>Moods</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'Feelings' content -->
                        <li data-tab-target="#feelingsData" class = "tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'Feelings' -->
                                    <span class="japanese-label">気持ち</span>
                                    <!-- English label for 'Feelings' -->
                                    <span>Feelings</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'Activities' content -->
                        <li data-tab-target="#activityData" class="tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'Activities' -->
                                    <span class="japanese-label">アクティビティ</span>
                                    <!-- English label for 'Activities' -->
                                    <span>Activities</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'People' content -->
                        <li data-tab-target="#socialData" class="tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'People' -->
                                    <span class="japanese-label">人々</span>
                                    <!-- English label for 'People' -->
                                    <span>People</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'Locations' content -->
                        <li data-tab-target="#locationsData" class="tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'Locations' -->
                                    <span class="japanese-label">場所</span>
                                    <!-- English label for 'Locations' -->
                                    <span>Locations</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'Food' content -->
                        <li data-tab-target="#foodsData" class="tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'Food' -->
                                    <span class="japanese-label">食事</span>
                                    <!-- English label for 'Food' -->
                                    <span>Food</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'Weather' content -->
                        <li data-tab-target="#weatherData" class="tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'Weather' -->
                                    <span class="japanese-label">天候</span>
                                    <!-- English label for 'Weather' -->
                                    <span>Weather</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'Goals' content -->
                        <li data-tab-target="#goalsData" class="tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'Goals' -->
                                    <span class="japanese-label">目標</span>
                                    <!-- English label for 'Goals' -->
                                    <span>Goals</span>
                                </p>
                            </div> 
                        </li>

                        <!-- Tab for managing 'Goal Category' content -->
                        <li data-tab-target="#categoryData" class="tabs">
                            <div class="navigation-content">
                                <p>
                                    <!-- Japanese label for 'Goal Category' -->
                                    <span class="japanese-label">目標カテゴリー</span>
                                    <!-- English label for 'Goal Category' -->
                                    <span>Goal Category</span>
                                </p>
                            </div> 
                        </li>
                    </ul>
                </div><!-- End of .content-management-navigation -->

            </section><!-- End of .content-management-navigation-wrapper -->

            <!-- Wrapper for the main content editing section -->
            <section class="content-management-wrapper">
                <!-- Display feedback messages, included from a shared PHP file -->
                <div>
                    <?php include('../../server-side/shared/feedback_messages.php'); ?>
                </div>
                
                 <!-- Section for editing 'Moods' data, initially active -->
                <div id="moodsData" data-tab-content class="active">
                    <div class="content-management-table">
                        <!-- Header section displaying the title in Japanese and English -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Japanese title for 'Moods' -->
                                <span class="japanese-title">気分</span>
                                <!-- English title for 'Moods' -->
                                <span class="english-title">Moods</span>
                            </h3>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying mood data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the moods data -->
                                <th>No</th>
                                <th>English Name</th>
                                <th>Japanese Name</th>
                                <th>Icon</th>
                                <th>Action</th>
                            </tr>

                            <?php 
                            // Check if there are any moods to display
                            if ($totalMoods > 0) {
                                // Loop through each mood in the query result
                                while ($moodData = mysqli_fetch_assoc($moodsQuery)) { 
                                    // Store the mood ID for easier access
                                    $moodID = $moodData['MoodID']; ?>

                                    <!-- Display a new row for each mood in the table -->
                                    <tr> 
                                        <!-- Display the mood ID -->
                                        <td><?php echo $moodData['MoodID']; ?></td>

                                        <!-- Display the English name of the mood, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($moodData['MoodName']); ?></td>

                                        <!-- Display the Japanese name of the mood, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($moodData['JapaneseMoodName']); ?></td>

                                        <!-- Display the emoji associated with the mood -->
                                        <td><?php echo $moodData['moodEmoji']; ?></td>
                                        
                                        <!-- Actions column with buttons for editing and deleting moods -->
                                        <td>
                                            <!-- Edit button linking to the mood edit page with the mood ID as a parameter -->
                                            <div class="action-buttons-wrapper">
                                                <div class="button-edit">
                                                    <!-- Link to the mood edit page, with the mood's ID passed as a URL parameter -->
                                                    <a href="./edit_content/edit_mood.php?id=<?php echo $moodData['MoodID']; ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> <!-- Icon representing the edit action -->
                                                    </a>
                                                </div><!-- End of .button-edit-wrapper -->
                                            </div><!-- End of .action-buttons-wrapper -->    
                                        </td>
                                    </tr>
                                <?php }
                            } ?>

                        </table>
                    </div><!-- End of .content-management-table  -->
                </div> <!-- #moodsData -->

                <!-- Feelings data tab content section -->
                <div id="feelingsData" data-tab-content>
                    <div class="content-management-table">

                        <!-- Top section with title and add button for feelings -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Title in Japanese for 'Feelings' -->
                                <span class="japanese-title">気持ち</span>
                                <!-- Title in English for 'Feelings' -->
                                <span class="english-title">Feelings</span>
                            </h3>

                             <!-- Button to add a new feeling, linking to the add feeling page -->
                            <a href="./add_content/add_feeling.php" class="primary-btn">
                                <!-- Icon representing the add action -->
                                <i class="fa-solid fa-plus"></i>
                                Add New
                            </a>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying feeling data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the feeling data -->
                                <th>No</th>
                                <th>Name</th>
                                <th>Loading</th>
                                <th>Action</th>
                            </tr>

                            <?php 
                            // Initialize counter for table row numbering
                            $rowIndex = 0; 

                            // Check if there are any feelings to display
                            if ($totalFeelings > 0) {
                                // Loop through each feeling record from the database query
                                while ($feelingData = mysqli_fetch_assoc($feelingsQuery)) { 
                                    // Store the feeling ID for easy access
                                    $FeelingID = $feelingData['FeelingID']; 
                                    // Retrieve the associated loading ID for the current feeling
                                    $feelingLoadingID = $feelingData['FeelingLoadingID'];

                                    // Query the database for the loading details related to the feeling
                                    $queryFeelingLoading = mysqli_query($con, "SELECT * FROM feelingloadings WHERE FeelingLoadingID = $feelingLoadingID");
                                    $resultFeelingLoading = mysqli_fetch_assoc($queryFeelingLoading);

                                    // Retrieve the associated feeling loading name for the current feeling
                                    $feelingLoading = $resultFeelingLoading['FeelingLoading'];

                                    // Increment row index for each new row
                                    $rowIndex++; 
                                    ?>

                                    <!-- Table row for displaying a single feeling record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the name of the feeling, ensuring special characters are escaped -->
                                        <td><?php echo htmlspecialchars($feelingData['FeelingName']); ?></td>

                                        <!-- Display the associated loading description for the feeling -->
                                        <td><?php echo $feelingLoading; ?></td>

                                        <!-- Action buttons for editing or deleting the feeling record -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <!-- Edit button linking to the feeling edit page with the feeling ID as a parameter -->
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_feeling.php?id=<?php echo $feelingData['FeelingID']; ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>  <!-- Icon for editing the feeling -->
                                                    </a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the feeling record -->
                                                <div class="button-delete">
                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling feeling deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a feeling -->
                                                            <button type="submit" name="feeling-delete" value="<?=$feelingData['FeelingID'];?>">
                                                                <i class="fa-solid fa-trash"></i>  <!-- Icon for deleting the feeling -->
                                                            </button>
                                                        </form>
                                                    </a>
                                                </div><!-- End of .button-delete -->

                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </td>

                                    </tr>
                                <?php 
                                } // End of while loop
                            }  // End of if statement 
                            ?>
                        </table>
                    </div><!-- End of .content-management-table  -->
                </div><!-- #feelingsData -->

                <!-- Activities data tab content section -->
                <div id="activityData" data-tab-content>
                        <div class="content-management-table">

                            <!-- Top section with title and add button for activities -->
                            <div class="content-management-header">
                                <h3>
                                    <!-- Japanese title for 'Activities' -->
                                    <span class="japanese-title">アクティビティ</span>
                                    <!-- English title for 'Activities' -->
                                    <span class="english-title">Activities</span>
                                </h3>

                                <!-- Button to navigate to the page for adding a new activity -->
                                <a href="./add_content/add_activity.php" class="primary-btn">
                                    <!-- Icon representing the add action -->
                                    <i class="fa-solid fa-plus"></i>
                                    Add New
                                </a>
                            </div><!-- End of .content-management-header -->

                            <!-- Table displaying activity data with columns for various attributes -->
                            <table>
                                <tr>
                                    <!-- Column headers for each attribute of the activity data -->
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Icon</th>
                                    <th>Action</th>
                                </tr>

                                <?php 
                                // Initialize counter for table row numbering
                                $rowIndex = 0; 
    
                                // Check if there are any activities to display
                                if ($totalActivities > 0) {
                                    // Loop through each activity record from the database query
                                    while ($activityData = mysqli_fetch_assoc($activitiesQuery)) { 
                                    // Store the activity ID for easy access
                                    $activityID = $activityData['ActivityID'];

                                    // Increment row index for each new row
                                    $rowIndex++; 
                                    ?>

                                    <!-- Table row for displaying a single activity record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the Japanese name of the activity, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($activityData['ActivityName']); ?></td>

                                        <!-- Display the emoji associated with the activity -->
                                        <td><?php echo $activityData['ActivityIcon']; ?></td>

                                        <!-- Actions column with buttons for editing and deleting activities -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <!-- Edit button linking to the activity edit page with the activity ID as a parameter -->
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_activity.php?id=<?php echo $activityData['ActivityID']; ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> <!-- Icon for editing the activity -->
                                                    </a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the activity record -->
                                                <div class="button-delete">
                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling activity deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a activity -->
                                                            <button type="submit" name="activity-delete" value="<?=$activityData['ActivityID'];?>">
                                                                <i class="fa-solid fa-trash"></i> <!-- Icon representing the action of deleting the activity -->
                                                            </button>
                                                        </form>
                                                        
                                                    </a>
                                                </div><!-- End of .button-delete -->
                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </tr>
                                    <?php 
                                    } // End of while loop
                                } // End of if statement
                                ?>
                            </table>
                        </div><!-- End of .content-management-table  -->
                </div><!-- #activityData -->
                
                <!-- Companies data tab content section -->
                <div id="socialData" data-tab-content>
                    <div class="content-management-table">

                        <!-- Top section with title and add button for companies -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Japanese title for 'People' -->
                                <span class="japanese-title">人々</span>
                                <!-- English title for 'People' -->
                                <span class="english-title">People</span>
                            </h3>

                            <!-- Button to navigate to the page for adding a new company -->
                            <a href="./add_content/add_people.php" class="primary-btn">
                                <!-- Icon representing the add action -->
                                <i class="fa-solid fa-plus"></i>
                                Add New
                            </a>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying company data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the company data -->
                                <th>No</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Action</th>
                            </tr>
                            
                            <?php 
                            // Initialize counter for table row numbering
                            $rowIndex = 0; 

                            // Check if there are any companies to display
                            if ($totalCompany > 0) {
                                // Loop through each company record from the database query
                                while ($companyData= mysqli_fetch_assoc($companyQuery)) { 
                                    // Store the company ID for easy access
                                    $companyID = $companyData['CompanyID']; 

                                    // Increment row index for each new row
                                    $rowIndex++;
                                    ?>
                                    
                                    <!-- Table row for displaying a single company record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the Japanese name of the company, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($companyData['CompanyName']); ?></td>

                                        <!-- Display the emoji associated with the company -->
                                        <td><?php echo $companyData['CompanyIcon']; ?></td>

                                        <!-- Actions column with buttons for editing and deleting companies -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <!-- Edit button linking to the company edit page with the company ID as a parameter -->
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_people.php?id=<?php echo $companyData['CompanyID']; ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> <!-- Icon for editing the company -->
                                                    </a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the company record -->
                                                <div class="button-delete">
                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling company deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a company -->
                                                            <button type="submit" name="company-delete" value="<?=$companyData['CompanyID'];?>">
                                                                <i class="fa-solid fa-trash"></i> <!-- Icon representing the action of deleting the company -->
                                                            </button>
                                                        </form>
                                                    </a>
                                                </div><!-- End of .button-delete -->
                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </td>

                                    </tr>
                                <?php 
                                } // End of while loop
                            } // End of if statement
                            ?>
                        </table>    
                    </div><!-- End of .content-management-table  -->
                </div><!-- #socialData -->
                
                <!-- Locations data tab content section -->
                <div id="locationsData" data-tab-content>
                    <div class="content-management-table">

                        <!-- Top section with title and add button for locations -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Japanese title for 'Locations' -->
                                <span class="japanese-title">場所</span>
                                <!-- English title for 'Locations' -->
                                <span class="english-title">Locations</span>
                            </h3>

                            <!-- Button to navigate to the page for adding a new location -->
                            <a href="./add_content/add_location.php" class="primary-btn">
                                <!-- Icon representing the add action -->
                                <i class="fa-solid fa-plus"></i>
                                Add New
                            </a>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying location data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the location data -->
                                <th>No</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Action</th>
                            </tr>
                            
                            <?php 
                            // Initialize counter for table row numbering
                            $rowIndex = 0; 

                            // Check if there are any locations to display
                            if ($totalLocations > 0) {
                                // Loop through each location record from the database query
                                while ($locationData= mysqli_fetch_assoc($locationsQuery)) { 
                                    // Store the location ID for easy access
                                    $locationID = $locationData['LocationID']; 

                                    // Increment row index for each new row
                                    $rowIndex++;
                                    ?>

                                    <!-- Table row for displaying a single location record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the Japanese name of the location, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($locationData['LocationName']); ?></td>

                                        <!-- Display the emoji associated with the location -->
                                        <td><?php echo $locationData['LocationIcon']; ?></td>

                                        <!-- Actions column with buttons for editing and deleting locations -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <!-- Edit button linking to the location edit page with the location ID as a parameter -->
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_location.php?id=<?php echo $locationData['LocationID']; ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> <!-- Icon for editing the location -->
                                                    </a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the location record -->
                                                <div class="button-delete">
                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling location deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a location -->
                                                            <button type="submit" name="location-delete" value="<?=$locationID;?>">
                                                                <i class="fa-solid fa-trash"></i> <!-- Icon representing the action of deleting the location -->
                                                            </button>
                                                        </form>

                                                    </a>
                                                </div><!-- End of .button-delete -->
                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </td>
                                    </tr>

                                <?php 
                                } // End of while loop
                            } // End of if statement
                            ?>
                        </table>
                    </div><!-- End of .content-management-table  -->
                </div><!-- #locationsData -->
                
                <!-- Food data tab content section -->
                <div id="foodsData" data-tab-content>
                    <div class="content-management-table">

                        <!-- Top section with title and add button for food -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Japanese title for 'Food' -->
                                <span class="japanese-title">食事</span>
                                <!-- English title for 'Food' -->
                                <span class="english-title">Food</span>
                            </h3>

                            <!-- Button to navigate to the page for adding a new food -->
                            <a href="./add_content/add_food.php" class="primary-btn">
                                <!-- Icon representing the add action -->
                                <i class="fa-solid fa-plus"></i>
                                Add New
                            </a>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying food data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the food data -->
                                <th>No</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Action</th>
                            </tr>
                           
                            <?php 
                            // Initialize counter for table row numbering
                            $rowIndex = 0; 

                            // Check if there are any food to display
                            if ($totalFoods > 0) {
                                // Loop through each food record from the database query
                                while ($foodData = mysqli_fetch_assoc($foodsQuery)) { 
                                    // Store the food ID for easy access
                                    $foodID = $foodData['FoodID']; 

                                    // Increment row index for each new row
                                    $rowIndex++;
                                    ?>

                                    <!-- Table row for displaying a single food record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the Japanese name of the food, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($foodData['FoodName']); ?></td>

                                        <!-- Display the emoji associated with the food -->
                                        <td><?php echo $foodData['FoodIcon']; ?></td>

                                        <!-- Actions column with buttons for editing and deleting food -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <!-- Edit button linking to the food edit page with the food ID as a parameter -->
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_food.php?id=<?php echo $foodData['FoodID']; ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i> <!-- Icon for editing the food -->
                                                    </a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the food record -->
                                                <div class="button-delete">
                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling food deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a food -->
                                                            <button type="submit" name="food-delete" value="<?=$foodData['FoodID'];?>">
                                                                <i class="fa-solid fa-trash"></i> <!-- Icon representing the action of deleting the food -->
                                                            </button>
                                                        </form>

                                                    </a>
                                                </div><!-- End of .button-delete -->
                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </td>
                                    </tr>

                                <?php 
                                } // End of while loop
                            } // End of if statement
                            ?>
                        </table>
                    </div><!-- End of .content-management-table  -->
                </div><!-- #foodsData -->
                
                <!-- Weather data tab content section -->
                <div id="weatherData" data-tab-content>
                    <div class="content-management-table">

                        <!-- Top section with title and add button for weather -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Japanese title for 'Weather' -->
                                <span class="japanese-title">天候</span>
                                <!-- English title for 'Weather' -->
                                <span class="english-title">Weather</span>
                            </h3>

                            <!-- Button to navigate to the page for adding a new weather -->
                            <a href="./add_content/add_weather.php" class="primary-btn">
                                <!-- Icon representing the add action -->
                                <i class="fa-solid fa-plus"></i>
                                Add New
                            </a>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying weather data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the weather data -->
                                <th>No</th>
                                <th>Name</th>
                                <th>Icon</th>
                                <th>Action</th>
                            </tr>
                            
                            <?php 
                            // Initialize counter for table row numbering
                            $rowIndex = 0; 
                            
                            // Check if there are any weather to display
                            if ($totalWeather > 0) {
                                // Loop through each weather record from the database query
                                while ($weatherData= mysqli_fetch_assoc($weatherQuery)) { 
                                    // Store the weather ID for easy access
                                    $weatherID = $weatherData['WeatherID'];
                                    
                                    // Increment row index for each new row
                                    $rowIndex++;
                                    ?>

                                    <!-- Table row for displaying a single weather record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the Japanese name of the weather, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($weatherData['WeatherName']); ?></td>

                                        <!-- Display the emoji associated with the weather -->
                                        <td><?php echo $weatherData['WeatherIcon']; ?></td>

                                        <!-- Actions column with buttons for editing and deleting weather -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_weather.php?id=<?php echo $weatherData['WeatherID']; ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the weather record -->
                                                <div class="button-delete">

                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling weather deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a weather -->
                                                            <button type="submit" name="weather-delete" value="<?=$weatherData['WeatherID'];?>">
                                                                <i class="fa-solid fa-trash"></i> <!-- Icon representing the action of deleting the weather -->
                                                            </button>
                                                        </form>
                                                    </a>
                                                </div><!-- End of .button-delete -->
                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </td>
                                    </tr>
                                <?php 
                                } // End of while loop
                            } // End of if statement
                            ?>
                        </table>
                    </div><!-- End of .content-management-table  -->
                </div><!-- #weatherData -->
                
                <!-- Goals data tab content section -->
                <div id="goalsData" data-tab-content>
                    <div class="content-management-table">

                        <!-- Top section with title and add button for goals -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Japanese title for 'Goals' -->
                                <span class="japanese-title">目標</span>
                                <!-- English title for 'Goals' -->
                                <span class="english-title">Goals</span>
                            </h3>

                            <!-- Button to navigate to the page for adding a new goal -->
                            <div class="goal-buttons">
                                <a href="./add_content/add_goal.php" class="primary-btn">
                                    <!-- Icon representing the add action -->
                                    <i class="fa-solid fa-plus"></i>
                                    Add New
                                </a>
                            </div>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying goal data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the goal data -->
                                <th>No</th>
                                <th>Goal</th>
                                <th>Icon</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                            
                            <?php 
                            // Initialize counter for table row numbering
                            $rowIndex = 0; 
                            
                            // Check if there are any goals to display
                            if ($totalGoals > 0) {
                                // Loop through each goal record from the database query
                                while ($goalData = mysqli_fetch_assoc($goalsQuery)) { 
                                    // Store the goal ID for easy access
                                    $goalID = $goalData['GoalID']; 
                                    $goalCategoryID = $goalData['GoalCategoriesID']; 

                                    $queryGetGoalCategory = mysqli_query($con, "SELECT * from goalcategories where GoalCategoriesID = $goalCategoryID");
                                    $resultGetGoalCategory =  mysqli_fetch_assoc($queryGetGoalCategory);
                                    $goalCategoryName = $resultGetGoalCategory['GoalCategoryNameJp'];

                                    // Increment row index for each new row
                                    $rowIndex++;
                                    ?>

                                    <!-- Table row for displaying a single goal record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the Japanese name of the goal, ensuring special characters are escaped -->
                                        <td><?php echo htmlspecialchars($goalData['GoalName']); ?></td>

                                        <!-- Display the emoji associated with the goal -->
                                        <td><?php echo $goalData['GoalIcon']; ?></td>

                                        <!-- Display the Japanese name of the goal category, with special characters converted for safety -->
                                        <td><?php echo $goalCategoryName; ?></td>

                                        <!-- Actions column with buttons for editing and deleting goals -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_goal.php?id=<?php echo $goalData['GoalID']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the goal record -->
                                                <div class="button-delete">
                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling goal deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a goal -->
                                                            <button type="submit" name="goal-delete" value="<?=$goalData['GoalID'];?>">
                                                                <i class="fa-solid fa-trash"></i> <!-- Icon representing the action of deleting the goal -->
                                                            </button>
                                                        </form>
                                                    </a>

                                                </div><!-- End of .button-delete -->
                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </td>
                                    </tr>
                                <?php 
                                } // End of while loop
                            } // End of if statement
                            ?>
                        </table>
                    </div><!-- End of .content-management-table  -->
                </div><!-- #goalsData -->

                <!-- Goal Categories data tab content section -->
                <div id="categoryData" data-tab-content>
                    <div class="content-management-table">

                        <!-- Top section with title and add button for goal categories -->
                        <div class="content-management-header">
                            <h3>
                                <!-- Japanese title for 'Goal Categories' -->
                                <span class="japanese-title">目標カテゴリー</span>
                                <!-- English title for 'Goal Categories' -->
                                <span class="english-title">Goal Categories</span>
                            </h3>

                            <!-- Button to navigate to the page for adding a new goal category -->
                            <div class="goal-buttons">
                                <a href="./add_content/add_goal_category.php" class="primary-btn">
                                    <!-- Icon representing the add action -->
                                    <i class="fa-solid fa-plus"></i>
                                    Add New
                                </a>
                            </div>
                        </div><!-- End of .content-management-header -->

                        <!-- Table displaying goal category data with columns for various attributes -->
                        <table>
                            <tr>
                                <!-- Column headers for each attribute of the goal category data -->
                                <th>No</th>
                                <th>Name English</th>
                                <th>Name Japanese</th>
                                <th>Action</th>
                            </tr>
                            
                            <?php 
                            // Initialize counter for table row numbering
                            $rowIndex = 0; 
                            
                            // Check if there are any goal actegories to display
                            if ($totalGoalCategories > 0) {
                                // Loop through each goal category record from the database query
                                while ($goalCategoryData = mysqli_fetch_assoc($goalCategoriesQuery)) { 
                                    // Store the goal category ID for easy access
                                    $goalCategoryID = $goalCategoryData['GoalCategoriesID']; 
                                    $goalCategoryName = $goalCategoryData['GoalCategoryName']; 
                                    $goalCategoryNameJP = $goalCategoryData['GoalCategoryNameJp']; 

                                    // Increment row index for each new row
                                    $rowIndex++;
                                    ?>

                                    <!-- Table row for displaying a single goal category record -->
                                    <tr> 
                                        <!-- Display the row number -->
                                        <td><?php echo $rowIndex; ?></td>

                                        <!-- Display the English name of the goal category, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($goalCategoryName); ?></td>

                                        <!-- Display the Japanese name of the goal category, with special characters converted for safety -->
                                        <td><?php echo htmlspecialchars($goalCategoryNameJP); ?></td>

                                        <!-- Actions column with buttons for editing and deleting goal categories -->
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <!-- Edit button linking to the goal category edit page with the goal category ID as a parameter -->
                                                <div class="button-edit">
                                                    <a href="./edit_content/edit_goal_category.php?id=<?php echo $goalCategoryData['GoalCategoriesID']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                                </div><!-- End of .button-edit-wrapper -->

                                                <!-- Button for deleting the goal category record -->
                                                <div class="button-delete">
                                                    <a href="deletelink" onclick="return checkDelete()">
                                                        <!-- Form for handling goal category deletion with POST method -->
                                                        <form action="../../server-side/admin/admin_data_cleanup.php" method="POST">
                                                            <!-- Button for deleting a goal category -->
                                                            <button type="submit" name="goalCategory-delete" value="<?=$goalCategoryData['GoalCategoriesID'];?>">
                                                                <i class="fa-solid fa-trash"></i> <!-- Icon representing the action of deleting the goal category -->
                                                            </button>
                                                        </form>
                                                    </a>
                                                    
                                                </div><!-- End of .button-delete -->
                                            </div><!-- End of .action-buttons-wrapper -->  
                                        </td>
                                    </tr>
                                <?php 
                                } // End of while loop
                            } // End of if statement
                            ?>
                        </table>
                    </div><!-- End of .content-management-table  -->
                </div><!-- #goalsData -->
            </section><!-- End of .content-management-wrapper -->
            
        </div><!-- End of .admin-main-wrapper -->  
    </body>


</html>