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


// SQL query to select file_name from user_family_photos for users in the specified team
$sql = "SELECT user_family_photos.file_name, users.name
FROM user_family_photos
INNER JOIN users ON users.user_id = user_family_photos.user_id
WHERE users.team_id = ?";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Error in preparing the statement: ' . $conn->error);
}

$stmt->bind_param("s", $user_data['team_id']);

if (!$stmt->execute()) {
    die('Error executing the statement: ' . $stmt->error);
}

// Bind the result
$stmt->bind_result($file_name, $user_name);

// Fetch the result and store it in the familyPictures array
$familyPictures = array();

while ($stmt->fetch()) {
    $familyPictures[] = ['file_name' => $file_name, 'user_name' => $user_name];
}

// Close the statement
$stmt->close();





$conn->close();
?>

<html>
<head>
    <title>Welcome to the website</title>
     <link rel="stylesheet" href="./dist/css/tailwind.css">
    <style>
        .background-image {
            background-image: url('./dist/images/dashboard-background.jpg'); /* Replace with the actual path to your image */
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .top-right {
            text-align: right;
            margin: 10px;
        }

        .file-input {
  display: none;
}

.image-container {
  position: relative;
  display: inline-block;
}

.hidden {
  display: none;
}
    </style>

<style>
        #carousel-container {
            @apply relative w-3/5 mx-auto overflow-hidden;
        }

        #carousel {
            @apply flex transition-transform duration-500 ease-in-out;
        }

        .carousel-item {
            @apply min-w-full;
        }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var imageContainer = document.getElementById('imageContainer');
    var fileInput = document.getElementById('profileImageInput');

    imageContainer.addEventListener('click', function () {
      fileInput.click();
    });

    fileInput.addEventListener('change', function () {
      var file = fileInput.files[0];

      if (file) {
        var reader = new FileReader();
        uploadProfileImage( file )

        reader.onload = function (e) {
          imageContainer.querySelector('img').src = e.target.result;
        };

        reader.readAsDataURL(file);
      }
    });
  });


  function uploadProfileImage( file ) {

      if (file) {
        const formData = new FormData();
        formData.append('avatar', file);

        fetch('./upload-profile.php', {
          method: 'POST',
          body: formData,
        })
        .then(response => response.json())
        .then(data => {
          console.log('Image uploaded successfully:', data.filename);

          // Assuming you have a user ID available, replace 'USER_ID' with the actual user ID
          const userId = "<?=$user_id?>";

          // Send the filename to the server to update the user table
          fetch(`./updateUserAvatar.php?userId=${userId}&filename=${data.filename}`)
          .then(response => response.json())
          .then(data => {
            console.log('User avatar updated:', data);
          })
          .catch(error => {
            console.error('Error updating user avatar:', error);
          });
        })
        .catch(error => {
          console.error('Error uploading image:', error);
        });
      }
    }
</script>


