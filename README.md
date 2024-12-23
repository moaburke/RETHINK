# RETHINK
<div>
  <img src="1.Record-your-mood.png" alt="1.Record your mood" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="2.Understand-your-mental-health.png" alt="2.Understand your mental health" width="400" style="display: inline-block; margin-right: 10px;">
</div>

<div>
  <img src="3.Discover-patterns.png" alt="3.Discover patterns" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="+Admin-side.png" alt="+Admin side" width="400" style="display: inline-block;">
</div>
<br>

**RE:THINK** is a comprehensive mental health tracking application designed to help users reflect on their emotional and mental well-being. This project was developed during my final year at **HAL Tokyo College of Technology and Design**, where I studied Information Processing Programming.  

The app is designed **in Japanese** to cater to Japanese-speaking users and includes features to track and analyze daily moods, emotions, activities, and sleep patterns, while providing tools such as a calendar and statistical insights.  

### Key Features for Users  
- **Track Daily Well-Being:** Record emotions, activities, and sleep patterns.  
- **Interactive Calendar:** Review past mental states and activities.  
- **Statistical Insights:** Identify trends and relationships between emotions and activities.  
- **Set Personal Goals:** Create goals to encourage positive habits.  
- **Feed and Chat Functionality:** Interact with other users by posting, editing, and chatting in the feed section.  
- **User Authentication:** Secure log-in and registration system for users.  

### Admin Features  
- **Statistics Dashboard:** View aggregated statistics for all users.  
- **User Management:** Maintain and organize user accounts.  
- **Content Management:** Add and manage activity categories, weather data, and other trackable factors for users.  
- **Feed Moderation:** Oversee posts and interactions to ensure safe and positive communication.  
- **Prohibited Words Management:** Manage a list of banned words to maintain a respectful community.  
- **Separate Admin Login:** Secure authentication system for admin accounts.  

**RE:THINK** helps users better understand their mental health, identify triggers, and take steps toward a healthier, more balanced life. The admin side ensures the application remains organized, safe, and effective for all users.  

This project reflects my technical skills and my passion for creating solutions that contribute to people’s well-being. It also represents my journey as an international student in Japan, where I combined my background from Sweden with the knowledge and experiences gained at HAL Tokyo.  

### Limitations  
Currently, the site is **not fully responsive** across all devices. However, I am actively working on implementing responsive design to improve the user experience on mobile and tablet devices.  

## Installation

To get **RE:THINK** up and running on your local machine, follow the steps below:

### 1. Clone the Repository
- First, clone the repository to your local machine. You can do this using the following command:
  ```bash
  git clone https://github.com/moaburke/rethink.git

### 2. Install XAMPP
- Download and install **[XAMPP](https://www.apachefriends.org/index.html)**, which includes Apache, MySQL, and PHP.
- During the installation process, ensure that Apache and MySQL are selected for installation.

### 3. Set Up the Database
- Open **XAMPP** and start **phpMyAdmin** by clicking the **Admin** button next to **MySQL** in the XAMPP Control Panel. This will open phpMyAdmin in your browser.
- In phpMyAdmin, create a new database called **rethink**.
- Import the **rethink.sql** file from the downloaded folder:
  - In phpMyAdmin, select the **rethink** database.
  - Click the **Import** tab.
  - Choose the **rethink.sql** file from the downloaded folder and click **Go** to import it.
  - This will create the necessary tables for the application.

### 4. Create a MySQL User
- In phpMyAdmin, go to the **User Accounts** section.
- Add a new user with the following details:
  - **Username:** guest
  - **Host name:** Any host (use `%` if unsure)
  - **Password:** Select No password
  - Grant **Global privileges** to this user by checking the "Check All" box.
  - Click **Go** to create the user.
    
### 5. Set the Time Zone
- To ensure the correct time zone is set, open the **timezone.php** file located under `server-side/shared/timezone.php`.
- In this file, update the time zone to reflect the location of the user. The current time zone is set to `America/New_York`. Change this to the appropriate time zone for the user (for example, `Europe/Stockholm` for Sweden).


### 6. Adjust Base URL (If Folder Name Is Changed)
- If you change the folder name from **rethink** to something else, you need to adjust the server setup in the following files:
  - **check_login.php**
  - **admin_layout.php**
  - **admin_check_login.php**
  - **user_layout.php**
  
  In each of these files, find the following line:
  ```php
  define('BASE_URL', '/rethink/');

Update `/rethink/` to match the new folder name (e.g., `/newfoldername/`).

### 7. Configure the Website
Before accessing the website, follow these steps to set up the project correctly:

