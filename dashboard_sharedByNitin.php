<?php
session_start();
include_once('./config/database.php');

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's data after successful login
$user_id = intval($_SESSION['user_id']);


$user_query = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = $conn->query($user_query);

if ($user_result->num_rows == 1) {
    $user_data = $user_result->fetch_assoc();
}

$sql = "SELECT * FROM users WHERE user_id != ? AND team_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_data['team_id']);

// Execute the statement
$stmt->execute();
// Get the result set
$result = $stmt->get_result();


$teamMembers = array();



// Fetch data as needed
while ($row = $result->fetch_assoc()) {
    $teamMembers[] = $row;
}

// Close the statement and connection
$stmt->close();
$conn->close();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cipla Mircosite | Dashboard</title>
    <link href="./dist/css/tailwind.css" rel="stylesheet">
     <!-- Slick Carousel CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <style>
        body {
            background-image: url('dist/images/dashboard-background.png');
        }
    </style>
</head>
<body class="w-screen h-screen ">
    <header class="h-[15%] flex justify-between items-center px-10 py-2" >
        <img class="w-1/4" src="./dist/images/tag.png" alt="Company Tag">
        <img style="width:8%" src="./dist/images/logo.png" alt="Company Logo">
    </header>
    <main class="p-10">
        <div class="lg:text-3xl my-10 font-light">
            <span class="block mb-5">Dear <?php echo isset($user_data['name']) ? $user_data['name'] : 'User'; ?> ,</span>
            <p class="font-normal">
                Welcome to Team <?php echo isset($user_data['team_id']) ? $user_data['team_id'] : '1'; ?>  where you will find your Team members.
            </p>
            <p class="font-normal">
                Complete Task 1 to help your colleagues get to know you better!
            </p>
        </div>
        <div class="flex my-10">
            <div class="w-44 h-52 rounded-3xl overflow-hidden bg-black">
                <img class="w-full h-full object-cover" src="./dist/images/avatar.jpg" alt="Your Profile Image">
            </div>
            <div class="ml-6">
                <input type="text" placeholder="Your First Name">
                <input type="text" placeholder="Your Last Name">
            </div>
        </div>
        <section>
            <h6 class="uppercase">Meet your team</h6>
            <div class="flex space-x-10 justify-around my-10">
                <!-- Repeat the following team member block for each team member -->
                <div class="w-44 h-52 rounded-3xl overflow-hidden bg-black">
                    <img class="w-full h-full object-cover" src="./dist/images/avatar.jpg" alt="Team Member's Image">
                </div>
                <!-- Repeat the above block for each team member -->

                <?php foreach ($teamMembers as $member): ?>
                        <div class="w-44 h-52 bg-black">
                        <img class="w-full h-full object-cover" src="./dist/images/avatar.jpg" alt="Team Member's Image">
                            <p><?php echo $member['name']; ?></p>
                        </div>
               <?php endforeach; ?>
            </div>

       
        </section>

        <section>
            <h6>Meet your Team Family Member</h6>
            <div class="carousel-container">
                    <div>
                      <div>
                          <img width="20%" class="w-full h-full object-cover" src="./dist/images/avatar.jpg" alt="Team Member's Image">
                      </div>
                      <span>Team Member Name</span>
                    </div>
                    <div>
                      <div>
                          <img width="20%" class="w-full h-full object-cover" src="./dist/images/avatar.jpg" alt="Team Member's Image">
                      </div>
                      <span>Team Member Name</span>
                    </div>
                    <div>
                      <div>
                          <img width="20%" class="w-full h-full object-cover" src="./dist/images/avatar.jpg" alt="Team Member's Image">
                      </div>
                      <span>Team Member Name</span>
                    </div>
            </div>

        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
        // Wait for the document to be ready
        $(document).ready(function(){
            // Initialize the Slick Carousel
            $('.carousel-container').slick({
                dots: true, // Display dots for navigation
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1
            });
        });
    </script>
</body>
</html>
