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

<html>
<head>
    <title>Welcome to the website</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .background-image {
            background-image: url('./dist/images/dashboardBG.png'); /* Replace with the actual path to your image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .top-right {
            text-align: right;
            margin: 10px;
        }
    </style>
    
</head>
<body>
    <!-- <header class="h-[15%] flex justify-between items-center px-10 py-2 bg-blue-500" >
        <img class="w-1/4" src="./dist/images/tag.png" alt="Company Tag">
        <img style="width:8%" src="./dist/images/logo.png" alt="Company Logo">
    </header> -->
    <div class="background-image">
        <div class="flex flex-row justify-between">
            <div class="text-white text-5xl font-extrabold justify-start">
                <div class="flex p-4 justify-center text-black font-semibold">
                    <div class="flex font-arial_black  bg-yellow-400 h-8 items-center text-4xl md:text-3xl sm:text-3xl font-extrabold p-4">
                        #BEYONDTHEHORIZON
                    </div>
                </div>
            </div>
            <div class="top-right text-white text-5xl font-extrabold justify-end">Cipla</div>
        </div>

        <div class="flex flex-row  gap-4 p-4">
            <div class="flex flex-col justify-between flex-1">
                <div class="flex text-justify text-2xl font-medium font-arial ml-4">
                    Dear <?php echo isset($user_data['name']) ? $user_data['name'] : 'User'; ?> ,
                    <br>
                    Welcome to Team <?php echo isset($user_data['team_id']) ? $user_data['team_id'] : '1'; ?>  where you will find your Team members.
                    <br>Complete Task 1 to help your colleagues get to know you better!
                </div>


                <div class="flex flex-row col-span-1 mt-10">
                    <div class="flex mr-1">
                        <img src="./dist/images/p1.webp" alt="Image" class="rounded-lg w-72 h-72">
                    </div>
                    <div class="flex flex-col justify-center gap-5 m-2">
                        <input type="text" name="objective" class="flex p-2 text-center text-xl py-4  bg-white rounded-full w-[540px]" placeholder="One adjective that best describes you">
                        <input type="text" name="nickName" class="flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]" placeholder="The nickname by which people fondly refer to you">
                        <input type="text" name="familyPic" class="flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]" placeholder="Upload a family picture">
                    </div>
                </div>
            </div>
            <div class="flex overflow-y-visible">
                <img src="./dist/images/dashBoardBirds_Filled.png" alt="Image" class="rounded-lg w-200 justify-end items-end">
            </div>
        </div>

             
        <div class="flex text-justify text-3xl font-semibold m-2 ">
            Meet Your Team
        </div>


        <div class="container mx-auto mt-5">
        <div id="carouselExample" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php
                // Array of image URLs
                $imageUrls = [
                    './dist/images/p1.jpg',
                    './dist/images/p2.jpg',
                    './dist/images/p3.jpg',
                    './dist/images/p4.jpg',
                    './dist/images/p5.jpg',
                    './dist/images/p6.png',
                   
                ];

                // // Loop through the image URLs to generate carousel items
                // foreach ($imageUrls as $index => $imageUrl) {
                //     $activeClass = ($index === 0) ? 'active' : '';
                //     echo '<div class="carousel-item ' . $activeClass . '">';
                //     echo '<img class="w-95 h-auto" src="' . $imageUrl . '" alt="Slide ' . ($index + 1) . '">';
                //     echo '</div>';
                // }
                ?>
            </div>

            <div class="flex flex-row justify-center items-center my-10">
                <?php foreach ($teamMembers as $index => $member): ?>
                    <div class="flex flex-col m-4 justify-center items-center scroll-m-1">
                        <img src="./dist/images/p<?php echo $index + 1; ?>.jpg" alt="Team Member's Image" class="rounded-lg w-64 h-64">
                        <div class="flex bg-white rounded-xl w-64 h-12 text-2xl shadow-xl text-center font-semibold justify-center py-2"><?php echo $member['name']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

 
    </div>

</body>
</html>