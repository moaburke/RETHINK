/* ---------- Tabbed Navigation System ---------- */
/**
 * This script manages a tabbed navigation system on the webpage. 
 * It allows users to switch between different content sections by clicking on the corresponding tabs. 
 * When a tab is clicked, the associated content is displayed while hiding all other content sections.
 */

// Select all elements with the 'data-tab-target' attribute
const tabs = document.querySelectorAll('[data-tab-target]')

// Select all elements with the 'data-tab-content' attribute
const tabContents = document.querySelectorAll('[data-tab-content]')

// Add click event listeners to each tab
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        // Get the target content associated with the clicked tab
        const targetContent = document.querySelector(tab.dataset.tabTarget)

        // Hide all tab content by removing the 'active' class
        tabContents.forEach(tabContent => {
            tabContent.classList.remove('active')
        })

        // Remove the 'active' class from all tabs
        tabs.forEach(tab => {
            tab.classList.remove('active')
        })

        // Set the clicked tab as active
        tab.classList.add('active')
        // Show the corresponding content for the active tab
        targetContent.classList.add('active')
    })
})


/* ---------- Tab Hover and Click Interaction for Dynamic Content Update ---------- */
/**
 * This script manages the hover and click interactions for a tabbed interface.
 * When the user hovers over or clicks on a tab (activity, company, location, food, or weather),
 * it updates the associated description text and handles visual feedback for active tabs.
 */

// Get the elements corresponding to each tab
const activityTab = document.getElementById("activityDataID"); // Activity tab
const companyTab = document.getElementById("companyDataID"); // Company tab
const locationTab = document.getElementById("locationsDataID"); // Location tab
const foodTab = document.getElementById("foodDataID"); // Food tab
const weatherTab = document.getElementById("weatherDataID"); // Weather tab
const tabsContainer = document.getElementById("tabs"); // Parent container for the tabs

// Add mouseover event for the activity tab
activityTab.addEventListener("mouseover", () =>{
    // Set the inner text of the activity paragraph to indicate the tab is being hovered over
    document.getElementById("activityDataP").innerHTML = "Activity"; 

    // Determine which tab is currently active and update its corresponding text abbreviation
    if (companyTab.classList.contains('active')) { 
        document.getElementById("companyDataP").innerHTML = "P";  // Set abbreviation for Company
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "L"; // Set abbreviation for Location
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "F";  // Set abbreviation for Food
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "W"; // Set abbreviation for Weather
    }

    // Apply hover effect if the activity tab is not currently active
    if (!activityTab.classList.contains('active')) {
        tabsContainer.classList.add('tab-hover'); // Add hover class to the parent container
    }
});

// Add mouseout event listener for the activity tab
activityTab.addEventListener('mouseout', function(){
    // If the activity tab is active, do nothing
    if (!activityTab.classList.contains('active')) {
        // Set the inner text of the activity paragraph to the abbreviation "A" when the mouse leaves
        document.getElementById("activityDataP").innerHTML = "A";
    }

    // Check which tab is currently active and update its corresponding text appropriately
    if (companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "People"; // Update text for Company tab
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "Location"; // Update text for Location tab
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "Food"; // Update text for Food tab
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "Weather"; // Update text for Weather tab
    }

    // Remove the hover effect from the tabs container when the mouse leaves the activity tab
    document.getElementById("tabs").classList.remove('tab-hover');
})

// Add mouseover event listener for the company tab
companyTab.addEventListener("mouseover", () => {
    // Update the company tab text to indicate it is being hovered over
    document.getElementById("companyDataP").innerHTML = "People";

    // Check which tab is currently active and update its corresponding text abbreviation
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "A"; // Set abbreviation for Activity
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "L"; // Set abbreviation for Location
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "F"; // Set abbreviation for Food
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "W"; // Set abbreviation for Weather
    }

    // Add hover effect if the company tab is not currently active
    if (!companyTab.classList.contains('active')) {
        tabsContainer.classList.add('tab-hover'); // Add hover class to the parent container
    }
});

// Add mouseout event listener for the company tab
companyTab.addEventListener('mouseout', function() {
    // If the company tab is active, do nothing
    if (!companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "P"; // Reset text for Company tab
    }

    // Check which tab is currently active and update its corresponding full text
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "Activity"; // Set full text for Activity
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "Location"; // Set full text for Location
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "Food"; // Set full text for Food
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "Weather"; // Set full text for Weather
    }

    // Remove hover effect from the tabs container
    document.getElementById("tabs").classList.remove('tab-hover'); // Remove hover class
});

// Add mouseover event listener for the location tab
locationTab.addEventListener("mouseover", () => {
    // Update the location tab text to indicate it is being hovered over
    document.getElementById("locationsDataP").innerHTML = "Location";

    // Check which tab is currently active and update its corresponding text abbreviation
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "A"; // Set abbreviation for Activity
    } else if (companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "P"; // Set abbreviation for Company
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "F"; // Set abbreviation for Food
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "W"; // Set abbreviation for Weather
    }

    // Add hover effect if the location tab is not currently active
    if (!locationTab.classList.contains('active')) {
        tabsContainer.classList.add('tab-hover'); // Add hover class to the parent container
    }
});

// Add mouseout event listener for the location tab
locationTab.addEventListener('mouseout', function() {
    // If the location tab is active, do nothing
    if (!locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "L"; // Reset text for Location tab
    }

    // Check which tab is currently active and update its corresponding full text
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "Activity"; // Set full text for Activity
    } else if (companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "People"; // Set full text for Company
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "Food"; // Set full text for Food
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "Weather"; // Set full text for Weather
    }

    // Remove hover effect from the tabs container
    document.getElementById("tabs").classList.remove('tab-hover'); // Remove hover class
});

