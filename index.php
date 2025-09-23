<?php
include 'db.php'; // ✅ Database connection

// ✅ Fetch images for the second carousel from the database
$stmt = $pdo->query("SELECT * FROM products WHERE category = 'featured'");
$carouselItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle logout success message
$logoutSuccess = $_SESSION['logout_success'] ?? '';
$loginSuccess = $_SESSION['login_success'] ?? '';
unset($_SESSION['logout_success'], $_SESSION['login_success']);
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>ShoeARizz Philippines</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/search.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body id="page-top" data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="54">
<?php include __DIR__ . '/includes/navbar.php'; ?>

<?php if ($logoutSuccess): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 0; border-radius: 0; position: fixed; top: 80px; left: 0; right: 0; z-index: 1050;">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($logoutSuccess); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($loginSuccess): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 0; border-radius: 0; position: fixed; top: 80px; left: 0; right: 0; z-index: 1050;">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($loginSuccess); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


    <header class="masthead" style="background-image: url('assets/img/g4940a8e64ec06ad001a520a58d8557b3b3155ec95757de01ef5f5a2a687ec10d5c4ea75d14969bb44ccdb635f367cdc666ec226531a777cbba35217b69298e89_640-1.jpg');height: 1000px;">
        <div class="container">
            <div class="intro-text">
                <div class="intro-lead-in"><span style="color: var(--bs-emphasis-color);text-shadow: 0px 0px 2px;">Shoe A Rizz</span></div>
                <div class="intro-heading text-uppercase"><span style="color: var(--bs-emphasis-color);border-color: var(--bs-body-bg);">Step into Success</span></div><a class="btn btn-primary btn-xl text-uppercase" role="button" href="men.php" style="background: var(--bs-tertiary-bg);color: rgb(0,0,0);border-color: rgb(0,0,0);">SHOP NOW</a>
            </div>
        </div>
    </header>
    <div class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000" data-bs-pause="false" id="carousel-1" style="height: 1200px;">
        <div class="carousel-inner h-100">
            <div class="carousel-item active h-100"><img class="w-100 d-block position-absolute h-100 fit-cover" alt="Slide Image" src="assets/img/photo-1636535721578-e4506adae3dd.jpg" style="z-index: -1;object-fit: cover;">
                <div class="container d-flex flex-column justify-content-center h-100">
                    <div class="row">
                        <div class="col-md-6 col-xl-4 offset-md-2">
                            <div style="max-width: 350px;"></div>
                        </div>
                        <div class="col">
                            <h1 class="text-uppercase fw-bold" style="font-family: Poppins, sans-serif;margin-left: 95px;font-size: 60px;padding-left: 100px;color: rgb(255,255,255);">CLASSIC SOLES</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item h-100"><img class="w-100 d-block position-absolute h-100 fit-cover" alt="Slide Image" src="assets/img/pexels-photo-6153367.jpeg" style="z-index: -1;object-fit: cover;" width="1393" height="600">
                <div class="container d-flex flex-column justify-content-center h-100">
                    <div class="row">
                        <div class="col-md-6 col-xl-4 offset-md-2">
                            <div style="max-width: 350px;">
                                <h1 class="text-uppercase fw-bold" style="color: var(--bs-body-bg);text-shadow: 0px 0px 14px var(--bs-emphasis-color);border-color: var(--bs-body-bg);font-family: Poppins, sans-serif;margin-right: 10px;font-size: 60px;">UNENDING STYLES</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item h-100"><img class="w-100 d-block position-absolute h-100 fit-cover" alt="Slide Image" src="assets/img/g1a2cd56208185987f0cf21b2dd22ba493dd12146e09f112888195c397eebe7f94b89755dfe61c2c3b4ed86278d18454ce4e5a8aae83617e7a9fb11e384264fe4_640.jpg" style="object-fit: cover;z-index: -1;"></div>
        </div>
        <div class="carousel-indicators"><button type="button" data-bs-target="#carousel-1" data-bs-slide-to="0" class="active"></button> <button type="button" data-bs-target="#carousel-1" data-bs-slide-to="1"></button> <button type="button" data-bs-target="#carousel-1" data-bs-slide-to="2"></button></div>
    </div>
    <section id="featured" class="py-4 py-xl-5" style="margin-bottom: -50px;">
        <div class="container">
            <div class="text-center p-4 p-lg-5">
                <h1 class="fw-bold mb-4" style="font-family: Poppins, sans-serif;font-size: 80px;">ELEVATE YOUR SOLE</h1><a href="men.html"><button class="btn btn-light fs-5 border rounded-pill py-2 px-4" type="button" style="background: var(--bs-emphasis-color);color: var(--bs-body-bg);" data-bs-target="men.html">SHOP</button></a>
            </div>
        </div>
    </section>
   
   
    <?php
