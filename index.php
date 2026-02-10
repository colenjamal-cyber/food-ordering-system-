<?php
include 'config.php';
$page_title = "Food Menu";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Food Ordering</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="main-header">
            <h1>Food Ordering System</h1>
            <nav class="main-nav">
                <?php if(isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn">My Dashboard</a>
                    <a href="cart.php" class="btn">View Cart</a>
                    <?php if(isAdmin()): ?>
                        <a href="admin/dashboard.php" class="btn admin-btn">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn">Register</a>
                <?php endif; ?>
            </nav>
        </header>

        <!-- Search and Filter Section -->
        <section class="search-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search for food items..." class="search-input">
                <select id="categorySelect" class="category-select">
                    <option value="">All Categories</option>
                    <?php
                    // Fetch categories from database
                    $cat_query = "SELECT * FROM categories";
                    $cat_result = $conn->query($cat_query);
                    while($cat = $cat_result->fetch_assoc()) {
                        echo "<option value='{$cat['category_id']}'>{$cat['category_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </section>

        <!-- Food Items Display -->
        <section class="food-section">
            <h2>Available Food Items</h2>
            <div id="foodContainer" class="food-grid">
                <!-- Food items will be loaded here by JavaScript -->
                <p>Loading food items...</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="main-footer">
            <p>Food Ordering System &copy; 2023 | Educational Project</p>
        </footer>
    </div>

    <!-- JavaScript Files -->
    <script src="../assets/js/main.js"></script>
    <script>
        // Load food items when page loads
        window.onload = function() {
            loadFoodItems();
            
            // Add event listeners for search and filter
            document.getElementById('searchInput').addEventListener('keyup', loadFoodItems);
            document.getElementById('categorySelect').addEventListener('change', loadFoodItems);
        };
        
        function loadFoodItems() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categorySelect').value;
            
            fetch(`../api/get_foods.php?search=${search}&category=${category}`)
                .then(response => response.json())
                .then(data => {
                    displayFoodItems(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        function displayFoodItems(foods) {
            const container = document.getElementById('foodContainer');
            
            if (foods.length === 0) {
                container.innerHTML = '<p class="no-items">No food items found.</p>';
                return;
            }
            
            let html = '';
            foods.forEach(food => {
                html += `
                <div class="food-card">
                    <div class="food-image">
                        <img src="../assets/images/food_${food.food_id % 5 + 1}.jpg" alt="${food.food_name}">
                    </div>
                    <div class="food-info">
                        <h3>${food.food_name}</h3>
                        <p class="food-desc">${food.description}</p>
                        <p class="food-price">$${food.price}</p>
                        <p class="food-category">Category: ${food.category_name || 'Uncategorized'}</p>
                        <button onclick="addToCart(${food.food_id})" class="add-to-cart-btn">
                            Add to Cart
                        </button>
                    </div>
                </div>
                `;
            });
            
            container.innerHTML = html;
        }
    </script>
</body>
</html>