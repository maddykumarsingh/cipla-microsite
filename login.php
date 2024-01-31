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
            background-repeat: no-repeat;
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
        <div class="top-right text-white text-5xl font-extrabold justify-end mr-10 ">Cipla</div>
        <div class="flex text-6xl font-medium md:text-5xl sm:text-4xl p-2 font-arial text-white justify-center ">You are born to do great things</div>
       
        <div class="flex text-7xl  lg:text-7xl md:6xl sm:5xl p-4 font-arial text-white justify-center items-center">
                <div class="flex">Welcome to HR Offsite 2024</div> 
        </div>
        
        <div class="flex p-4 justify-center mb-4 sm:mb-8">
            <div class="flex  bg-yellow-400 h-12 items-center text-black text-5xl md:text-5xl sm:text-4xl font-arial_black p-8 mb-8">
                #BEYONDTHEHORIZON
            </div>
        </div>
        <form method="post" action="login.php" class="flex flex-col">
            <div class=" flex flex-col">
                <div class="flex justify-center m-2">
                    <input type="text" name="email" class="flex p-4 w-2/3 rounded-lg text-lg text-center placeholder:text-2xl " placeholder="Enter Email">
                </div>
                <div class="flex justify-center  m-2">
                    <input type="password" name="password" class="flex p-4 w-2/3 rounded-lg text-lg text-center placeholder:text-2xl " placeholder="Enter Password">
                </div>
                <div class="flex justify-center m-2">
                    <button class=" flex flex-col font-semibold bg-blue-900 p-2 px-4 text-white text-xl rounded-xl justify-center items-center">
                        <p class="text-3xl font-arial font-thin">LOGIN</p>
                        <p class="font-normal font-arial px-4"> to let your greatness unfold</p>
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>