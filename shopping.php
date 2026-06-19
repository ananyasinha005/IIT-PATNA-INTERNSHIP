<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart Demo</title>
    <link rel="stylesheet" href="shop.css">
    
</head>
<body>
    <a href="cart.php" class="cart-icon">
    🛒 Cart(<span id="cart-count">0</span>)
</a>

<h1> Shop Your Favorites, Delivered with Love✨</h1>

<div class="container">

    <div class="card">
        <img src="mouse.jpg" class="product-img">
        <h3>Wireless Mouse</h3>
        <p>₹1499</p>
        <button onclick="addToCart('Wireless Mouse')">Add to Cart</button>
    </div>

    <div class="card">
        <img src="tshirt.jpg" class="product-img">
        <h3>Tshirt</h3>
        <p>₹500</p>
        <button onclick="addToCart('Tshirt')">Add to Cart</button>
    </div>

    <div class="card">
        <img src="furniture1.jpg" class="product-img">
        <h3>Furniture</h3>
        <p>₹3999</p>
        <button onclick="addToCart('Furniture')">Add to Cart</button>
    </div>
    <div class="card">
    <img src="jewellery.jpeg" class="product-img">
    <h3>Jewellery</h3>
    <p>₹2499</p>
    <button onclick="addToCart('Jewellery')">Add to Cart</button>
</div>

<div class="card">
    <img src="books.jpeg" class="product-img">
    <h3>Books</h3>
    <p>₹499</p>
    <button>Add to Cart</button>
</div>

<div class="card">
    <img src="toys.jpeg" class="product-img">
    <h3>Toys</h3>
    <p>₹999</p>
    <button onclick="addToCart('Toys')">Add to Cart</button>
</div>

</div>

<script>
let cart = [];

function addToCart(product) {
    cart.push(product);

    let list = document.getElementById("cartItems");
    let item = document.createElement("li");
    item.textContent = product;
    list.appendChild(item);
}

function checkout() {
    if (cart.length === 0) {
        alert("Your cart is empty!");
    } else {
        alert("Checkout successful! Total items: " + cart.length);
    }
}
</script>

</body>
</html>