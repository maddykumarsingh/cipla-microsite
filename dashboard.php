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
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
</head>
<body class="h-screen w-screen background-image p-5 lg:p-10 text-white">
    <header class="w-full h-[20%] flex justify-between">
       <div class="w-1/2 flex justify-start items-center">
           <img class="w-[100%] lg:w-[40%]" src="./dist/images/tag.png" alt="">
       </div>
       <div class="w-1/2 flex justify-end space items-center">
            <img class="w-[30%] lg:w-[15%]"src="./dist/images/logo.png" alt="">
       </div>
    </header>
    <main class="w-full h-full">

    <section class="mt-[14%] w-full lg:w-1/2 lg:mt-[4%]">
        <div class="text-justify text-sm lg:text-2xl font-light  tracking-wide">
                        <span >Dear <?php echo isset($user_data['name']) ? $user_data['name'] : 'User'; ?> ,</span> 
                        <p>
                        Welcome to Team <?php echo isset($user_data['team_id']) ? $user_data['team_id'] : '1'; ?>  where you will find your Team members.
                        <br>Complete Task 1 to help your colleagues get to know you better!
                        </p>
                        
        </div>
    </section>

    <section class="mt-[5%] w-full  lg:mt-[4%] flex flex-col lg:flex-row">
          <div id="imageContainer" class="flex w-full lg:w-1/4 mx-auto flex-col lg:flex-row items-center">
                  <img class="w-[80%] rounded-2xl" src="./uploads/user/profile-image/<?=empty( $user_data['avatar'])?'dummy.webp':$user_data['avatar'] ?>" alt="Image" >
                    <input type="file" id="profileImageInput" class="hidden" accept="image/*">

                    <?php if( empty( $user_data['avatar']) ): ?>
                    <div class="flex relative items-center  text-black -top-4 justify-center cursor-pointer text-md font-arial bg-white w-[80%] rounded-full  ">
                        <span class="mr-4">Upload your picture</span> <img class="rounded-full  w-[18%]" src="./dist/images/upload.png" class=""/>
                    </div>
                    <?php endif; ?>
          </div>

          <div class="relative flex flex-col lg:w-2/4 space-y-5 mt-10">

                  <?php if(!empty($user_data['nick_name']) ): ?>
                    <div class="p-3 py-5 text-center text-black border-2 border-white   bg-gray-100 rounded-2xl w-full">
                        Brave Nick
                    </div>
                  <?php else: ?>

                    <div class="relative">
                            <input type="text" name="objective" class="p-3 py-5 text-center text-black border-2 border-white   bg-gray-100 rounded-2xl w-full" placeholder="One adjective that best describes you">
                            <span class="absolute text-xs  text-gray-400 top-0 right-6 font-arial font-bold ">
                                  Max 20 letters
                              </span>
                      </div>

                      <div class="relative">
                          <input type="text" name="nickName" class="p-3 py-5 text-center text-black border-2 border-white   bg-gray-100 rounded-2xl w-full" placeholder="The nickname by which people fondly refer to you">
                          <span class="absolute text-xs  text-gray-400 top-0 right-6 font-arial font-bold ">
                              Max 20 letters
                          </span>
                      </div>                         
                        
                        <div id="familyImage" class="p-3 py-5 text-center text-black border-2 border-white   bg-gray-100 rounded-2xl w-full">
                                <input type="file" name="familyPic" class="file-input" id="fileInput" />
                               <span class="flex w-full text-center justify-center text-gray-400">Upload a family picture<span class="flex justify-end text-right items-end ml-20"><img src="./dist/images/upload.png" class="w-8"/></span> 
                               <span class=" absolute text-xs  text-gray-400 bottom-2 right-[50%] font-arial font-bold ">
                                    Format:jpeg. Max size 5 mb
                                </span>
                        </div>

                        <div class="hidden lg:block absolute -right-60 bottom-0">
                           <button onclick="update()" class="bg-blue-100 cursor-pointer text-black rounded z-50 px-7 py-2">Update</button>
                        </div>
                <?php endif; ?>
          </div>

          <div class="relative w-full -z-10 lg:w-1/4 hidden lg:block">
              <span class="absolute top-0 left-0 mb-2 text-lg text-white opacity-50 p-2 rounded">Optimistic</span>
              <span class="absolute top-1/4 left-1/4 mb-2 text-xl text-white opacity-50 p-2 rounded">Affectionate</span>
              <span class="absolute top-1/2 left-1/2 mb-2 text-2xl text-white opacity-50 p-2 rounded">Courageous</span>
              <span class="absolute top-3/4 left-3/4 mb-2 text-3xl text-white opacity-70 p-2 rounded">Brave</span>
              <span class="absolute top-0 right-0 mb-2 text-4xl text-white opacity-90 p-2 rounded">Adventurous</span>
              <span class="absolute top-1/4 right-1/4 mb-2 text-5xl text-white opacity-80 p-2 rounded">Dazzling</span>
              <span class="absolute top-1/2 right-1/2 mb-2 text-6xl text-white opacity-60 p-2 rounded">Beautiful</span>
              <span class="absolute bottom-0 right-0 mb-2 text-7xl text-white opacity-60 p-2 rounded">Generous</span>
          </div>
       
    </section>



        

    <!-- Accordion Container -->
        <section class="mt-5 text-2xl">

        <!-- Accordion Item 1 -->
        <div class="mb-4">
            <!-- Accordion Header -->
            <button class="w-full flex items-center " onclick="toggleAccordion('accordion1')">
               <img class="w-[7%] lg:w-[3%] mr-6" src="./dist/images/plus.png" alt=""> Meet Your Team
            </button>
            <!-- Accordion Content (Initially Hidden) -->
            <div id="accordion1" class="accordion-content hidden">
            <div class="flex flex-row flex-wrap justify-center items-center">
                <?php foreach ($teamMembers as $index => $member): ?>
                    <div class="flex w-[250px] h-[300px] relative flex-col m-4 justify-center items-center scroll-m-1">
                        <img class="w-full" src="./uploads/user/profile-image/<?= empty($member['avatar'])? 'dummy.webp': $member['avatar']?>" class=" w-64 h-64">
                        <span class=" absolute -bottom-4 p-2  bg-white text-black rounded-xl w-full text-center "><?php echo $member['name']; ?></span>
                    </div>
                <?php endforeach; ?>
          </div>
            </div>
        </div>

        <!-- Accordion Item 2 -->
        <div class="mb-6">
            <!-- Accordion Header -->
            <button class="w-full flex items-center text-small " onclick="toggleAccordion('accordion2')">
            <img class="w-[7%] lg:w-[3%] mr-6" src="./dist/images/plus.png" alt=""> Meet your team's family
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

        </section>


        <section class="h-[5%]">

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