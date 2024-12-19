<?php
/*
 * File: index.php
 * Author: Moa Burke
 * Date: 2024-10-30
 * Description: 
 *     This file serves as the main entry point for the RE:THINK application. 
 *     It initializes the session for user data management and includes essential files for:
 *     - Database connections
 *     - User authentication
 *     - Header layout
 * 
 *     The HTML structure of this file defines the layout of the RE:THINK homepage, 
 *     featuring sections that describe the service's purpose, emotional tracking features, 
 *     mental health statistics, and calls to action for user engagement.
 * 
 * Sections Included:
 * - Header Section: Navigation and branding
 * - Main Section: Introduction and sign-up call to action
 * - "What is RE:THINK?" Section: Explanation of the service
 * - Mental Health Statistics Section: Information on mental health issues
 * - "How it Works" Section: Steps for using the RE:THINK service
 * - Sign-Up Section: Encouragement for user registration
 * - Footer Section: Copyright information
 * - Loading Screen: Heart animation during loading
 * 
 * Note: Ensure that all included files are correctly linked to provide the necessary functionality.
 */

session_start(); // Start the session

// Define a constant for the base path
define('BASE_PATH', './server-side/shared/');

include(BASE_PATH . "connections.php"); // Include databade connection file
include(BASE_PATH . "check_login.php"); // Include login check script to verify user authentication before accessing this page.
include(BASE_PATH . "timezone.php"); // Include the timezone configuration
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>RE:THINK</title>
        <!-- Load font Awesome for icons -->
        <script src="https://kit.fontawesome.com/4f1988a159.js" crossorigin="anonymous"></script>
        <!-- Preconnect to Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Gudea&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Work+Sans&display=swap" rel="stylesheet">
        <!-- Load JQuery library from CDN  -->
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
        <!-- Link to the custom CSS stylesheet -->
        <link rel="stylesheet" href="./assets/css/shared.css">
        <link rel="stylesheet" href="./assets/css/auth.css">
        <!-- Link to custom JavaScipt file that handles animations and interactions -->
        <script src="./assets/javascript/top_page_interactions.js" defer></script>
    </head>

    <body>
        <!-- Header Section -->
        <header class="header">
            <nav>
                <div class='header-left'>
                    <!-- Main navigation link to the homepage -->
                    <a href='./index.php'>
                        <!-- Container for the logo and website name -->
                        <div class='logo-container'>
                            <div class='logo-heart-left'></div> <!-- Left side of the heart logo -->
                            <div class='logo-heart-right'></div> <!-- Right side of the heart logo -->

                            <!-- Displaying the website name with a logo -->
                            <div class='site-name'>
                                RE:<span>THINK</span>
                            </div> 
                        </div><!-- .heart -->
                    </a>

                    <!-- Navigation links -->
                    <ul>
                        <!-- Link to "What is RE:THINK" section -->
                        <li class='hover-underline-animation'>
                            <a href="javascript: scrollToAboutSection()">What is RE:THINK?</a>
                        </li> 

                        <!-- Link to "How it works" section -->
                        <li class='hover-underline-animation'>
                            <a href="javascript: scrollToHowItWorksSection()">How it Works</a>
                        </li> 
                    </ul>
                </div><!-- .header-left -->

                <!-- Login button -->
                <a href='./user/login.php' class="header-link">Log In</a>
            </nav>
        </header>

        <!-- Main Section: Introduction to service with a call to action -->
        <section class="intro-section">
            <!-- Main heading with an inspiring message -->
            <h2 class="fade-in-left-top">より幸せ、より楽しく生きる。</h2> 

             <!-- Subheading explaining the RE:THINK service -->
            <h3 class="fade-in-right-top">RE:THINKの感情トラッカーで、気分の浮き沈みを見極めて、<br>原因を取り除き、心の元気を取り戻しましょう！</h3>

            <!-- Sign up button leading to the registration page -->
            <a href="./user/sign_up.php" class="primary-btn">Sign Up</a> 
        </section><!-- .intro-section -->

        <!-- "What is RE:THINK" Section: Explanation of the RE:THINK service -->
        <section class="main-section" id="about-rethink-section">
            <!-- Section heading for the RE:THINK content -->
            <h3>
                <span class="section-title-en">What is RE:THINK?</span> <!-- English title for the section -->
                <span class="section-title-jp">RE:THINKとは</span> <!-- Japanese title for the section -->
            </h3>
            
            <!-- Decorative heart element, enhancing the visual appeal of the section -->
            <div class='heart'>
                <div class='heart-left'></div> <!-- Left side of the heart graphic -->
                <div class='heart-right'></div> <!-- Right side of the heart graphic -->
            </div><!-- .heart --> 

            <!-- Paragraph explaining RE:THINK's purpose and how ir works tp track emotions -->
            <p class="fade-in-left">
                <span>RE:THINKとは、毎日の感情・気分・思考などをキャプチャする気分トラッカーです。気分トラッカーはメンタルヘルスを改善するためのポジティブ心理学の手法であります。通常は設定された時間間隔で気分を記録し、気分の変化のパターンを特定するのに役立ちます。気分を記録することで、あなたが幸福に影響することを見つけましょう。それだけではなく、うつ病などの精神的な病の原因究明にも役立てられます。気分トラッカーを使い、時間の経過に伴う感情の傾向を追跡することによって、自身の感情が何によって変化するのかを知るきっかけになります。</span>
                <span>あなたは自分自身をよりよく理解しようとしていますか？気分トラッカーを始めましょう。RE:THINKの気分トラッカーで気分と行動のパターンを分析して、自分のメンタル面について理解を深めよう。</span>
            </p>
        </section><!-- .main-section -->

        <!-- Section on Mental Health Statistics: Displays figures on mental health conditions -->
        <section class="full-width-section">
            <!-- Section heading for the "You're not Alone" message -->
            <h3>
                <!-- English title for the section, conveying support and community -->
                <span class="section-title-en">You're not Alone</span>
                <!-- Japanese title for the section, conveying the same supportive message -->
                <span class="section-title-jp">あなたはひとりじゃないよ</span>
            </h3>

            <div class="fade-in-left">
                <!-- First fact: Number of people receiving hospital treatment for mental health -->
                <div class="facts" id="mental-health-treatment-fact">         
                    <!-- Heart icon representing care and mental health -->       
                    <div class='heart'>
                        <div class='heart-left'></div> <!-- Left half of the heart icon -->
                        <div class='heart-right'></div> <!-- Right half of the heart icon -->
                    </div><!-- .heart -->

                    <!-- Paragraph displaying mental health statistics -->
                    <p>
                        <!-- Number of people affected by mental health issues -->
                        <span>419万人</span>
                        <!-- Description of the statistic, indicating these are individuals receiving treatment -->
                        <span class="info">心の病気で病院に通院や入院をしている人</span>
                    </p> 
                </div><!-- .facts -->

                <!-- Second fact: Number of suicides in a year -->
                <div class="facts" id="annual-suicide-statistics-fact">
                    <!-- Heart icon representing care and mental health -->   
                    <div class='heart'>
                        <div class='heart-left'></div> <!-- Left half of the heart icon -->
                        <div class='heart-right'></div> <!-- Right half of the heart icon -->
                    </div><!-- .heart -->

                    <!-- Paragraph displaying suicide statistics -->
                    <p>
                        <!-- Number of people who take their own lives in one year -->
                        <span>21,081人</span>
                        <!-- Description of the statistic, indicating these are individuals who commit suicide -->
                        <span class="info">1年間に自ら命を断つ人</span>
                    </p>
                </div><!-- .facts -->

            </div><!-- .fade-in-left -->
        </section><!-- .main-section -->

        <!-- Section on how RE:THINK works -->
        <section class="main-section" id="how-it-works-section">
            <h3 class="section-heading-how-to">
                 <!-- Dual-language heading explaining how to use RE:THINK -->
                <span class="section-title-en">How it Works</span>
                <span class="section-title-jp">RE:THINKの使い方</span>
            </h3>

            <!-- First step: Recording daily emotions -->
            <div class="content-block">
                <div class="step-one">
                    <!-- Icon representing journaling and tracking emotions -->
                    <i class="fa-solid fa-file-pen fade-in-right"></i> 
                </div>

                <div class="fade-in-left">
                    <!-- Step title in Japanese: Record your daily mood -->
                    <h3>毎日の気分を記録。</h3> 
                    <!-- Step description in Japanese -->
                    <p>その日の気分を選んで、行った活動などを追加しましょう。 また、メモを追加することもできます。たった一分だけで大切な感情を記録し確認できます！</p> 
                </div>
            </div><!-- .content-block -->

            <!-- Second step: Discovering patterns -->
            <div class="content-block">
                <div class="step-two">
                    <!-- Icon representing analysis and discovering patterms -->
                    <i class="fa-solid fa-chart-column fade-in-left"></i> 
                </div>

                <div class="fade-in-right">
                    <!-- Step title in Japanese: Discover patterns -->
                    <h3>パターンを発見。</h3>

                    <!-- Step description in Japanese -->
                    <p> RE:THINKの気分トラッカーを利用して、あなたが幸福に影響することを見つけましょう。時間が経つにつれて、さまざまな方法で分析できる自分自身に関する洞察に満ちたデータを収集します。カレンダーはあなたの感情的な幸福を反映する色で満たされます。これにより、これまでにない人生の鷲の視点を得ることができます。しかも、さまざまな要因と気分との関係を見つけるのに役立ちます。</p>
                </div>
            </div><!-- .content-block -->

            <!-- Third step: Improving mental health -->
            <div class="content-block">
                <div class="step-three">
                    <!-- Icon representing well-being -->
                    <i class="fa-solid fa-face-laugh-beam fade-in-right"></i> 
                </div>

                <div class="fade-in-left">
                    <!-- Step title in Japanese: Improve your mental health -->
                    <h3>あなたの精神的健康を改善しよう。</h3>
                    <!-- Step description in Japanese -->
                    <p>あなたは自分自身をより理解できた上で、自分の精神的健康を改善しましょう。日々の生活を大切にすることで、幸せや自己改善を実現させます。あなただけのストレス解消の気分記録、良い習慣を身につけ、不安を和らげ、長期的な幸福を達成しましょう。</p>
                </div>
            </div><!-- .content-block -->
            
        </section><!-- .main-section -->

        <!-- Section for signing up -->
        <section class="main-section sign-up-section">
            <!-- Explanation about analyzing the relationship between mood and actions, aimed at improving mental health -->
            <p>気分と行動の相互関係を分析。メンタルヘルスの管理や向上を目指すあなたに。</p> 

            <!-- Button for signing up -->
            <a href="./user/sign_up.php" class="primary-btn">Sign Up Now</a> 
        </section><!-- .main-section -->

        <!-- Footer Section -->
        <footer class="footer">  
            <div class="copyright">
            <small>&copy; 2024 RE:THINK</small> 
            </div>
        </footer>

        <!-- Loading screen -->
        <div class="loading-overlay">
            <div class="loader">    
                <!-- Heart animation in the loading screen -->                
                <div class='heart'>
                    <div class='heart-left'></div> <!-- Left half of the heart icon -->
                    <div class='heart-right'></div> <!-- Right half of the heart icon -->
                </div><!-- .heart -->
            </div>
        </div><!-- ./loader-wrapper -->

        <!-- Button to scroll back to the top -->
        <a href="javascript:" id="return-to-top"><i class="fa fa-angle-up"></i></a>  
    </body>
</html>

