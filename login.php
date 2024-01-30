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



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cipla Mircostie | Login </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
<link href="./dist/css/tailwind.css" rel="stylesheet">
   <style>
        body {
            background: url('./dist/images/lakes.jpg');
            background-size: cover;
            background-position-y: bottom;
            width: 100vw;
            height: 100vh;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            letter-spacing: 1px;
        }

        header {
            display: flex;
            flex-direction: row-reverse;
            justify-content: end;
            align-items: center;
            width: 100%;
            height: 10%;
        }

        main {
           height: 80%;  
           width: 100%;
        }

        .header-logo {
            width: 7.5%;
            margin-right: 25px;
        }

        .heading-third{
            font-size: 3rem;
            margin: 0;
        }

        .heading-first{
            font-size: 5rem;
            margin: 0;
        }

        #title-container {
            color: white; 
            width: 100%; 
            text-align: center;
        }

        .form-input {
            padding: 5px;
            width: 100%;
            text-align: center;
            margin: 5px 0;
            border: 0;
            outline-color: #0055b8;
            border-radius: 5px;
            height: 40px;
        }

        .form-input::placeholder{
            font-size: large;
        }

        form{
             width: 30%;
        }

        #login-button{
            font-size: 1.5rem; 
            background-color:#0055b8; 
            color: white; 
            display: flex; 
            border: 0px; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center;
            width: 50%;
            padding: 5px;
            border-radius: 5px;
        }


        @media only screen and (max-width: 767px){
           

            .heading-third{
                    font-size: 1rem;
            }

            .heading-first {
                font-size: 3rem;
            }   

            main {
                 height: 90%;
            }

            .form-input {
                width: 80%;
            }

            form{
                width: 80%;
            }

            .header-logo{
                width: 18%;
            }
        }



    



    </style>
</head>
<body>
    <header>
         <img class="header-logo" src="./dist/images/logo.png" alt="">
    </header>
    <main>
         <div id="title-container">
            <h3 class="heading-third" >You are born to do great things</h3>
            <h1 class="heading-first">Welcome to HR Offsite 2024</h1>
         </div>

         <div style="
         display: flex;
         justify-content: center;">
            <div style="text-transform: uppercase; letter-spacing: 3px; font-weight: bold; background-color: #ffd200; width: max-content; padding: 8px 10px; font-size: x-large;">
                #beyondthehorizon
            </div>
         </div>

         <div class="flex justify-center items-center mt-20 space-y-6">
            <form method="post" action="login.php">
                  <div>
                     <input class="form-input" name="email" type="text" placeholder="Enter Email">
                  </div>
                  <div>
                     <input class="form-input" name="password" type="text" placeholder="Enter Passcode">
                  </div>
                  <div >
                     <button class="hover:scale-105 cursor-pointer"  id="login-button">
                         <span style="text-transform: uppercase;">Login</span> <span style="font-size:small;">to let your great unfold</span>
                     </button>
                  </div>
            </form>
         </div>
        
        

    </main>
</body>
</html>