// Add mouseover event listener for the food tab
foodTab.addEventListener("mouseover", () => {
    // Update the food tab text to indicate it is being hovered over
    document.getElementById("foodDataP").innerHTML = "Food";

    // Check which tab is currently active and update its corresponding text abbreviation
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "A"; // Set abbreviation for Activity
    } else if (companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "P"; // Set abbreviation for Company
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "L"; // Set abbreviation for Location
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "W"; // Set abbreviation for Weather
    }

    // Add hover effect if the food tab is not currently active
    if (!foodTab.classList.contains('active')) {
        tabsContainer.classList.add('tab-hover'); // Add hover class to the parent container
    }
});

// Add mouseout event listener for the food tab
foodTab.addEventListener('mouseout', function() {
    // If the food tab is active, do nothing
    if (!foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "F"; // Reset text for Food tab
    }

    // Check which tab is currently active and update its corresponding full text
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "Activity"; // Set full text for Activity
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "Location"; // Set full text for Location
    } else if (companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "People"; // Set full text for Company
    } else if (weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "Weather"; // Set full text for Weather
    }

    // Remove hover effect from the tabs container
    document.getElementById("tabs").classList.remove('tab-hover'); // Remove hover class
});

// Add mouseover event listener for the weather tab
weatherTab.addEventListener("mouseover", () => {
    // Update the weather tab text to indicate it is being hovered over
    document.getElementById("weatherDataP").innerHTML = "Weather";

    // Check which tab is currently active and update its corresponding text abbreviation
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "A"; // Set abbreviation for Activity
    } else if (companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "P"; // Set abbreviation for Company
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "L"; // Set abbreviation for Location
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "F"; // Set abbreviation for Food
    }

    // Add hover effect if the weather tab is not currently active
    if (!weatherTab.classList.contains('active')) {
        tabsContainer.classList.add('tab-hover'); // Add hover class to the parent container
    }
});

// Add mouseout event listener for the weather tab
weatherTab.addEventListener('mouseout', function() {
    // If the weather tab is active, do nothing
    if (!weatherTab.classList.contains('active')) {
        document.getElementById("weatherDataP").innerHTML = "W"; // Reset text for Weather tab
    }

    // Check which tab is currently active and update its corresponding full text
    if (activityTab.classList.contains('active')) {
        document.getElementById("activityDataP").innerHTML = "Activity"; // Set full text for Activity
    } else if (locationTab.classList.contains('active')) {
        document.getElementById("locationsDataP").innerHTML = "Location"; // Set full text for Location
    } else if (companyTab.classList.contains('active')) {
        document.getElementById("companyDataP").innerHTML = "People"; // Set full text for Company
    } else if (foodTab.classList.contains('active')) {
        document.getElementById("foodDataP").innerHTML = "Food"; // Set full text for Food
    }

    // Remove hover effect from the tabs container
    document.getElementById("tabs").classList.remove('tab-hover'); // Remove hover class
});


// Add click event listener for the locations tab
locationTab.addEventListener("click", () => {
    // Set the text for the Locations tab
    document.getElementById("locationsDataP").innerHTML = "Location";

    // Reset text for other tabs to their respective abbreviations
    document.getElementById("activityDataP").innerHTML = "A"; // Activity abbreviation
    document.getElementById("companyDataP").innerHTML = "P";  // Company abbreviation
    document.getElementById("foodDataP").innerHTML = "F";     // Food abbreviation
    document.getElementById("weatherDataP").innerHTML = "W";  // Weather abbreviation
});

// Add click event listener for the activity tab
activityTab.addEventListener("click", () => {
    // Set the text for the Activity tab
    document.getElementById("activityDataP").innerHTML = "Activity";

    // Reset text for other tabs to their respective abbreviations
    document.getElementById("locationsDataP").innerHTML = "L"; // Location abbreviation
    document.getElementById("companyDataP").innerHTML = "P";   // Company abbreviation
    document.getElementById("foodDataP").innerHTML = "F";      // Food abbreviation
    document.getElementById("weatherDataP").innerHTML = "W";   // Weather abbreviation
});

// Add click event listener for the company tab
companyTab.addEventListener("click", () => {
    // Set the text for the Company tab
    document.getElementById("companyDataP").innerHTML = "People";

    // Reset text for other tabs to their respective abbreviations
    document.getElementById("activityDataP").innerHTML = "A"; // Activity abbreviation
    document.getElementById("locationsDataP").innerHTML = "L"; // Location abbreviation
    document.getElementById("foodDataP").innerHTML = "F";      // Food abbreviation
    document.getElementById("weatherDataP").innerHTML = "W";   // Weather abbreviation
});

// Add click event listener for the food tab
foodTab.addEventListener("click", () => {
    // Set the text for the Food tab
    document.getElementById("foodDataP").innerHTML = "Food";

    // Reset text for other tabs to their respective abbreviations
    document.getElementById("activityDataP").innerHTML = "A"; // Activity abbreviation
    document.getElementById("locationsDataP").innerHTML = "L"; // Location abbreviation
    document.getElementById("companyDataP").innerHTML = "P";   // Company abbreviation
    document.getElementById("weatherDataP").innerHTML = "W";   // Weather abbreviation
});

// Add click event listener for the weather tab
weatherTab.addEventListener("click", () => {
    // Set the text for the Weather tab
    document.getElementById("weatherDataP").innerHTML = "Weather";

    // Reset text for other tabs to their respective abbreviations
    document.getElementById("activityDataP").innerHTML = "A"; // Activity abbreviation
    document.getElementById("locationsDataP").innerHTML = "L"; // Location abbreviation
    document.getElementById("companyDataP").innerHTML = "P";   // Company abbreviation
    document.getElementById("foodDataP").innerHTML = "F";      // Food abbreviation
});