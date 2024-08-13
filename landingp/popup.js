var log = document.getElementById("popup");

function showpopup(button) {
    log.style.display = "block";

    if (button === "login") {
        loadLogin();
    } else if (button === "signup") {
        loadSignup();
    }
}

function loadLogin() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "../User/login.php", true); // Only pulls HTML content
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var loginContent = document.getElementById("loginContent");
            var signupContent = document.getElementById("SignupContent");
            loginContent.innerHTML = xhr.responseText;
            signupContent.style.display = "none";
            loginContent.style.display = "block";
        }
    };
    xhr.send();
}

function loadSignup() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "../User/signup.php", true); // Only pulls HTML content
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var loginContent = document.getElementById("loginContent");
            var signupContent = document.getElementById("SignupContent");
            signupContent.innerHTML = xhr.responseText;
            loginContent.style.display = "none";
            signupContent.style.display = "block";
        }
    };
    xhr.send();
}

function hidepopup() {
    log.style.display = "none";
}
