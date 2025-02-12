<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <header class="bg-white px-4 py-2 h-14 flex justify-between items-center">
        <div class="text-xl font-bold flex-grow text-center">Home</div>
        <!-- <div class="relative">
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" >
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M14.857 17.082a24 24 0 0 0 5.454-1.31A8.97 8.97 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.97 8.97 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.3 24.3 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </div> -->
        <a href="#" id="logoutButton" class="flex items-center text-red-500 hover:text-red-700 transition duration-300">
            <!-- Heroicons ArrowRightOnRectangle Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-6 w-6 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
            <span class="text-xs"></span>
        </a>
    </header>

    <!-- Search Bar -->
    <div class="p-4">
        <div class="bg-white rounded-lg flex items-center px-4 py-2 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" width="32" height="32"
                viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="1.5"
                    d="m21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607" />
            </svg>
            <input type="text" placeholder="Search on Kupa"
                class="ml-2 w-full border-none outline-none bg-transparent text-gray-600">
        </div>
    </div>

    <!-- Delivery Section -->
    <div class="p-4">
        <div class="bg-green-500 rounded-lg p-4 shadow-md text-white flex items-center justify-between">
            <div>
                <div class="text-sm font-bold">Delivery to Home</div>
                <div class="text-xs">Utama Street no. 14, Rumbai</div>
            </div>
            <div class="bg-white rounded-full text-green-500 px-3 py-1 text-xs">2.4 km</div>
        </div>
    </div>

    <!-- Promotion Section -->
    <div class="p-4">
        <div class="bg-white rounded-lg p-4 shadow-md flex">
            <div class="ml-4 flex flex-col justify-between w-full">
                <div class="flex-grow">
                    <div class="text-lg font-bold">Chicken Teriyaki</div>
                    <div class="text-xs text-gray-500">Discount 25%</div>
                </div>
                <button class="bg-green-500 w-1/2 text-white py-2 px-4 rounded-md mt-2">Order Now</button>
            </div>
            <img src="https://downshiftology.com/wp-content/uploads/2020/03/Teriyaki-Chicken-6.jpg"
                alt="Chicken Teriyaki" class="rounded-lg w-24 h-24 object-cover">
        </div>
    </div>

    <!-- Top of Week Section -->
    <div class="p-4">
        <h2 class="text-lg font-bold mb-2">Top of Week</h2>
        <div class="flex space-x-4 overflow-x-auto">
            <!-- Item 1 -->
            <a href="menu-detail.html" class="bg-white rounded-lg p-2 w-36">
                <img src="https://via.placeholder.com/150" alt="Hongkong Hainan" class="rounded-lg w-full object-cover">
                <div class="mt-2">
                    <h3 class="text-sm font-bold">Hongkong Hainanese</h3>
                    <div class="text-green-500 font-semibold">$14.99</div>
                </div>
            </a>
            <!-- Item 2 -->
            <a href="menu-detail.html" class="bg-white rounded-lg p-2 w-36">
                <img src="https://via.placeholder.com/150" alt="Hot & Sour Corn" class="rounded-lg w-full object-cover">
                <div class="mt-2">
                    <h3 class="text-sm font-bold">Hot & Sour Corn</h3>
                    <div class="text-green-500 font-semibold">$20.99</div>
                </div>
            </a>
            <!-- Item 3 -->
            <a href="menu-detail.html" class="bg-white rounded-lg p-2 w-36">
                <img src="https://via.placeholder.com/150" alt="Singapura Hotpot"
                    class="rounded-lg w-full object-cover">
                <div class="mt-2">
                    <h3 class="text-sm font-bold">Singapura Hotpot</h3>
                    <div class="text-green-500 font-semibold">$24.99</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Bottom Navbar -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-md p-2 flex justify-around">
        <a href="home.html" class="flex flex-col items-center text-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" width="32" height="32" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="1.5"
                    d="m2.25 12l8.955-8.955a1.124 1.124 0 0 1 1.59 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="text-xs">Home</span>
        </a>
        <a href="menu.html" class="flex flex-col items-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" width="32" height="32" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6z" />
            </svg>
            <span class="text-xs">Menu</span>
        </a>
        <a href="cart.html" class="flex flex-col items-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" width="32" height="32" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.137a60 60 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0m12.75 0a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0" />
            </svg>
            <span class="text-xs">Cart</span>
        </a>
        <a href="profile.html" class="flex flex-col items-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" width="32" height="32" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0a3.75 3.75 0 0 1 7.5 0M4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.9 17.9 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632" />
            </svg>
            <span class="text-xs">Profile</span>
        </a>

        <script src="js/logout.js"></script>
</body>

</html>