<?php
session_start();

include '../assets/database-functions/dbconnection.php';

// Get all rooms
function getAllRooms($conn) {
    $query = "SELECT * FROM roomdetails ORDER BY RoomID DESC";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$rooms = getAllRooms($conn);

// Get room details
function getRoomDetails($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT * FROM roomdetails WHERE RoomID = '$id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Handle AJAX request for room details
if (isset($_GET['action']) && $_GET['action'] == 'get_room' && isset($_GET['id'])) {
    $roomDetails = getRoomDetails($conn, $_GET['id']);
    echo json_encode($roomDetails);
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
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="dashboard-container">
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <h1 class="dashboard-title">Room Management</h1>
        <button class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addEditModal">
            <i class="fas fa-plus-circle me-2"></i>Add New Room
        </button>
    </div>

    <div class="row" id="roomsContainer">
    <?php foreach ($rooms as $room): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="room-card shadow-sm">
                <div class="position-relative">
                    <img src="../assets/img/gallery/rooms/<?php echo htmlspecialchars($room['RoomCardImg']); ?>" alt="<?php echo htmlspecialchars($room['RoomName']); ?>" class="room-image">
                    <span class="price-badge">LKR <?php echo htmlspecialchars($room['PriceLKR']); ?></span>
                </div>
                <div class="cover-images-container">
                    <?php
                        $coverImages = explode(',', $room['CoverImage']);
                        foreach ($coverImages as $coverImage):
                    ?>
                        <img src="../assets/img/gallery/rooms/<?php echo htmlspecialchars($coverImage); ?>" alt="<?php echo htmlspecialchars($room['RoomName']); ?> Cover" class="cover-image">
                    <?php endforeach; ?>
                </div>
                <div class="room-card-header d-flex justify-content-between align-items-center">
                    <h5 class="room-title"><?php echo htmlspecialchars($room['RoomName']); ?></h5>
                </div>
                <div class="room-details">
                    <p><?php echo htmlspecialchars($room['Description']); ?></p>
                    <p><strong>Bed Type:</strong> <?php echo htmlspecialchars($room['BedType']); ?></p>
                    <p><strong>Size:</strong> <?php echo htmlspecialchars($room['Size']); ?></p>
                    <p><strong>Capacity:</strong> <?php echo htmlspecialchars($room['Capacity']); ?> (Adults: <?php echo htmlspecialchars($room['AdultCount']); ?>, Children: <?php echo htmlspecialchars($room['ChildCount']); ?>)</p>
                </div>
                <div class="room-actions d-flex justify-content-between">
                    <button class="btn btn-primary btn-sm edit-room" data-id="<?php echo $room['RoomID']; ?>">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                    
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addEditModal" tabindex="-1" aria-labelledby="addEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEditModalLabel">Add/Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="roomForm" enctype="multipart/form-data">
                    <input type="hidden" id="roomId" name="RoomID">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="roomName" class="form-label">Room Name</label>
                            <input type="text" class="form-control" id="roomName" name="RoomName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priceLKR" class="form-label">Price (LKR)</label>
                            <input type="number" class="form-control" id="priceLKR" name="PriceLKR" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="Description" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bedType" class="form-label">Bed Type (King / Double / Single)</label>
                            <input type="text" class="form-control" id="bedType" name="BedType" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="size" class="form-label">Room Size (Sqft)</label>
                            <input type="text" class="form-control" id="size" name="Size" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="capacity" class="form-label">Full Capacity (Adult + Child)</label>
                            <input type="number" class="form-control" id="capacity" name="Capacity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="adultCount" class="form-label">Adult Count</label>
                            <input type="number" class="form-control" id="adultCount" name="AdultCount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="childCount" class="form-label">Child Count</label>
                            <input type="number" class="form-control" id="childCount" name="ChildCount" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="facilities" class="form-label">Facilities</label>
                        <input type="text" class="form-control" id="facilities" name="Facilities">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="acAvailable" name="ACAvailable" value="1">
                        <label class="form-check-label" for="acAvailable">AC Available</label>
                    </div>
                    <div class="mb-3">
                        <label for="roomCardImg" class="form-label">Room Card Image</label>
                        <input type="file" class="form-control" id="roomCardImg" name="RoomCardImg" accept="image/*">
                        <div id="roomCardImgPreview" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label for="coverImage" class="form-label">Cover Images</label><br>
                        <small>Select Multiple Image at Once</small>
                        <input type="file" class="form-control" id="coverImage" name="CoverImage[]" accept="image/*" multiple>
                        <div id="coverImagesPreview" class="mt-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveRoom">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="../assets/js/disable-click.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addEditModal = new bootstrap.Modal(document.getElementById('addEditModal'));
    const roomForm = document.getElementById('roomForm');
    const saveButton = document.getElementById('saveRoom');
    const roomCardImgInput = document.getElementById('roomCardImg');
    const roomCardImgPreview = document.getElementById('roomCardImgPreview');
    const coverImageInput = document.getElementById('coverImage');
    const coverImagesPreview = document.getElementById('coverImagesPreview');

    // Add new room
    document.querySelector('.add-btn').addEventListener('click', function() {
        roomForm.reset();
        document.getElementById('roomId').value = '';
        document.getElementById('addEditModalLabel').textContent = 'Add New Room';
        roomCardImgPreview.innerHTML = '';
        coverImagesPreview.innerHTML = '';
        addEditModal.show();
    });

    // Edit room
    document.querySelectorAll('.edit-room').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('addEditModalLabel').textContent = 'Edit Room';

            
            fetch(`?action=get_room&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    
                    document.getElementById('roomId').value = data.RoomID;
                    document.getElementById('roomName').value = data.RoomName;
                    document.getElementById('description').value = data.Description;
                    document.getElementById('priceLKR').value = data.PriceLKR;
                    document.getElementById('bedType').value = data.BedType;
                    document.getElementById('size').value = data.Size;
                    document.getElementById('capacity').value = data.Capacity;
                    document.getElementById('adultCount').value = data.AdultCount;
                    document.getElementById('childCount').value = data.ChildCount;
                    document.getElementById('facilities').value = data.Facilities;
                    document.getElementById('acAvailable').checked = data.ACAvailable == 1;

                    
                    roomCardImgPreview.innerHTML = `<img src="../assets/img/gallery/rooms/${data.RoomCardImg}" class="img-thumbnail" alt="Room Card Image">`;

                    
                    const coverImages = data.CoverImage.split(',');
                    coverImagesPreview.innerHTML = coverImages.map(img => 
                        `<img src="../assets/img/gallery/rooms/${img}" class="img-thumbnail me-2" alt="Cover Image">`
                    ).join('');

                    
                    fetch('../assets/database-functions/admin/check-booking-rows.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        const priceInput = document.getElementById('priceLKR');
                        if (!data.canDelete) {
                            let originalPrice = priceInput.value;
                            priceInput.addEventListener('input', function() {
                                alert("Ongoing Bookings processing. Better not to change the price.");
                            });
                            priceInput.addEventListener('change', function() {
                                if (this.value !== originalPrice) {
                                    if (!confirm("Are you sure you want to change the price? This may affect ongoing bookings.")) {
                                        this.value = originalPrice;
                                    }
                                }
                            });
                        }
                    });
                });

            addEditModal.show();
        });
    });


    // Image preview
    roomCardImgInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                roomCardImgPreview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" alt="Room Card Image Preview">`;
            }
            reader.readAsDataURL(file);
        }
    });

    coverImageInput.addEventListener('change', function() {
        coverImagesPreview.innerHTML = '';
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                coverImagesPreview.innerHTML += `<img src="${e.target.result}" class="img-thumbnail me-2" alt="Cover Image Preview">`;
            }
            reader.readAsDataURL(file);
        }
    });

    // Save room
    saveButton.addEventListener('click', function() {
        const formData = new FormData(roomForm);
        fetch('../assets/database-functions/admin/add-room.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addEditModal.hide();
                location.reload(); 
            } else {
                alert('Failed to save room');
            }
        });
    });
});
</script>

</body>
</html>