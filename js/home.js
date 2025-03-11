window.onload = function() {
    if (!localStorage.getItem('id') || !localStorage.getItem('full_name') || !localStorage.getItem('email')) {
        // If any of the required items are missing, redirect to signin.php
        window.location.href = "signin.php";
    }
}



function toggleComments(postId) {
    // Get the "more-comments" container for this post
    const moreCommentsDiv = document.getElementById(`more-comments-${postId}`);

    // Check if it's currently visible or hidden
    if (moreCommentsDiv.classList.contains('hidden')) {
        // Show additional comments
        moreCommentsDiv.classList.remove('hidden');
        // Change link text to "View Less"
        document.querySelector(`#more-comments-${postId} + a`).innerText = 'View Less';
    } else {
        // Hide additional comments
        moreCommentsDiv.classList.add('hidden');
        // Change link text back to "View More"
        document.querySelector(`#more-comments-${postId} + a`).innerText = 'View More';
    }
}


function logout() {
     // Clear specific items from localStorage
     localStorage.removeItem('id');
     localStorage.removeItem('full_name');
     localStorage.removeItem('email');

    window.location.href = "signin.php"; // Redirect to signin.php
}



const postInput = document.getElementById('postInput');
const postModal = document.getElementById('postModal');
const closeModal = document.getElementById('closeModal');
const imageUpload = document.getElementById('imageUpload');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const whatsappShare = document.getElementById('whatsappShare');

// Open modal
postInput.addEventListener('click', () => {
    postModal.classList.remove('hidden');
});

// Close modal
closeModal.addEventListener('click', () => {
    postModal.classList.add('hidden');
});

// Close modal when clicking outside
postModal.addEventListener('click', (event) => {
    if (event.target === postModal) {
        postModal.classList.add('hidden');
    }
});

// Image Preview
imageUpload.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            imagePreview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
});

// Feeling Button
document.getElementById('feelingBtn').addEventListener('click', () => {
    alert('Select your mood 😃');
});

// Tag Button
document.getElementById('tagBtn').addEventListener('click', () => {
    alert('Tag your friends 👥');
});

// Check-in Button
document.getElementById('checkInBtn').addEventListener('click', () => {
    alert('Share your location 📍');
});

// WhatsApp Share
whatsappShare.addEventListener('click', (event) => {
    event.preventDefault();
    const postText = document.querySelector('textarea[name="content"]').value;
    const whatsappUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(postText)}`;
    window.open(whatsappUrl, '_blank');
});


// Dropdown
function toggleMenu(button) {
    let menu = button.nextElementSibling;
    menu.classList.toggle("hidden");
}

// Close menu when clicking outside
document.addEventListener("click", function(event) {
    let menus = document.querySelectorAll(".menu-dropdown");
    menus.forEach(menu => {
        if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
            menu.classList.add("hidden");
        }
    });
});

function openEditModal(postId, content, imagePath) {
    document.getElementById("editPostId").value = postId;
    document.getElementById("editPostContent").value = content;
    document.getElementById("editPostImagePreview").src = imagePath ? imagePath : 'default-image.jpg';
    document.getElementById("editModal").classList.remove("hidden");
}

function closeEditModal() {
    document.getElementById("editModal").classList.add("hidden");
}

