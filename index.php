<?php
session_start();
require "includes/db.php";
include "includes/header.php";
?>

<!-- HERO SECTION -->
<section class="relative h-screen sm:h-[80vh] flex items-center">
    <img src="assets/images/pic.jpg" class="absolute inset-0 w-full h-full object-cover -z-10" />

    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/60 -z-5"></div>

    <div class="w-full max-w-5xl mx-auto px-6 text-center text-white z-10">
        <h1 class="font-extrabold text-4xl sm:text-5xl md:text-6xl leading-tight drop-shadow-lg">
            Authentic. Handcrafted.<br />
            Proudly Made in Nepal.
        </h1>

        <p class="mt-6 text-lg text-white/90 max-w-3xl mx-auto leading-relaxed">
            Discover handcrafted treasures from local artisans — each piece tells a story.
        </p>

        <div class="mt-8">
            <a href="products.php"
                class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition-all duration-300 z-20">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section id="featured" class="max-w-7xl mx-auto px-6 py-12">

    <!-- TITLE + ARROWS -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-[#795548]">Featured Products</h2>

        <div class="flex gap-2">
            <button id="featuredPrev"
                class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100">
                &#8249;
            </button>

            <button id="featuredNext"
                class="w-10 h-10 flex items-center justify-center rounded-full border border-[#795548] text-[#795548] hover:bg-[#795548] hover:text-white transition">
                &#8250;
            </button>
        </div>
    </div>

    <!-- SLIDER WRAPPER -->
    <div class="relative overflow-hidden">

        <!-- SLIDER -->
        <div id="featuredSlider"
             class="flex gap-6 transition-transform duration-500 ease-in-out">

            <?php
            $res = $mysqli->query("SELECT * FROM products WHERE featured = 1 ORDER BY id DESC LIMIT 8");
            while ($p = $res->fetch_assoc()):
                $img = "assets/uploads/" . $p['image'];
                if (!file_exists($img)) $img = "assets/images/default.jpg";
            ?>

            <!-- PRODUCT CARD -->
            <div class="min-w-[48%] md:min-w-[23%] bg-white rounded-2xl shadow-lg p-4">

                <a href="product.php?id=<?= $p['id'] ?>">
                    <img src="<?= $img ?>" class="w-full h-44 object-cover rounded-lg mb-3 transition-transform hover:scale-105">

                </a>

                <h3 class="font-semibold text-lg truncate">
                    <?= htmlspecialchars($p['name']); ?>
                </h3>

                <!-- PRICE -->
                <?php if (!empty($p['mrp']) && $p['mrp'] > $p['price']): ?>
                    <p class="text-sm text-gray-400 line-through">
                        Rs. <?= number_format($p['mrp']); ?>
                    </p>
                <?php endif; ?>
                <p class="text-lg font-bold text-green-700">
                    Rs. <?= number_format($p['price']); ?>
                </p>

                <!-- STOCK -->
                <?php if ($p['stock'] > 0): ?>
                    <p class="text-sm text-green-600 font-semibold">
                        In Stock: <?= $p['stock'] ?>
                    </p>
                <?php else: ?>
                    <p class="text-sm text-red-500 font-bold">
                        Out of Stock
                    </p>
                <?php endif; ?>

                <!-- CART -->
                <?php if ($p['stock'] > 0): ?>
                    <a href="add_to_cart.php?id=<?= $p['id'] ?>"
                       class="mt-3 block bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700">
                        Add to Cart
                    </a>
                <?php else: ?>
                    <button class="mt-3 block bg-gray-400 text-white text-center py-2 rounded-lg cursor-not-allowed">
                        Out of Stock
                    </button>
                <?php endif; ?>

            </div>

            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- SLIDER SCRIPT -->
<script>
    const slider = document.getElementById("featuredSlider");
    const prevBtn = document.getElementById("featuredPrev");
    const nextBtn = document.getElementById("featuredNext");

    let index = 0;
    const cardWidth = slider.children[0].offsetWidth + 24; // card + gap
    const visibleCards = window.innerWidth < 768 ? 2 : 4;
    const maxIndex = slider.children.length - visibleCards;

    nextBtn.addEventListener("click", () => {
        if (index < maxIndex) {
            index++;
            slider.style.transform = `translateX(-${index * cardWidth}px)`;
        }
    });

    prevBtn.addEventListener("click", () => {
        if (index > 0) {
            index--;
            slider.style.transform = `translateX(-${index * cardWidth}px)`;
        }
    });
</script>

<!-- NEW ARRIVALS -->
<section id="new-arrivals" class="max-w-7xl mx-auto px-6 py-12">
    <h2 class="text-3xl font-bold text-[#795548] mb-6">New Arrivals</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

        <?php
        $res = $mysqli->query("SELECT * FROM products ORDER BY id DESC LIMIT 10");
        while ($p = $res->fetch_assoc()):
            $img = "assets/uploads/" . $p['image'];
            if (!file_exists($img)) $img = "assets/images/default.jpg";
        ?>

        <div class="bg-white rounded-2xl shadow-lg p-4 hover:shadow-xl transition">

            <!-- PRODUCT IMAGE -->
            <a href="product.php?id=<?= $p['id'] ?>">
                <img src="<?= $img ?>" class="w-full h-44 object-cover rounded-lg mb-3 transition-transform hover:scale-105">

            </a>

            <!-- PRODUCT NAME -->
            <a href="product.php?id=<?= $p['id'] ?>">
                <h3 class="font-semibold text-lg hover:text-[#795548] transition">
                    <?= htmlspecialchars($p['name']); ?>
                </h3>
            </a>

            <!-- PRICE -->
            <?php if (!empty($p['mrp']) && $p['mrp'] > $p['price']): ?>
                <p class="text-sm text-gray-400 line-through">
                    Rs. <?= number_format($p['mrp']); ?>
                </p>
            <?php endif; ?>
            <p class="text-lg font-bold text-green-700">
                Rs. <?= number_format($p['price']); ?>
            </p>

            <!-- STOCK DISPLAY -->
            <?php if ($p['stock'] > 10): ?>
                <p class="text-green-600 text-sm font-medium mt-1">✔ In Stock</p>
            <?php elseif ($p['stock'] > 0): ?>
                <p class="text-orange-500 text-sm font-medium mt-1">
                    ⚠ Only <?= $p['stock']; ?> left
                </p>
            <?php else: ?>
                <p class="text-red-600 text-sm font-medium mt-1">✖ Out of Stock</p>
            <?php endif; ?>

            <!-- ADD TO CART -->
            <?php if ($p['stock'] > 0): ?>
                <a href="add_to_cart.php?id=<?= $p['id'] ?>" 
                   class="mt-3 block bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700 transition">
                    Add to Cart
                </a>
            <?php else: ?>
                <button class="mt-3 block bg-gray-300 text-gray-700 text-center py-2 rounded-lg cursor-not-allowed">
                    Out of Stock
                </button>
            <?php endif; ?>

        </div>

        <?php endwhile; ?>

    </div>
</section>

<!-- ABOUT SECTION -->
<section id="about" class="max-w-7xl mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold text-[#795548] mb-6">Why Choose Nivara?</h2>
    <p class="mt-4 text-gray-700 leading-relaxed">
        Nivara is a Nepali marketplace dedicated to promoting local artisans and handcrafted products.
        Our mission is to preserve traditional crafts and provide fair income to our communities.
    </p>

    <div class="mt-8 grid md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-semibold">Authenticity</h3>
            <p class="text-gray-600 mt-2">Genuine handmade items directly from artisans.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-semibold">Sustainability</h3>
            <p class="text-gray-600 mt-2">Eco-friendly materials and ethical production.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-semibold">Community</h3>
            <p class="text-gray-600 mt-2">Supporting local livelihoods and culture.</p>
        </div>
    </div>
</section>

<!-- CONTACT SECTION -->
<section id="contact" class="max-w-7xl mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold text-[#795548] mb-6">Contact Us</h2>

    <form action="contact.php" method="post" class="grid md:grid-cols-2 gap-6 bg-white p-6 rounded-xl shadow">

        <input type="text" name="name" class="border p-3 rounded-lg" placeholder="Your Name" required>
        <input type="email" name="email" class="border p-3 rounded-lg" placeholder="Your Email" required>

        <input type="text" name="subject" class="border p-3 rounded-lg md:col-span-2" placeholder="Subject" required>

        <textarea name="message" rows="5" class="border p-3 rounded-lg md:col-span-2" placeholder="Message" required></textarea>

        <button class="bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 md:col-span-2">
            Send Message
        </button>
    </form>
</section>

<?php include "includes/footer.php"; ?>
