<?php
session_start();

include '../assets/database-functions/dbconnection.php';

// Fetch promotions
$query = "SELECT * FROM promotions ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$promotions = array();
while ($row = mysqli_fetch_assoc($result)) {
    $promotions[] = $row;
}

// Fetch room details
$roomQuery = "SELECT * FROM roomdetails ORDER BY RoomName";
$roomResult = mysqli_query($conn, $roomQuery);

$rooms = array();
while ($roomRow = mysqli_fetch_assoc($roomResult)) {
    $rooms[] = $roomRow;
}

// Get promotion details
function getPromotionDetails($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT * FROM promotions WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Handle AJAX request for promotion details
if (isset($_GET['action']) && $_GET['action'] == 'get_promotion' && isset($_GET['id'])) {
    $promotionDetails = getPromotionDetails($conn, $_GET['id']);
    echo json_encode($promotionDetails);
    exit;
}
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
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
</head>
<body>


<?php include 'sidebar.php'; ?>


<div class="dashboard-container">
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <h1 class="dashboard-title">Promotions Dashboard</h1>
        <button class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addEditModal">
            <i class="fas fa-plus-circle me-2"></i>Add New Promotion
        </button>
    </div>

    <div class="row" id="promotionsContainer">
        <?php foreach ($promotions as $promotion): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="promo-card">
                    <div class="position-relative">
                        <img src="../assets/img/promotions/<?php echo htmlspecialchars($promotion['image']); ?>" alt="<?php echo htmlspecialchars($promotion['title']); ?>" class="promo-image">
                        <span class="discount-badge"><?php echo htmlspecialchars($promotion['discount_percentage']); ?> OFF</span>
                    </div>
                    <div class="promo-card-header d-flex justify-content-between align-items-center">
                        <h5 class="promo-title"><?php echo htmlspecialchars($promotion['title']); ?></h5>
                    </div>
                    <div class="promo-details">
                        <p><?php echo htmlspecialchars($promotion['description']); ?></p>
                        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($promotion['start_date']); ?></p>
                        <p><strong>End Date:</strong> <?php echo htmlspecialchars($promotion['end_date']); ?></p>
                    </div>
                    <div class="promo-actions d-flex justify-content-between">
                        <button class="btn btn-primary btn-sm edit-promo" data-id="<?php echo $promotion['id']; ?>">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete-promo" data-id="<?php echo $promotion['id']; ?>">
                            <i class="fas fa-trash-alt me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addEditModal" tabindex="-1" aria-labelledby="addEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEditModalLabel">Add/Edit Promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="promotionForm" enctype="multipart/form-data">
                    <input type="hidden" id="promotionId" name="id">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="discountPercentage" class="form-label">Discount Percentage</label>
                        <input type="text" class="form-control" id="discountPercentage" name="discount_percentage" required>
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div id="imagePreview"></div>
                    </div>

                    
                    <div class="mb-3">
                        <label class="form-label">Promo Applied Rooms</label>
                        <div class="room-checkboxes">
                            <?php foreach ($rooms as $room): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="promo_applied_rooms[]" value="<?php echo htmlspecialchars($room['RoomID']); ?>" id="room_<?php echo htmlspecialchars($room['RoomID']); ?>">
                                    <label class="form-check-label" for="room_<?php echo htmlspecialchars($room['RoomID']); ?>">
                                        <?php echo htmlspecialchars($room['RoomName']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePromotion">Save</button>
            </div>
        </div>
    </div>
</div>


    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../assets/js/disable-click.js"></script>
    
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const addEditModal = new bootstrap.Modal(document.getElementById('addEditModal'));
    const promotionForm = document.getElementById('promotionForm');
    const saveButton = document.getElementById('savePromotion');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');

    // Add new promotion
    document.querySelector('.add-btn').addEventListener('click', function() {
        promotionForm.reset();
        document.getElementById('promotionId').value = '';
        document.getElementById('addEditModalLabel').textContent = 'Add New Promotion';
        imagePreview.innerHTML = '';
        addEditModal.show();
    });

    // Edit promotion
    document.querySelectorAll('.edit-promo').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('addEditModalLabel').textContent = 'Edit Promotion';
            // Fetch promotion details and populate the form
            fetch(`?action=get_promotion&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('promotionId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('description').value = data.description;
                    document.getElementById('discountPercentage').value = data.discount_percentage;
                    document.getElementById('startDate').value = data.start_date;
                    document.getElementById('endDate').value = data.end_date;
                    imagePreview.innerHTML = `<img src="../assets/img/promotions/${data.image}" class="promoeditpreview-image" alt="Current Image">`;

                    // Populate the room checkboxes
                    const appliedRooms = data.promo_applied_rooms ? data.promo_applied_rooms.split(',') : [];
                    document.querySelectorAll('.room-checkboxes input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = appliedRooms.includes(checkbox.value);
                    });
                });
            addEditModal.show();
        });
    });

    // Save promotion
    saveButton.addEventListener('click', function() {
        const formData = new FormData(promotionForm);
        // Collect checked room IDs
        const selectedRooms = [];
        document.querySelectorAll('.room-checkboxes input[type="checkbox"]:checked').forEach(checkbox => {
            selectedRooms.push(checkbox.value);
        });
        formData.append('promo_applied_rooms', selectedRooms.join(','));

        fetch('../assets/database-functions/admin/add-edit-promotion.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addEditModal.hide();
                location.reload();
            } else {
                alert('Failed to save promotion');
            }
        });
    });
});


// Delete promotion
document.querySelectorAll('.delete-promo').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this promotion?')) {
                const id = this.getAttribute('data-id');
                fetch('../assets/database-functions/admin/delete-promotion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.col-md-6').remove();
                    } else {
                        alert('Failed to delete promotion');
                    }
                });
            }
        });
});


// Image preview
imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Image Preview">`;
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>