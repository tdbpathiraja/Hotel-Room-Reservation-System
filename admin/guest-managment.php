<?php
session_start();

include '../assets/database-functions/dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location:login.php');
    exit();
}

// Function to get all guests
function getAllGuests($conn)
{
    $sql = "SELECT id, full_name, username, gender, email, birth_date, profile_image, created_at FROM users ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$guests = getAllGuests($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Varsity Lodge | Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link
      href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link href="../assets/css/admin-dashboard.css" rel="stylesheet">
    
</head>
<body>


<?php include 'sidebar.php'; ?>


    <div class="container">
        <div class="row">
            

            <main class="col-md-10 ms-sm-auto px-4">

                <div class="dashboard-header d-flex justify-content-between align-items-center">
                    <h1 class="dashboard-title">Guest Manage</h1>
                    <button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                        <i class="fas fa-plus"></i> Add New Guest
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="search-container mb-3">
                    <input type="text" id="guestSearch" class="form-control" placeholder="Search by name or username...">
                </div>

                <!-- Guest List Table -->
<div class="table-responsive">
    <table class="table table-striped table-sm table-hover" id="guestTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Birth Date</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($guests as $guest): ?>
            <tr>
                <td data-label="ID"><?php echo $guest['id']; ?></td>
                <td data-label="Image">
                    <img src="../assets/img/visitor-profiles/<?php echo $guest['profile_image'] ? $guest['profile_image'] : 'path/to/default/image.jpg'; ?>" alt="<?php echo $guest['full_name']; ?>" class="guest-image">
                </td>
                <td><?php echo $guest['full_name']; ?></td>
                <td><?php echo $guest['username']; ?></td>
                <td><?php echo $guest['email']; ?></td>
                <td><?php echo ucfirst($guest['gender']); ?></td>
                <td><?php echo date('M d, Y', strtotime($guest['birth_date'])); ?></td>
                <td><?php echo date('M d, Y', strtotime($guest['created_at'])); ?></td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editGuestModal<?php echo $guest['id']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?php echo $guest['id']; ?>">
                            <i class="fas fa-key"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteGuest(<?php echo $guest['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

            </main>
        </div>
    </div>

    <!-- Add Guest Modal -->
    <div class="modal fade" id="addGuestModal" tabindex="-1" aria-labelledby="addGuestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGuestModalLabel">Add New Guest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addGuestForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="birthDate" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="birthDate" name="birthDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="profileImage" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profileImage" name="profileImage">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addGuest()">Add Guest</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Guest Modal -->
    <?php foreach ($guests as $guest): ?>
    <div class="modal fade" id="editGuestModal<?php echo $guest['id']; ?>" tabindex="-1" aria-labelledby="editGuestModalLabel<?php echo $guest['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGuestModalLabel<?php echo $guest['id']; ?>">Edit Guest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGuestForm<?php echo $guest['id']; ?>">
                        <input type="hidden" name="id" value="<?php echo $guest['id']; ?>">
                        <div class="mb-3">
                            <label for="editFullName<?php echo $guest['id']; ?>" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editFullName<?php echo $guest['id']; ?>" name="fullName" value="<?php echo $guest['full_name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUsername<?php echo $guest['id']; ?>" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername<?php echo $guest['id']; ?>" name="username" value="<?php echo $guest['username']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail<?php echo $guest['id']; ?>" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail<?php echo $guest['id']; ?>" name="email" value="<?php echo $guest['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editGender<?php echo $guest['id']; ?>" class="form-label">Gender</label>
                            <select class="form-select" id="editGender<?php echo $guest['id']; ?>" name="gender" required>
                                <option value="male" <?php echo $guest['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $guest['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editBirthDate<?php echo $guest['id']; ?>" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="editBirthDate<?php echo $guest['id']; ?>" name="birthDate" value="<?php echo $guest['birth_date']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProfileImage<?php echo $guest['id']; ?>" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="editProfileImage<?php echo $guest['id']; ?>" name="profileImage">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updateGuest(<?php echo $guest['id']; ?>)">Update Guest</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;?>

    <!-- Reset Password Modal -->
    <?php foreach ($guests as $guest): ?>
    <div class="modal fade" id="resetPasswordModal<?php echo $guest['id']; ?>" tabindex="-1" aria-labelledby="resetPasswordModalLabel<?php echo $guest['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel<?php echo $guest['id']; ?>">Reset Password for <?php echo $guest['full_name']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm<?php echo $guest['id']; ?>">
                        <input type="hidden" name="id" value="<?php echo $guest['id']; ?>">
                        <div class="mb-3">
                            <label for="newPassword<?php echo $guest['id']; ?>" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword<?php echo $guest['id']; ?>" name="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword<?php echo $guest['id']; ?>" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword<?php echo $guest['id']; ?>" name="confirmPassword" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="resetPassword(<?php echo $guest['id']; ?>)">Reset Password</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/disable-click.js"></script>
    <script>
        function addGuest() {
            const form = document.getElementById('addGuestForm');
            const formData = new FormData(form);

            fetch('../assets/database-functions/admin/add-guest.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Guest added successfully!');
                    location.reload(); 
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        }

        function updateGuest(id) {
            const form = document.getElementById('editGuestForm' + id);
            const formData = new FormData(form);

            fetch('../assets/database-functions/admin/update-guest.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Guest updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        }

        function deleteGuest(id) {
            if (confirm('Are you sure you want to delete this guest?')) {
                fetch('../assets/database-functions/admin/delete-guest.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Guest deleted successfully!');
                        location.reload(); 
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            }
        }

        function resetPassword(id) {
            const form = document.getElementById('resetPasswordForm' + id);
            const newPassword = form.elements['newPassword'].value;
            const confirmPassword = form.elements['confirmPassword'].value;

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match. Please try again.');
                return;
            }

            const formData = new FormData(form);

            fetch('../assets/database-functions/admin/reset-password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password reset successfully!');
                    $('#resetPasswordModal' + id).modal('hide');
                    form.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        }
    </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('guestSearch');
    const table = document.getElementById('guestTable');
    const rows = table.getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();

        for (let i = 1; i < rows.length; i++) {
            const fullName = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
            const username = rows[i].getElementsByTagName('td')[3].textContent.toLowerCase();

            if (fullName.includes(searchTerm) || username.includes(searchTerm)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    });
});
</script>

</body>
</html>