<?php
session_start();
include_once('./config/database.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s",$password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user_data = $result->fetch_assoc();
        $_SESSION['user_id'] = $user_data['user_id'];

        // Redirect to the dashboard or another page after successful login
        header("Location: dashboard.php");

        exit();
    } else {
        $error_message = "Invalid username/email or password. Please try again.";
    }

    // Close the prepared statement and the database connection
    $stmt->close();
    $conn->close();

}
?>

<html>
<head>
    <title>Cipla Mircostie | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </script>
    <link href="./dist/css/tailwind.css" rel="stylesheet">
     
    <style>
        body, html {
            margin: 0;
        }
        .background-image {
            background-image: url('./dist/images/login-min.gif'); /* Replace with the actual path to your image */
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
        }

        .top-right {
            text-align: right;
            margin: 20px;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen w-screen background-image text-white p-10">
        <div class="flex justify-end items-end "><img class="w-[24%] lg:w-[8%]" src="./dist/images/Cipla_White.png"/></div>
        <div class="text-center mt-4 lg:text-[3rem]">You are born to do great things</div>
       
        <div class="text-3xl font-bold  my-3 tracking-wider lg:text-[5rem] lg:my-6 text-center">
                Welcome to HR Offsite 2024 
        </div>
        

        <div class="flex justify-center my-2 translate-y-5 w-full ">
            <div class="flex italic bg-yellow-400 lg:w-fit items-center text-gray-800 text-2xl lg:text-5xl font-arial_black p-2">
                #BEYONDTHEHORIZON
            </div>
        </div>
        <form method="post" action="login.php" class="flex mt-14 space-x-2 lg:space-y-5 flex-col">
                <div class="flex justify-center my-2 shadow-sm">
                    <input type="text" name="email" class="p-3 text-gray-800 lg:p-4 w-full lg:w-1/4 rounded-lg text-lg text-center placeholder:text-xl placeholder:font-light placeholder:text-gray-800 " placeholder="Enter Name">
                </div>
                <div class="flex justify-center my-2  shadow-sm">
                    <input type="password" name="password" class="p-3 text-gray-800  lg:p-4 w-full lg:w-1/4  rounded-lg text-lg text-center placeholder:text-xl placeholder:font-light placeholder:text-gray-800 " placeholder="Enter Passcode">
                </div>
                <div class="flex justify-center mt-10 lg:mt-1">
                    <button class=" flex flex-col font-semibold bg-blue-600 py-2 text-white text-xl rounded-xl justify-center items-center w-full lg:w-1/6 ">
                        <p class="text-xl font-arial font-thin">LOGIN</p>
                        <p class="font-normal font-arial text-sm"> to let your greatness unfold</p>
                    </button>
                </div>
        </form>
    </div>
</body>
</html>