// Fetch products from the specified categories
$stmt = $pdo->query("SELECT * FROM products WHERE category IN ('running', 'sneakers', 'athletics')");
$carouselItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- This is the carousel you should modify -->
<section style="margin-left: 34px;padding-top: 0px;">
    <div class="carousel slide" data-bs-ride="false" id="carousel-2">
        <div class="carousel-inner">
            <?php
            $totalItems = count($carouselItems);
            $itemsPerSlide = 3; // Number of items per slide
            $first = true; // To track the first slide for setting it as active

            for ($i = 0; $i < $totalItems; $i += $itemsPerSlide) {
                // Set the first item as active
                $activeClass = $first ? 'active' : '';
                $first = false;

                echo '<div class="carousel-item ' . $activeClass . '">';
                echo '<div class="container">';
                echo '<div class="row">';

                // Create a group of three products per slide
                for ($j = 0; $j < $itemsPerSlide && ($i + $j) < $totalItems; $j++) {
                    $item = $carouselItems[$i + $j];
                    echo '<div class="col-md-4" style="width: 440px;height: 400px;">';
                    echo '<a href="product.php?id=' . $item['id'] . '"><img data-bss-hover-animate="pulse" style="width: 400px;height: 400px;" src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title']) . '"></a>';
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <div><a class="carousel-control-prev" href="#carousel-2" role="button" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Previous</span></a><a class="carousel-control-next" href="#carousel-2" role="button" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Next</span></a></div>
        <div class="carousel-indicators">
            <?php
            $totalSlides = ceil($totalItems / $itemsPerSlide);
            for ($i = 0; $i < $totalSlides; $i++) {
                $activeClass = $i === 0 ? 'active' : '';
                echo '<button type="button" data-bs-target="#carousel-2" data-bs-slide-to="' . $i . '" class="' . $activeClass . '"></button>';
            }
            ?>
        </div>
    </div>
</section>




    <section style="padding-top: 0px;">
        <div data-bss-parallax-bg="true" style="height: 1000px;background-image: url(assets/img/photo-1560769629-975ec94e6a86.jpg);background-position: center;background-size: cover;">
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-md-6 text-center text-md-start d-flex d-sm-flex d-md-flex justify-content-center align-items-center justify-content-md-start align-items-md-center justify-content-xl-center">
                        <div style="max-width: 350px;">
                            <h1 class="text-uppercase fw-bold" style="margin-top: 143px;">BE FANCY IN YOur OWN STYLE</h1>
                            <p class="my-3">With the New BioStrides X</p><a class="btn btn-light fs-5 border rounded-pill py-2 px-4" role="button" style="background: var(--bs-emphasis-color);color: var(--bs-body-bg);" href="men.php">SHOP</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section id="promo-video" class="py-5" style="background-color: #f8f9fa;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div style="background-color: #fff; padding: 5px; border-radius: 8px;">
                    <video autoplay loop muted playsinline style="width: 100%; height: 100%; display: block; object-fit: cover;">
                        <source src="assets/img/promovideo.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