1. Open the XAMPP Control Panel and click **Start** next to **Apache** and **MySQL**.
2. Locate the project folder downloaded from this repository. Inside the repository, you will find a folder named **`rethink`**.
3. **Important**:  
   - Only copy the **`rethink`** folder (not the entire repository folder) into the `htdocs` directory of your XAMPP installation (usually located at `C:\xampp\htdocs\`).  
   - Copying the entire folder downloaded from GitHub (including the repository's root directory with README, license, etc.) into `htdocs` will cause the base URLs in the project files to break.  
   - Ensure that the **`rethink`** folder is placed directly inside `htdocs`, like this:  
     ```
     C:\xampp\htdocs\rethink
     ```
4. Access the website by navigating to the following URL in your browser:  
  `http://localhost/rethink/index.php`

### 8. Access the Application
- Once everything is set up, you can access **RE:THINK** locally by going to `http://localhost/rethink/index.php`.
- Log in using the **guest** account, or set up your own user account within the application.

### Notes
- Ensure that **Apache** and **MySQL** are running whenever you want to access the application.
- You may need to tweak your **php.ini** file if you face any PHP-related issues.

## How to Log In

To access the application, follow these steps:

### Regular User Login
1. Click the **Login** button on the homepage.
2. Enter the username of any **pre-created user**. The usernames are:
   - `johndoe123`
   - `janesmith456`
   - `alexjohnson789`
   - `sarahlee321`
   - `markbrown101`
   - `emilydavis234`
   - `chriswilson567`
   - `lilymartinez890`
   - `michaelwhite123`
   - `oliviataylor456`  
3. Use the password `password` for all users to log in.

Alternatively, you can create a **new user account**:
1. Click the **Sign Up** button.
2. Fill in the required details to create your account.
3. After signing up, return to the **Login** page and use your new credentials to log in.

---

### Admin Login
To access the admin side of the application:
1. Click the **Login** button.
2. On the login page, locate the link below the login form:  
   **"管理者としてログイン"**  
   *(In English: "Log in as an Admin")*
3. Click the link to switch to the admin login form.
4. Use the following credentials to log in as an admin:
   - **Username:** `MainAdmin`  
   - **Password:** `admin`

---

### Notes
- The application includes a mix of **English** and **Japanese**, with titles often in **English**, some content in both **English** and **Japanese**, and detailed explanations primarily in **Japanese**.
- The admin login is intended for managing users, posts, and statistical insights. Be cautious when making changes.


## Future Improvements

Here are some enhancements that I plan to implement in future versions of the application:

- **Responsive Design:** Make the UI more responsive to improve user experience on various devices.
- **User Account Management:** Allow users to delete their own accounts.
- **Expanded User Control:** Enable users to add their own activities, companies, foods, feelings, etc., giving them more control over what they can register.
- **Edit Daily Trackings:** Allow users to edit their daily tracking entries for more flexibility in managing their data.
- **Admin Side Enhancements:** Allow admins to sort users. Currently, users are displayed by their user ID when they sign up.

## Screenshots
<h3>Landing Page</h3>
<div>
  <img src="Screenshots/Landing-Page.png" alt="Landing Page" width="400" style="display: inline-block; margin-right: 10px;">
</div>

<h3>Authentication Pages</h3>
<div>
  <img src="Screenshots/Login-Page.png" alt="Login Page" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="Screenshots/Sign-Up-Page.png" alt="Sign Up Page" width="400" style="display: inline-block;">
</div>

<h3>User Side</h3>
<div>
  <img src="Screenshots/User-Side_Home-Page.png" alt="User Side - Home Page" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="Screenshots/User-Side_Register-Mood.png" alt="User side - Register Mood" width="400" style="display: inline-block;">
</div>

<div>
  <img src="Screenshots/User-Side_Insights.png" alt="User Side - Insights" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="Screenshots/User-Side_Monthly-Insights.png" alt="User Side - Montly Insights" width="400" style="display: inline-block;">
</div>

<div>
  <img src="Screenshots/User-Side_Daily-Tracking-Overview.png" alt="User Side - Daily Tracking Overview" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="Screenshots/User-Side_My-Page.png" alt="User Side - My Page" width="400" style="display: inline-block;">
</div>
  

<h3>Admin Side</h3>
<div>
  <img src="Screenshots/Admin-Side_Dashboard.png" alt="Admin Side - Dadshboard" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="Screenshots/Admin-Side_Manage-Users.png" alt="Admin Side - Manage Users" width="400" style="display: inline-block; margin-right: 10px;">
</div>
<div>
  <img src="Screenshots/Admin-Side_Manage-Content.png" alt="Admin Side - Manage Contents" width="400" style="display: inline-block; margin-right: 10px;">
  <img src="Screenshots/Admin-Side_Feed-Analytics.png" alt="Admin Side - Feed Analytics" width="400" style="display: inline-block;">
</div>
<br>

## Technologies Used  
- **HTML5**  
- **CSS3**  
- **JavaScript**  
- **PHP**  
- **MySQL**  
- **Chart.js** (for rendering charts and graphs)
- **XAMPP** (for local development with Apache and MySQL)
- **PHPMyAdmin** (for managing MySQL database)
- **Font Awesome** (for icons)
- **PHP Sessions** (for user authentication and login management)

