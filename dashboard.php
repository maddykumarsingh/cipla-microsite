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
    
</head>
<body class="h-screen w-screen bg-[#a5dfe6]">
    <main class="background-image">
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

        <section class="flex flex-row  gap-4 p-4">
            <div class="flex flex-col justify-between flex-1">
                <div class="flex text-justify text-2xl font-medium font-arial ml-4">
                    Dear <?php echo isset($user_data['name']) ? $user_data['name'] : 'User'; ?> ,
                    <br>
                    Welcome to Team <?php echo isset($user_data['team_id']) ? $user_data['team_id'] : '1'; ?>  where you will find your Team members.
                    <br>Complete Task 1 to help your colleagues get to know you better!
                </div>
                

                <div class="flex flex-row w-full mt-10 space-x-12" >
                <div id="imageContainer" class="image-container">
                        <img src="./uploads/user/profile-image/<?=empty( $user_data['avatar'])?'dummy.webp':$user_data['avatar'] ?>" alt="Image" class="rounded-lg w-72 h-72">
                        <input type="file" id="profileImageInput" class="hidden" accept="image/*">
                </div>
                    <div class="flex flex-col justify-center gap-5 m-2">
                        <input type="text" name="objective" class="flex p-2 text-center text-xl py-4  bg-white rounded-full w-[540px]" placeholder="One adjective that best describes you">
                        <input type="text" name="nickName" class="flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]" placeholder="The nickname by which people fondly refer to you">
                        
                        <div id="familyImage" class="file-input-container flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]">
                                <input type="file" name="familyPic" class="file-input" id="fileInput" />
                               <span class="w-full text-center text-gray-400">Upload a family picture</span> 
                        </div>
                    </div>
                </div>
            </div>
           
        </section>

        <section>
            <h6 class="flex text-justify text-3xl font-semibold m-2 ">
                Meet Your Team
            </h6>

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
            <h6 class="flex text-justify text-3xl font-semibold m-2 ">
                Meet Your Team member Family
            </h6>
              

        </section>
        <div class="carousel-container">
        <?php foreach($familyPictures as $index => $picture): ?>
                <div class="flex flex-row justify-center content-center">
                    <div>
                        <img class="h-2/3" src="./uploads/user/family-image/<?=$picture['file_name']?>" alt="Family Photo">
                    </div>
                    <span class="flex bg-white rounded-xl w-64 h-12 text-2xl shadow-xl text-center font-semibold justify-center py-2">
                        <?=$picture['user_name']?>
                    </span>
                </div>
        <?php endforeach; ?>


            </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
        // Wait for the document to be ready
        $(document).ready(function(){
            // Initialize the Slick Carousel
            $('.carousel-container').slick({
  centerMode: true,
  centerPadding: '80px',
  slidesToShow: 3,
  dots:true,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 2
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 1
      }
    }
  ]
}); });
    </script>


</body>
</html>