<?php
session_start();

include '../assets/database-functions/dbconnection.php';

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header('Location:login.php');
    exit();
}

// Function to get all gallery images
function getAllGalleryImages($conn)
{
    $sql = "SELECT id, title, image_url FROM gallery ORDER BY id DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$galleryImages = getAllGalleryImages($conn);
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
    <style>
        .main-content {
            padding: 20px;
        }
        .h2 {
            font-weight: bold;
            color: #007bff;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
            transform: scale(1.02);
            transition: all 0.2s ease-in-out;
        }
        .table-hover tbody tr:hover .img-thumbnail {
            border: 2px solid var(--primary-color);
        }
        .img-thumbnail {
            border-radius: 8px;
            cursor: pointer;
        }
        .modal-content {
            border-radius: 12px;
        }
        .btn {
            border-radius: 50px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-close {
            background: none;
            border: none;
            padding: 0;
        }
        .btn-close:focus {
            outline: none;
        }
        .modal-body img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

    <div class="container">
        <div class="row">

            <main class="col-md-10 ms-sm-auto px-4">
                
                <div class="dashboard-header d-flex justify-content-between align-items-center">
                    <h1 class="dashboard-title">Gallery Manage</h1>
                    <button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#addImageModal">
                        <i class="fas fa-plus"></i> Add New Image
                    </button>
                </div>

                <!-- Gallery Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($galleryImages as $image): ?>
                            <tr>
                                <td><?php echo $image['id']; ?></td>
                                <td>
                                    <img src="../assets/img/gallery/<?php echo $image['image_url']; ?>" alt="<?php echo $image['title']; ?>" class="img-thumbnail" style="width: 100px; height: 100px;" onclick="showImageModal('<?php echo $image['image_url']; ?>', '<?php echo $image['title']; ?>')">
                                </td>
                                <td><?php echo $image['title']; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteImage(<?php echo $image['id']; ?>)">
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

    <!-- Add Image Modal -->
    <div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addImageModalLabel">Add New Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="addImageForm" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="imageName" class="form-label">Title</label>
        <input type="text" class="form-control" id="imageName" name="imageName" required>
    </div>
    <div class="mb-3">
        <label for="imageFile" class="form-label">Image File</label>
        <input type="file" class="form-control" id="imageFile" name="imageFile" required>
    </div>
</form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addImage()">Add Image</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal for Enlarge Image -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" src="" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/disable-click.js"></script>

    <script>
        function addImage() {
    const form = document.getElementById('addImageForm');
    const formData = new FormData(form);

    fetch('../assets/database-functions/admin/add-image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Image added successfully!');
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

function deleteImage(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch('../assets/database-functions/admin/delete-image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Image deleted successfully!');
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

        function showImageModal(imageUrl, imageTitle) {
            document.getElementById('modalImage').src = '../assets/img/gallery/' + imageUrl;
            document.getElementById('imageModalLabel').textContent = imageTitle;
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }
    </script>
</body>
</html>
