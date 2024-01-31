<?php
session_start();
include_once('./config/database.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
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
       
    </script>
    <link href="./dist/css/tailwind.css" rel="stylesheet">
     
    <style>
        body, html {
            height: 100%;
            margin: 1;
        }
        .background-image {
            background-image: url('./dist/images/login-min.gif'); /* Replace with the actual path to your image */
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .top-right {
            text-align: right;
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="background-image">
        <div class="flex justify-end items-end p-4"><img src="./dist/images/Cipla_White.png"/></div>
        <div class="flex text-6xl font-medium md:text-5xl sm:text-5xl sm:mt-40 sm:p-6 p-2 font-arial text-white justify-center ">You are born to do great things</div>
       
        <div class="flex text-6xl lg:text-7xl md:6xl sm:6xl p-4 font-arial text-white justify-center items-center">
                Welcome to HR Offsite 2024 
        </div>
        
        <div class="flex p-4 justify-center mb-4 sm:mb-8">
            <div class="flex  bg-yellow-400 h-12 items-center text-black text-5xl md:text-5xl sm:text-4xl font-arial_black p-8 mb-8">
                #BEYONDTHEHORIZON
            </div>
        </div>
        <form method="post" action="login.php" class="flex flex-col">
            <div class=" flex flex-col">
                <div class="flex justify-center m-2 sm:m-4">
                    <input type="text" name="email" class="flex p-4 md:w-1/3 sm:w-full rounded-lg text-lg text-center placeholder:text-2xl " placeholder="Enter Email">
                </div>
                <div class="flex justify-center  m-2 sm:m-4">
                    <input type="password" name="password" class="flex p-4 w-1/4 md:w-1/3 sm:w-1/2  rounded-lg text-lg text-center placeholder:text-2xl " placeholder="Enter Password">
                </div>
                <div class="flex justify-center m-2 sm:m-4">
                    <button class=" flex flex-col font-semibold bg-blue-600 p-2 px-4 text-white text-xl rounded-xl justify-center items-center w-1/3 sm:w-1/3 mt-4">
                        <p class="text-3xl font-arial font-thin">LOGIN</p>
                        <p class="font-normal font-arial px-4"> to let your greatness unfold</p>
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>