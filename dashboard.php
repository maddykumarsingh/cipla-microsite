<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cipla Mircosite | Dashboard</title>
    <link href="./dist/css/tailwind.css" rel="stylesheet">
    <style>
        body {
            background-image: url('dist/images/dashboard-background.png');
        }
    </style>
</head>
<body class="w-screen h-screen ">
    <header class="h-[15%] flex justify-between items-center px-10 py-2" >
        <img class="w-1/4  " src="./dist/images/tag.png" alt="">
        <img style="width:8%" src="./dist/images/logo.png" alt="">
    </header>
    <main class="p-10">
        <div class="lg:text-3xl my-10 font-light">
            <span class="block mb-5 ">Dear XXX ,</span>
            <p class="font-normal">
                Welcome to Team 1 where you will find your Team members.
            </p>
            <p class="font-normal">
            Complete Task 1  to help your colleagues get to know you better!
            </p>
        </div>
        <div class="flex my-10">
            <div class="w-44 h-52 rounded-3xl bg-black">
                 <img class="w-full h-full rounded-3xl" src="./dist/images/avatar.jpg" alt="">
            </div> 
            <div>
                <input type="text">
                <input type="text">
            </div>
        </div>
        <div>
            <h6 class="uppercase">Meet your team</h6>
            <div class="flex space-x-10 justify-around my-10">
                    <div class="w-44 h-52  bg-black">
                        <img class="w-full h-full" src="./dist/images/avatar.jpg" alt="">
                    </div>
                    <div class="w-44 h-52  bg-black">
                        <img class="w-full h-full " src="./dist/images/avatar.jpg" alt="">
                    </div>
                    <div class="w-44 h-52  bg-black">
                        <img class="w-full h-full " src="./dist/images/avatar.jpg" alt="">
                    </div>
                    <div class="w-44 h-52  bg-black">
                        <img class="w-full h-full " src="./dist/images/avatar.jpg" alt="">
                    </div>
            </div>
        </div>

    </main>
</body>
</html>