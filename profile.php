<?php
global $conn;
session_start();
require_once "connect.php";

/*
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
*/
//delete later
$_SESSION['user_id'] = 1;

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $new_file_name = "user_" . $user_id . "." . $ext; // filename only
    $new_file_path = "uploads/" . $new_file_name;     // full path for move

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $new_file_path)) {
        $sql = "UPDATE users SET profile_pic='" . mysqli_real_escape_string($conn, $new_file_name) . "' WHERE id='$user_id'";
        mysqli_query($conn, $sql);
    }

    header("Location: profile.php");
    exit();
}

// Handle AJAX POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $address    = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE users SET 
                name='$first_name', 
                surname='$last_name', 
                email='$email', 
                phone='$phone', 
                address='$address' 
            WHERE id='$user_id'";

    if(mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($conn)]);
    }
    exit;
}

$query_user_data = "SELECT * FROM users WHERE id = '$user_id'";
$result_user_data = mysqli_query($conn, $query_user_data);

if ($result_user_data && mysqli_num_rows($result_user_data) > 0) {
    $user = mysqli_fetch_assoc($result_user_data);
} else {
    echo "User not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Treggo | My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body>

<div class="container-fluid profile-container">

    <div class="profile-card shadow-lg">

        <div class="profile-header">
            <img src="uploads/<?php echo htmlspecialchars(isset($user['profile_pic']) ? $user['profile_pic'] : 'default-user.png'); ?>"
                 alt="Profile Photo" class="profile-photo">

            <div>
                <h3><?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>

                <form method="POST" enctype="multipart/form-data" id="photoForm">
                    <input type="file" name="profile_pic" id="profile_pic" class="d-none">
                    <button type="button" class="btn btn-primary block full-width m-b" id="changePhotoBtn">
                        <i class="fa fa-camera"></i> Change photo
                    </button>
                </form>
            </div>
        </div>


        <hr>

        <h5 class="mb-3">Personal Information</h5>

        <form method="POST" id="profileForm" >
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" placeholder="First Name" value="<?php echo htmlspecialchars($user['name']); ?>">
                </div>

                <div class="form-group col-md-6">
                    <label>Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Last Name" value="<?php echo htmlspecialchars($user['surname']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text"  id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>">
            </div>

            <button type="button" id="saveBtn" class="btn btn-primary block m-b">
                <i class="fa fa-save"></i> Save changes
            </button>

            <p id="saveMessage" class="alert alert-success mt-2 py-2 px-3" style="display:none;">
                ✔ Changes saved successfully
            </p>
        </form>

        <hr>

        <h5>Security</h5>
        <p class="text-muted">Change your password</p>

        <!-- TODO: implement password change -->
        <button class="btn btn-warning">
            <i class="fa fa-lock"></i> Change password
        </button>

    </div>
</div>
<?php
require_once "includes/no_login/footer.php";
?>
</body>
<!-- JS -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.js"></script>

<script>
    document.getElementById('changePhotoBtn').addEventListener('click', function() {
        document.getElementById('profile_pic').click();
    });

    document.getElementById('profile_pic').addEventListener('change', function() {
        document.getElementById('photoForm').submit();
    });
</script>

<script>
    $('#saveBtn').on('click', function () {
        update_user();
    });

    function update_user() {
        var name = $("#first_name").val();
        var surname = $("#last_name").val();
        var email = $("#email").val();
        var phone = $("#phone").val();
        var address = $("#address").val();

        var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var alpha_regex = /^[a-zA-Z]{3,40}$/;
        var error = 0;

        // validation
        if (!alpha_regex.test(name)) error++;
        if (!alpha_regex.test(surname)) error++;
        if (!email_regex.test(email)) error++;

        if (error > 0) return;

        var data = new FormData();
        data.append("first_name", name);
        data.append("last_name", surname);
        data.append("email", email);
        data.append("phone", phone);
        data.append("address", address);

        $.ajax({
            type: "POST",
            url: "profile.php",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.status === "success") {
                    $("#saveMessage").fadeIn().delay(3000).fadeOut();
                }
            },
            error: function () {
                alert("Something went wrong!");
            }
        });
    }
</script>

