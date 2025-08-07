<?php
/**
 * BeyChoc Admin Dashboard
 * Main admin panel for product management
 */

require_once 'session_check.php';
require_once '../config/database.php';

$admin = getCurrentAdmin();

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        switch ($_POST['action']) {
            case 'get_products':
                $sql = "SELECT * FROM products ORDER BY created_at DESC";
                $stmt = $db->query($sql);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'products' => $products]);
                break;
                
            case 'delete_product':
                $product_id = $_POST['product_id'];
                $sql = "DELETE FROM products WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$product_id]);
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BeyChoc</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        .admin-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background: var(--white);
            color: var(--chocolate-brown);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .dashboard-header h1 {
            color: var(--chocolate-brown);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card i {
            font-size: 3rem;
            color: var(--olive);
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            color: var(--chocolate-brown);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: var(--deep-green);
            font-weight: 500;
        }

        .products-management {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .products-header h2 {
            color: var(--chocolate-brown);
            font-size: 1.8rem;
        }

        .add-product-btn {
            background: var(--gradient-primary);
            color: var(--white);
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .add-product-btn:hover {
            background: var(--gradient-secondary);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .products-table-container {
            overflow-x: auto;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .products-table th,
        .products-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--cream);
        }

        .products-table th {
            background: var(--light-cream);
            color: var(--chocolate-brown);
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        .products-table tr:hover {
            background: var(--light-cream);
        }

        .product-image-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .category-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            background: var(--gradient-secondary);
            color: var(--chocolate-brown);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit,
        .btn-delete {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-edit {
            background: var(--olive);
            color: var(--white);
        }

        .btn-edit:hover {
            background: var(--deep-green);
        }

        .btn-delete {
            background: #dc3545;
            color: var(--white);
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .loading {
            text-align: center;
            padding: 2rem;
        }

        .no-products {
            text-align: center;
            padding: 3rem;
            color: var(--olive);
        }

        @media (max-width: 768px) {
            .admin-nav {
                padding: 0 1rem;
            }

            .dashboard-container {
                padding: 0 1rem;
            }

            .products-header {
                flex-direction: column;
                align-items: stretch;
            }

            .add-product-btn {
                justify-content: center;
            }

            .products-table {
                font-size: 0.9rem;
            }

            .products-table th,
            .products-table td {
                padding: 0.75rem 0.5rem;
            }

            .action-buttons {
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
            <div class="admin-user">
                <span>Welcome, <?php echo htmlspecialchars($admin['username']); ?></span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p>Manage your chocolate products</p>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-boxes"></i>
                <h3 id="totalProducts">0</h3>
                <p>Total Products</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-tags"></i>
                <h3 id="totalCategories">6</h3>
                <p>Categories</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3>24/7</h3>
                <p>Online Store</p>
            </div>
        </div>

        <div class="products-management">
            <div class="products-header">
                <h2>Product Management</h2>
                <a href="add_product.php" class="add-product-btn">
                    <i class="fas fa-plus"></i>
                    Add New Product
                </a>
            </div>

            <div class="products-table-container">
                <div class="loading" id="productsLoading">
                    <div class="spinner"></div>
                    <p>Loading products...</p>
                </div>

                <table class="products-table" id="productsTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Weight</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                    </tbody>
                </table>

                <div class="no-products" id="noProducts" style="display: none;">
                    <p>No products found. <a href="add_product.php">Add your first product</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load products on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
        });

        // Load products from server
        async function loadProducts() {
            const loading = document.getElementById('productsLoading');
            const table = document.getElementById('productsTable');
            const noProducts = document.getElementById('noProducts');
            const tbody = document.getElementById('productsTableBody');
            const totalProductsEl = document.getElementById('totalProducts');

            loading.style.display = 'block';
            table.style.display = 'none';
            noProducts.style.display = 'none';

            try {
                const formData = new FormData();
                formData.append('action', 'get_products');
                
                const response = await fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const products = data.products;
                    totalProductsEl.textContent = products.length;
                    
                    if (products.length === 0) {
                        noProducts.style.display = 'block';
                    } else {
                        tbody.innerHTML = products.map(product => `
                            <tr>
                                <td>
                                    <img src="../${product.image}" alt="${product.name}" 
                                         class="product-image-thumb" 
                                         onerror="this.src='../assets/images/placeholder.jpg'">
                                </td>
                                <td><strong>${product.name}</strong></td>
                                <td>${product.code}</td>
                                <td><span class="category-badge">${product.category}</span></td>
                                <td>${product.weight || 'N/A'}</td>
                                <td>${formatDate(product.created_at)}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_product.php?id=${product.id}" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn-delete" onclick="deleteProduct(${product.id}, '${product.name}')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                        table.style.display = 'table';
                    }
                } else {
                    showError('Failed to load products: ' + data.message);
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showError('Error loading products');
            } finally {
                loading.style.display = 'none';
            }
        }

        // Delete product
        async function deleteProduct(productId, productName) {
            if (!confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'delete_product');
                formData.append('product_id', productId);
                
                const response = await fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Product deleted successfully');
                    loadProducts(); // Reload products
                } else {
                    showError('Failed to delete product: ' + data.message);
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showError('Error deleting product');
            }
        }

        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString();
        }

        function showSuccess(message) {
            // Simple alert for now - could be enhanced with custom notifications
            alert('Success: ' + message);
        }

        function showError(message) {
            alert('Error: ' + message);
        }

        // Auto-refresh products every 30 seconds
        setInterval(loadProducts, 30000);
    </script>
</body>
</html>
