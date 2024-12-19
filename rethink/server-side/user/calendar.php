<?php
/*
 * Page Name: connections.php
 * Description: This script is a calendar management tool, which uses custom date-related
 *      classes and traits to generate and display a monthly calendar with current, 
 *      previous, and next month days arranged in weekly format.
*/

// Get the current date in 'YYYY-MM-DD' format
$getDate = date('Y-m-d');

// Trait containing helper methods for date formatting and extraction
trait DateHelpers {
    // Get the total number of days in the current month
    public function getMonthNumberDays() {
        return (int) $this->format('t');
    }

    // Get the current day number of the month (1–31)
    public function getCurrentDayNumber() {
        return (int) $this->format('j');
    }

    // Get the numeric month representation (1 for January, 12 for December)
    public function getMonthNumber() {
        return (int) $this->format('n');
    }

    // Get the short name of the month (e.g., Jan, Feb)
    public function getMonthName() {
        return $this->format('M');
    }

    // Get the current year (e.g., 2023)
    public function getYear() {
        return $this->format('Y');
    }

    // Get the complete date in 'YYYY-MM-DD' format
    public function getDate() {
        return (int) $this->format('Y-m-d');
    }
}

// Class to represent the current date with immutability and helper methods from DateHelpers
class CurrentDate extends DateTimeImmutable {
    use DateHelpers;

    // Constructor for CurrentDate, calls parent DateTimeImmutable constructor
    public function __construct(){
        parent::__construct();
    }
}

// Class to represent a date specifically for calendar functionality
class CalendarDate extends DateTime {
    use DateHelpers;

    // Constructor for CalendarDate, setting the date to the first day of the current month
    public function __construct(){
        parent::__construct();
        $this->modify('first day of this month'); // Set date to the first day of the month
    }

    // Method to get the day of the week for the month's starting day (1 = Monday, 7 = Sunday)
    public function getMonthStartDayOfWeek(){
        return (int) $this->format('N'); // Format 'N' gives day number (1-7) with Monday as 1
    }
}

    // The Calendar class handles date and time information to build a monthly calendar display
    class Calendar{
        // The current date (passed as a CurrentDate object)
        protected $currentDate;
        // The date representing the calendar month (passed as a CalendarDate object)
        protected $calendarDate;

        // Labels for the days of the week (in Japanese)
        protected $dayLabels = [
            '日', '月', '火', '水', '木', '金', '土'
        ];

        // Labels for each month in English
        protected $monthLabels = [
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        ];

        // Indicates if the calendar should start on Sunday (true) or Monday (false)
        protected $sundayFirst = true;

        // Array to hold weeks of the month, with each week represented as an array of days
        protected $weeks = [];

        // Constructor to initialize the Calendar with the current and calendar dates
        public function __construct(CurrentDate $currentDate, CalendarDate $calendarDate){
            $this->currentDate = $currentDate;
            $this->calendarDate = clone $calendarDate; // Clone the calendarDate to prevent changes to the original object
            $this->calendarDate->modify('first day of this month'); // Set the calendar date to the first day of the month
        }

        // Returns the day labels array
        public function getDayLabels(){
            return $this->dayLabels;
        }

        // Returns the month labels array
        public function getMonthLabels(){
            return $this->monthLabels;
        }

        // Sets whether the calendar starts on Sunday (true) or Monday (false)
        public function setSundayFirst($bool){
            $this->sundayFirst = $bool;
            if(!$this->sundayFirst) {
                // Rotate day labels to make Monday the first day if sundayFirst is false
                array_push($this->dayLabels, array_shift($this->getDayLabels));
            }
        }

        // Sets the calendar month based on the given month number
        public function setMonth($monthNumber){
            $this->calendarDate->setDate($this->calendarDate->getYear(), $monthNumber, 1);
        }

        // Returns the name of the current calendar month
        public function getCalendarMonth(){
            return $this->calendarDate->getMonthName();
        }

        // Returns the day of the week for the first day of the month
        protected function getMonthFirstDay(){
            $day = $this->calendarDate->getMonthStartDayOfWeek();

            // Adjust for Sunday-first or Monday-first
            if ($this->sundayFirst) {
                if($day === 7){
                    return 1;
                }
                if ($day < 7 ){
                    return ($day + 1);
                }
            }
            return $day;
        }

        // Determines if the provided day number matches today's date
        public function isCurrentDate($dayNumber){
            // Check if the year, month, and day of the calendar date match those of the current date
            if ($this->calendarDate->getYear() === $this->currentDate->getYear() && 
                $this->calendarDate->getMonthNumber() === $this->currentDate->getMonthNumber() &&
                $this->currentDate->getCurrentDayNumber() === $dayNumber){
                    return true; // Return true if it matches today's date
                }
                return false; // Return false if it does not match today's date
        }

        // Returns the array of weeks for the current calendar month
        public function getWeeks(){
            return $this->weeks;
        }

        // Constructs the calendar's weeks array to represent the current month,
        // including days from the previous and next months for a full weekly display
        public function create(){
            // Fill empty slots at the start of the first week with placeholder days from the previous month
            // The number of empty slots is based on the starting day of the current month
            $days = array_fill(0, ($this->getMonthFirstDay() - 1), ["currentMonth" => false, "dayNumber" => ""]);
            
            // Fill in the days for the current month, marking them as belonging to this month
            for ($x = 1; $x <= $this->calendarDate->getMonthNumberDays(); $x++) {
                $days[] = ["currentMonth" => true, "dayNumber" => $x];
            }

            // Split the days array into weekly chunks of 7 days
            $this->weeks = array_chunk($days, 7);

             // Fill in missing days from the previous month in the first week, if there are empty slots
            $firstWeek = $this->weeks[0];
            $prevMonth = clone $this->calendarDate; // Clone calendarDate to avoid modifying it
            $prevMonth->modify('-1 month'); // Move to the previous month
            $prevMonthNumDays = $prevMonth->getMonthNumberDays(); // Total days in the previous month

            // Populate empty slots from the end of the previous month
            for($x = 6; $x >= 0; $x--){
                if(!$firstWeek[$x]["dayNumber"]){
                    $firstWeek[$x]["dayNumber"] = $prevMonthNumDays;
                    $prevMonthNumDays -= 1; // Move to the previous day
                }
            }

            $this->weeks[0] = $firstWeek; // Update the first week with populated values

            // Fill in missing days from the next month in the last week, if there are empty slots
            $lastWeek = $this->weeks[count($this->weeks) - 1];
            $nextMonth = clone $this->calendarDate; // Clone calendarDate to avoid modifying it
            $nextMonth->modify('+1 month'); // Move to the next month

            $c = 1;  // Counter for the days of the next month

            // Populate empty slots from the beginning of the next month
            for($x = 0; $x < 7; $x++){
                if(!isset($lastWeek[$x])){
                    $lastWeek[$x]['currentMonth'] = false; // Mark as not part of the current month
                    $lastWeek[$x]['dayNumber'] = $c; // Assign day number from next month
                    $c++;
                }
            }
            $this->weeks[count($this->weeks) - 1] = $lastWeek; // Update the last week with populated values
        }
    }
