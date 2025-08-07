<?php
/**
 * BeyChoc Admin - Edit Product
 * Form to edit existing products
 */

require_once 'session_check.php';
require_once '../config/database.php';

$admin = getCurrentAdmin();
$success_message = '';
$error_message = '';
$product = null;

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Load product data
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        $error_message = 'Product not found.';
    }
} catch (Exception $e) {
    $error_message = 'Error loading product: ' . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product) {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';
    $weight = trim($_POST['weight'] ?? '');
    
    // Validation
    if (empty($name) || empty($code) || empty($description) || empty($category)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {
            // Check if product code already exists (but not for this product)
            $checkSql = "SELECT COUNT(*) FROM products WHERE code = ? AND id != ?";
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->execute([$code, $product_id]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $error_message = 'Product code already exists. Please use a different code.';
            } else {
                // Handle image upload
                $imagePath = $product['image']; // Keep existing image by default
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../assets/images/products/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $imageExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($imageExtension, $allowedExtensions)) {
                        $imageName = uniqid() . '_' . time() . '.' . $imageExtension;
                        $uploadPath = $uploadDir . $imageName;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                            $imagePath = 'assets/images/products/' . $imageName;
                        }
                    }
                }
                
                // Update product
                $updateSql = "UPDATE products SET name = ?, code = ?, description = ?, image = ?, category = ?, weight = ?, updated_at = NOW() WHERE id = ?";
                $updateStmt = $db->prepare($updateSql);
                $weightValue = ($category === 'Bars' || $category === 'Packages') ? $weight : null;
                
                $updateStmt->execute([
                    $name,
                    $code,
                    $description,
                    $imagePath,
                    $category,
                    $weightValue,
                    $product_id
                ]);
                
                $success_message = 'Product updated successfully!';
                
                // Reload product data
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $error_message = 'Error updating product: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - BeyChoc Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Reuse the same styles from add_product.php -->
    <style>
        body {
            background: var(--light-cream);
            padding-top: 0;
        }

        .admin-header {
            background: var(--gradient-primary);
            color: var(--white);
            padding: 1.5rem 0;
            box-shadow: var(--shadow);
        }

        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .admin-logo {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
        }

        .nav-link {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            color: var(--chocolate-brown);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-grid {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: var(--chocolate-brown);
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 1rem;
            border: 2px solid var(--cream);
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            background: var(--light-cream);
            color: var(--chocolate-brown);
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--olive);
            box-shadow: var(--shadow);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .weight-group {
            display: none;
        }

        .weight-group.show {
            display: flex;
            flex-direction: column;
        }

        .current-image {
            margin-bottom: 1rem;
        }

        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--gradient-secondary);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background: var(--cream);
            color: var(--chocolate-brown);
        }

        .btn-secondary:hover {
            background: var(--olive);
            color: var(--white);
        }

        @media (max-width: 768px) {
            .admin-nav {
                padding: 0 1rem;
            }

            .form-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-nav">
            <div class="admin-logo">
                <i class="fas fa-chocolate-bar"></i> BeyChoc Admin
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>

    <div class="form-container">
        <?php if ($product): ?>
        <div class="form-header">
            <h1>Edit Product</h1>
            <p>Update product information</p>
        </div>

        <?php if ($success_message): ?>
            <div class="message success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="message error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="productForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($product['name']); ?>"
                           placeholder="Enter product name">
                </div>

                <div class="form-group">
                    <label for="code">Product Code *</label>
                    <input type="text" id="code" name="code" required 
                           value="<?php echo htmlspecialchars($product['code']); ?>"
                           placeholder="Enter unique product code">
                </div>

                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required onchange="toggleWeightField()">
                        <option value="">Select Category</option>
                        <option value="White Chocolate" <?php echo $product['category'] === 'White Chocolate' ? 'selected' : ''; ?>>White Chocolate</option>
                        <option value="Milk Chocolate" <?php echo $product['category'] === 'Milk Chocolate' ? 'selected' : ''; ?>>Milk Chocolate</option>
                        <option value="Dark Chocolate" <?php echo $product['category'] === 'Dark Chocolate' ? 'selected' : ''; ?>>Dark Chocolate</option>
                        <option value="Light Chocolate" <?php echo $product['category'] === 'Light Chocolate' ? 'selected' : ''; ?>>Light Chocolate</option>
                        <option value="Bars" <?php echo $product['category'] === 'Bars' ? 'selected' : ''; ?>>Bars</option>
                        <option value="Packages" <?php echo $product['category'] === 'Packages' ? 'selected' : ''; ?>>Packages</option>
                    </select>
                </div>

                <div class="form-group weight-group" id="weightGroup">
                    <label for="weight">Weight</label>
                    <input type="text" id="weight" name="weight" 
                           value="<?php echo htmlspecialchars($product['weight'] ?? ''); ?>"
                           placeholder="e.g., 100g, 250g, 500g">
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required 
                              placeholder="Enter product description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Product Image</label>
                    <?php if ($product['image'] && file_exists('../' . $product['image'])): ?>
                    <div class="current-image">
                        <p>Current Image:</p>
                        <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="Current product image">
                    </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Leave empty to keep current image</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Product
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancel
                </a>
            </div>
        </form>

        <?php else: ?>
        <div class="form-header">
            <h1>Product Not Found</h1>
            <p>The requested product could not be found.</p>
        </div>
        <div class="form-actions">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Toggle weight field based on category
        function toggleWeightField() {
            const category = document.getElementById('category').value;
            const weightGroup = document.getElementById('weightGroup');
            
            if (category === 'Bars' || category === 'Packages') {
                weightGroup.classList.add('show');
                document.getElementById('weight').required = true;
            } else {
                weightGroup.classList.remove('show');
                document.getElementById('weight').required = false;
                document.getElementById('weight').value = '';
            }
        }

        // Initialize weight field visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleWeightField();
        });

        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const code = document.getElementById('code').value.trim();
            const category = document.getElementById('category').value;
            const description = document.getElementById('description').value.trim();
            
            if (!name || !code || !category || !description) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }

            // Validate weight if required
            const weightRequired = category === 'Bars' || category === 'Packages';
            const weight = document.getElementById('weight').value.trim();
            
            if (weightRequired && !weight) {
                e.preventDefault();
                alert('Weight is required for Bars and Packages.');
                return;
            }
        });
    </script>
</body>
</html>