<script>
  // Plain JavaScript
  document.addEventListener('DOMContentLoaded', function () {
    var fileInput = document.getElementById('fileInput');
    var fileInputContainer = document.querySelector('#familyImage');

    fileInputContainer.addEventListener('click', function () {
      fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        var file = fileInput.files[0];
        if (file) 
         uploadFamilyImage( file )
    });
 
});

  function uploadFamilyImage( file ) {

if (file) {
  const formData = new FormData();
  formData.append('avatar', file);

  fetch('./upload-family-pic.php', {
    method: 'POST',
    body: formData,
  })
  .then(response => response.json())
  .then(data => {
    console.log('Image uploaded successfully:', data.filename);

    // Assuming you have a user ID available, replace 'USER_ID' with the actual user ID
    const userId = "<?=$user_id?>";

    // Send the filename to the server to update the user table
    fetch(`./insert-family-picture.php?userId=${userId}&filename=${data.filename}`)
    .then(response => response.json())
    .then(data => {
      console.log('User avatar updated:', data);
      window.location.reload();
    })
    .catch(error => {
      console.error('Error updating user avatar:', error);
    });
  })
  .catch(error => {
    console.error('Error uploading image:', error);
  });
}
}
</script>

    <!-- Slick Carousel CSS -->
     <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen w-screen background-image p-2 md:p-6">
    <header class="w-full h-[20%] p-6 flex justify-between">
       <div class="w-1/2 flex justify-start items-center">
           <img class="lg:w-[50%] md:w-[80%]" src="./dist/images/tag.png" alt="">
       </div>
       <div class="w-1/2 flex justify-end space items-center">
            <img class="mr-20 lg:w-[15%] md:w-[30%]"src="./dist/images/logo.png" alt="">
       </div>
    </header>
    <main class=" w-full">

    <section>

    <div class="text-justify mx-4 text-xl text-white">
                    <span class="my-2 block">Dear <?php echo isset($user_data['name']) ? $user_data['name'] : 'User'; ?> ,</span> 
                    <p>
                    Welcome to Team <?php echo isset($user_data['team_id']) ? $user_data['team_id'] : '1'; ?>  where you will find your Team members.
                    <br>Complete Task 1 to help your colleagues get to know you better!
                    </p>
                    
    </div>

    </section>


        <section class="flex flex-row  gap-4 p-4">
                
                

                <div class="flex flex-row w-full mt-10 space-x-12" >
                  <div id="imageContainer" class="image-container relative flex flex-col items-center">
                  <img src="./uploads/user/profile-image/<?=empty( $user_data['avatar'])?'dummy.webp':$user_data['avatar'] ?>" alt="Image" class="rounded-lg w-72 h-72">
                    <input type="file" id="profileImageInput" class="hidden" accept="image/*">
                    <div class="flex text-center justify-center h-14 w-56 cursor-pointer text-md items-center font-medium font-arial bg-white rounded-xl absolute bottom-0 left-1/2 transform -translate-x-1/2 -mt-4">
                        Upload your picture <img src="./dist/images/upload.png" class="ml-2 w-8"/>
                    </div>
                </div>
                    <div class="flex flex-col justify-center gap-5 m-2">
                        <input type="text" name="objective" class="flex p-2 text-center text-xl py-4  bg-white rounded-full w-[540px]" placeholder="One adjective that best describes you">

                        <div class="relative">
                            <input type="text" name="nickName" class="flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]" placeholder="The nickname by which people fondly refer to you">
                            <div class="absolute top-0 right-0 p-1 mr-2 text-xs font-arial font-extralight text-gray-400">
                              Max 20 letters
                            </div>
                      </div>                         
                        <div id="familyImage" class="file-input-container flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]">
                                <input type="file" name="familyPic" class="file-input" id="fileInput" />
                               <span class="flex w-full text-center justify-center text-gray-400">Upload a family picture<span class="flex justify-end text-right items-end ml-20"><img src="./dist/images/upload.png" class="w-8"/></span> 
                        </div>
                    </div>
                </div>
            </div>
           
        </section>

        <section>
          <div class="flex flex-row justify-between">
            <h6 class="flex text-justify text-3xl font-semibold m-2 text-white ">
                Meet Your Team
            </h6>
            <button class="flex h-10 w-40 bg-blue-600 text-white justify-center text-center items-center  rounded-lg font-semibold mr-10" onclick="window.location.href='./dashboard.php'">Submit</button>  
            
          </div>

          <div class="flex flex-row sm:flex-wrap justify-center items-center my-10">
                <?php foreach ($teamMembers as $index => $member): ?>
                    <div class="flex flex-col m-4 justify-center items-center scroll-m-1">
                        <img src="./uploads/user/profile-image/<?= empty($member['avatar'])? 'dummy.webp': $member['avatar']?>" class="rounded-lg w-64 h-64">
                        <div class="flex bg-white rounded-xl w-64 h-12 text-2xl shadow-xl text-center font-semibold justify-center py-2"><?php echo $member['name']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

        </section>
        <section>
            <h6 class="flex text-white text-justify text-3xl font-semibold m-2 ">
                Meet Your Team member Family
            </h6>

            <div id="carousel-container">
    <div id="carousel" class="flex">

    <?php foreach($familyPictures as $index => $picture): ?>
      <div class="carousel-item">
            <img class="h-2/3" src="./uploads/user/family-image/<?=$picture['file_name']?>" alt="Family Photo">
        </div>
     <?php endforeach; ?>
      
       
        
    </div>
</div>

<div class="flex justify-center mt-4">
    <button id="prevBtn" class="bg-blue-500 text-white px-4 py-2 mr-2">Previous</button>
    <button id="nextBtn" class="bg-blue-500 text-white px-4 py-2">Next</button>
</div>

        </section>
       





    </main>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        const $carousel = $('#carousel');
        const $prevBtn = $('#prevBtn');
        const $nextBtn = $('#nextBtn');

        $prevBtn.on('click', function () {
            $carousel.animate({ marginLeft: '+=100%' }, 500, function () {
                // Move the last item to the front
                $carousel.prepend($carousel.children().last());
                $carousel.css('margin-left', 0);
            });
        });

        $nextBtn.on('click', function () {
            $carousel.animate({ marginLeft: '-=100%' }, 500, function () {
                // Move the first item to the end
                $carousel.append($carousel.children().first());
                $carousel.css('margin-left', 0);
            });
        });
    });
</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>


</body>
</html>