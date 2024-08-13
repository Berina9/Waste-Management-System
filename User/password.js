document.getElementById('forgetPasswordBtn').addEventListener('click', function() {
    window.location.href = 'password.php';
});

document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var newPassword = document.getElementById('newPassword').value;
    var confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }

    // Handle password change logic here (e.g., send data to the server)
    alert('Password changed successfully!');
});