</section>





    <section style="padding-top: 0px;">
        <h1 class="fw-bold text-center mb-4" style="font-family: Poppins, sans-serif;font-size: 30px;margin-left: 30px;margin-right: 40px;">Find your Groove</h1>
        <div class="container" style="width: 1200px;">
            <div class="row" style="width: 1200px;">
                <div class="col-md-4" style="height: 580px;width: 400px;"><img alt="A basketball player in midair performing an impressive dunk against a backdrop of clouds." data-bss-hover-animate="pulse" style="width: 360px;height: 550px;" src="assets/img/pexels-photo-5586400.jpeg"></div>
                <div class="col-md-4" style="width: 400px;height: 580px;"><img alt="Stylish young woman with tattoos poses casually by a soccer goal in Moscow." data-bss-hover-animate="pulse" style="width: 360px;height: 550px;" src="assets/img/pexels-photo-2726161.jpeg"></div>
                <div class="col-md-4" style="width: 400px;height: 580px;"><img alt="Stylish woman in urban setting showcasing high fashion with designer handbag and chic attire." data-bss-hover-animate="pulse" style="width: 360px;height: 550px;" src="assets/img/pexels-photo-27677799.jpeg"></div>
            </div>
        </div>
    </section>

    
    <section id="contact" style="background-image:url('assets/img/map-image.png');">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="text-uppercase section-heading">Contact Us</h2>
                    <h3 class="text-muted section-subheading">Lorem ipsum dolor sit amet consectetur.</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form id="contactForm" name="contactForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3"><input class="form-control" type="text" id="name" placeholder="Your Name *" required=""><small class="form-text text-danger flex-grow-1 lead"></small></div>
                                <div class="form-group mb-3"><input class="form-control" type="email" id="email" placeholder="Your Email *" required=""><small class="form-text text-danger lead"></small></div>
                                <div class="form-group mb-3"><input class="form-control" type="tel" placeholder="Your Phone *" required=""><small class="form-text text-danger lead"></small></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3"><textarea class="form-control" id="message" placeholder="Your Message *" required=""></textarea><small class="form-text text-danger lead"></small></div>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-lg-12 text-center">
                                <div id="success"></div><button class="btn btn-primary btn-xl text-uppercase" id="sendMessageButton" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <hr class="my-4">

    <footer class="text-center">
        <div class="container text-muted py-4 py-lg-5">
            <ul class="list-inline">
                <li class="list-inline-item me-4"><a class="link-secondary" href="#">Web design</a></li>
                <li class="list-inline-item me-4"><a class="link-secondary" href="#">Development</a></li>
                <li class="list-inline-item"><a class="link-secondary" href="#">Hosting</a></li>
            </ul>
            <ul class="list-inline">
                <li class="list-inline-item me-4"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-facebook">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"></path>
                    </svg></li>
                <li class="list-inline-item me-4"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-twitter">
                        <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15"></path>
                    </svg></li>
                <li class="list-inline-item"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-instagram">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"></path>
                    </svg></li>
            </ul>
            <p class="mb-0">Copyright © 2024 ShoeARizz</p>
        </div>
    </footer>


<script>
// 🔄 Fetch cart count dynamically
function updateCartCount() {
    fetch("get_cart.php")
        .then(res => res.json())
        .then(data => {
            let count = data.cart ? data.cart.length : 0;
            document.getElementById("cart-count").innerText = count;
        })
        .catch(err => console.error("Cart fetch error:", err));
}

// Run on load
updateCartCount();

// Also refresh every 30s (optional)
setInterval(updateCartCount, 30000);

        // Real-time search functionality
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");

    // Real-time search input handling
    searchInput.addEventListener("input", async function() {
        const query = searchInput.value.trim();

        if (query.length < 1) {
            searchResults.innerHTML = ""; // Clear results if no input
            return;
        }

        const response = await fetch("search.php?q=" + encodeURIComponent(query)); // Fetch data from search.php
        const products = await response.json();

        // Display results or 'No results' if empty
        if (products.length === 0) {
            searchResults.innerHTML = "<li class='list-group-item'>No products found</li>";
        } else {
            searchResults.innerHTML = products.map(product => `
                <li class="list-group-item search-result-item" onclick="window.location.href='product.php?id=${product.id}'">
                    ${product.title}
                </li>
            `).join("");
        }
    });

    // Prevent form submission when pressing Enter and instead trigger the search
    searchInput.addEventListener('keydown', function(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent the form from submitting
            const query = searchInput.value.trim();
            if (query.length > 0) {
                window.location.href = 'search_results.php?q=' + encodeURIComponent(query); // Redirect to search results page
            }
        }
    });
    </script>

    <!-- Search JS is already included in navbar.php -->
</body>

</html>