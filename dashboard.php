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
<body class="h-screen w-screen background-image p-2 md:p-6 text-white">
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
                    <span class="my-2 tracking-wide block text-2xl font-light">Dear <?php echo isset($user_data['name']) ? $user_data['name'] : 'User'; ?> ,</span> 
                    <p class="text-xl tracking-wider">
                    Welcome to Team <?php echo isset($user_data['team_id']) ? $user_data['team_id'] : '1'; ?>  where you will find your Team members.
                    <br>Complete Task 1 to help your colleagues get to know you better!
                    </p>
                    
    </div>

    </section>


        <section class="flex flex-row w-full my-5">
                
                

                  <div id="imageContainer" class="image-container relative flex flex-col items-center w-full">
                  <img src="./uploads/user/profile-image/<?=empty( $user_data['avatar'])?'dummy.webp':$user_data['avatar'] ?>" alt="Image" class="rounded-lg w-72 h-72">
                    <input type="file" id="profileImageInput" class="hidden" accept="image/*">

                    <?php if( empty( $user_data['avatar']) ): ?>
                    <div class="flex relative items-center  -top-4 justify-center cursor-pointer text-md p-2 py-4 space-x-1 f font-arial bg-white w-[80%] rounded-full  ">
                        <span>Upload your picture</span> <img class="rounded-full ml-5 w-[22%]" src="./dist/images/upload.png" class=""/>
                    </div>
                    <?php endif; ?>
                  </div>
                    <div class="flex flex-col justify-center gap-5 m-2">

                       <div class="relative">

                         <input type="text" name="objective" class="flex p-2 text-center text-xl py-4  bg-white rounded-full w-[540px]" placeholder="One adjective that best describes you">
                         <span class="absolute text-xs  text-gray-400 top-0 right-6 font-arial font-bold ">
                              Max 20 letters
                          </span>
                        </div>

                        <div class="relative">
                            <input type="text" name="nickName" class="flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]" placeholder="The nickname by which people fondly refer to you">
                            <span class="absolute text-xs  text-gray-400 top-0 right-6 font-arial font-bold ">
                              Max 20 letters
                             </span>
                      </div>                         
                        <div id="familyImage" class="file-input-container relative flex p-2 text-center text-xl py-4 bg-white rounded-full w-[540px]">
                                <input type="file" name="familyPic" class="file-input" id="fileInput" />
                               <span class="flex w-full text-center justify-center text-gray-400">Upload a family picture<span class="flex justify-end text-right items-end ml-20"><img src="./dist/images/upload.png" class="w-8"/></span> 
                               <span class=" absolute text-xs  text-gray-400 bottom-2 right-[50%] font-arial font-bold ">
                                    Format:jpeg. Max size 5 mb
                                </span>
                        </div>
                      </div>
                      <div class="relative w-full">
    <span class="absolute top-0 left-0 mb-2 text-lg text-white opacity-50 p-2 rounded">Optimistic</span>
    <span class="absolute top-1/4 left-1/4 mb-2 text-xl text-white opacity-50 p-2 rounded">Affectionate</span>
    <span class="absolute top-1/2 left-1/2 mb-2 text-2xl text-white opacity-50 p-2 rounded">Courageous</span>
    <span class="absolute top-3/4 left-3/4 mb-2 text-3xl text-white opacity-70 p-2 rounded">Brave</span>
    <span class="absolute top-0 right-0 mb-2 text-4xl text-white opacity-90 p-2 rounded">Adventurous</span>
    <span class="absolute top-1/4 right-1/4 mb-2 text-5xl text-white opacity-80 p-2 rounded">Dazzling</span>
    <span class="absolute top-1/2 right-1/2 mb-2 text-6xl text-white opacity-60 p-2 rounded">Beautiful</span>
    <span class="absolute bottom-0 right-0 mb-2 text-7xl text-white opacity-60 p-2 rounded">Generous</span>
</div>
                </div>
           
        </section>

        <section class="">
    <!-- Accordion Container -->
    <div class="rounded shadow p-4">

        <!-- Accordion Item 1 -->
        <div class="mb-4">
            <!-- Accordion Header -->
            <button class="w-full text-[2rem] flex items-center space-x-4 text-white text-2xl text-left p-2" onclick="toggleAccordion('accordion1')">
               <img class="w-[3%] mr-6" src="./dist/images/plus.png" alt=""> Meet Your Team
            </button>
            <!-- Accordion Content (Initially Hidden) -->
            <div id="accordion1" class="accordion-content hidden">
            <div class="flex flex-row sm:flex-wrap justify-center items-center my-10">
                <?php foreach ($teamMembers as $index => $member): ?>
                    <div class="flex relative flex-col m-4 justify-center items-center scroll-m-1">
                        <img src="./uploads/user/profile-image/<?= empty($member['avatar'])? 'dummy.webp': $member['avatar']?>" class=" w-64 h-64">
                        <span class=" absolute -bottom-4 flex bg-white text-black rounded-xl w-64 h-12 text-2xl shadow-xl text-center font-semibold justify-center py-2"><?php echo $member['name']; ?></span>
                    </div>
                <?php endforeach; ?>
          </div>
            </div>
        </div>

        <!-- Accordion Item 2 -->
        <div class="mb-4">
            <!-- Accordion Header -->
            <button class="w-full text-white flex items-center text-[2rem] text-left p-2" onclick="toggleAccordion('accordion2')">
            <img class="w-[3%] mr-6" src="./dist/images/plus.png" alt=""> Meet your team members family
            </button>
            <!-- Accordion Content (Initially Hidden) -->
            <div id="accordion2" class="accordion-content hidden">
            
            <!-- Carasouel  -->
            <div id="carousel-container">
    <div id="carousel" class="slick-carousel h-fit w-fit">
        <?php foreach($familyPictures as $index => $picture): ?>
            <div class="carousel-item">
              <img src="./uploads/user/family-image/<?=$picture['file_name']?>" alt="<?=$picture['file_name']?>">
            </div>
        <?php endforeach; ?>
    </div>
    <div class="flex justify-center mt-4">
        <button id="prevBtn" class="text-white px-4 py-2 mr-2 cursor-pointer">
           <img src="./dist/images/left.png" alt="">
        </button>
        <button id="nextBtn" class=" text-white px-4 py-2 cursor-pointer">
        <img src="./dist/images/right.png" alt="">
        </button>
    </div>
</div>
<!-- Caraouel End -->

            </div>
        </div>

        <!-- Add more accordion items as needed -->

    </div>
        </section>


    </main>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function toggleAccordion(accordionId) {
        const accordion = document.getElementById(accordionId);
        accordion.classList.toggle('hidden');
    }
</script>

<script>
    $(document).ready(function(){
        $('#carousel').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow: $('#prevBtn'),
            nextArrow: $('#nextBtn'),
        });
    });
</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>


</body>
</html>