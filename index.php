<?php
include 'assets/connect.php';
include 'assets/track_visits.php';


// Function to get cart item count
function getCartItemCount()
{
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saisyd Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="icon" href="assets/img/round_logo.png" type="image/png">

</head>

<body class="loading">

    <!-- Loading Screen -->
    <div id="loading-screen" style="display: none !important; visibility: hidden !important;">
        <div class="loading-logo">
            <div class="coffee-cup-container">
                <div class="steam">
                    <div class="steam-line"></div>
                    <div class="steam-line"></div>
                    <div class="steam-line"></div>
                </div>
                <div class="coffee-cup">
                    <div class="cup">
                        <div class="cup-handle"></div>
                    </div>
                    <div class="saucer"></div>
                </div>
            </div>

            <div class="loading-text">
                <div class="cafe-name">SAISYD CAFÉ</div>
                <div class="tagline">THE HIDDEN FARM</div>
            </div>
        </div>

        <div class="loading-progress">
            <div class="progress-bar-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
            <div class="loading-percentage" id="loading-percentage">0%</div>
        </div>

        <div class="loading-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <div class="loading-message">
            Brewing the perfect experience...
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Sidebar Overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div id="mobileSidebar" class="sidebar">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a class="navbar-brand mx-0">
                    <img src="assets/img/saisydLogo.png" style="height: 40px;" alt="SAISYD Logo" />
                </a>
                <button id="closeSidebar" class="fs-3 border-0 bg-transparent">&times;</button>
            </div>

            <div id="sidebarNav">
                <a href="index.php" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.15s">
                    <i class="bi bi-house fs-5"></i> <span>Home</span>
                </a>
                <a href="#about" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.25s">
                    <i class="bi bi-info-circle fs-5"></i> <span>About</span>
                </a>
                <a href="#location" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.35s">
                    <i class="bi bi-geo-alt fs-5"></i> <span>Location</span>
                </a>
                <a href="#contact" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.45s">
                    <i class="bi bi-envelope fs-5"></i> <span>Contact</span>
                </a>
                <a href="cart.php" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.55s"
                    style="display: flex; align-items: center; justify-content: space-between; gap: 6px; position: relative;">
                    <i class="bi bi-cart fs-5"></i>
                    <span>Cart</span>
                    <?php if (getCartItemCount() > 0): ?>
                        <span class="badge bg-danger rounded-pill"
                            style="position: absolute; top: -5px; right: 70px; font-size: 0.75rem; padding: 0.25em 0.5em;">
                            <?php echo getCartItemCount(); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>

            <button class="btn menu-btn" onclick="location.href='menu.php'">
                <i class="fas fa-mug-hot me-2"></i> Menu
            </button>

        </div>

        <!-- Navbar -->
        <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-custom fixed-top py-2">
            <div class="container-fluid px-3">

                <!-- Mobile Layout: Burger (left) - Logo (center) - Cart (right) -->
                <div class="d-flex d-lg-none align-items-center w-100 position-relative" style="min-height: 50px;">
                    <!-- Left: Burger menu -->
                    <button id="openSidebarBtn" class="navbar-toggler border-0 p-1">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Center: Logo  -->
                    <div class="position-absolute top-50 translate-middle" style="left: 53%;">
                        <a class="navbar-brand fw-bold mb-0">
                            <img src="assets/img/saisydLogo.png" alt="SAISYD Logo" style="height: 45px;" />
                        </a>
                    </div>

                    <!-- Mobile: Right Cart -->
                    <div class="ms-auto d-flex align-items-center">
                        <a href="cart.php" class="d-flex align-items-center text-decoration-none position-relative">
                            <i class="bi bi-cart3 fs-5 me-2" style="color: var(--text-color-dark);"></i>
                            <?php if (getCartItemCount() > 0): ?>
                                <span class="position-absolute badge rounded-pill bg-danger" style="
                                    top: -6px;
                                    right: -6px;
                                    font-size: 0.65rem;
                                    padding: 0.25em 0.45em;
                                    line-height: 1;
                                ">
                                    <?php echo getCartItemCount(); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>

                <!-- Desktop Layout: Logo on left -->
                <a class="navbar-brand fw-bold d-none d-lg-block">
                    <img src="assets/img/saisydLogo.png" alt="SAISYD Logo" style="height: 45px;" />
                </a>

                <!-- Navbar Links -->
                <div class="collapse navbar-collapse" id="saisydNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3" id="navbarNav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="bi bi-house"></i> <span>Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">
                                <i class="bi bi-info-circle"></i> <span>About</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#location">
                                <i class="bi bi-geo-alt"></i> <span>Location</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">
                                <i class="bi bi-envelope"></i> <span>Contact</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Desktop: Cart + Menu -->
                    <div class="d-none d-lg-flex align-items-center">
                        <a href="cart.php" class="nav-link position-relative me-2">
                            <i class="bi bi-cart3 fs-4"></i> <span>Cart</span>
                            <?php if (getCartItemCount() > 0): ?>
                                <span class="position-absolute badge rounded-pill bg-danger" style="
                                    top: -2px;
                                    right: -2px;
                                    font-size: 0.75rem;
                                    padding: 0.25em 0.5em;
                                ">
                                    <?php echo getCartItemCount(); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <button class="btn menu-btn" onclick="location.href='menu.php'">
                            <i class="fas fa-mug-hot me-2"></i> Menu
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Body -->
        <div class="container-fluid mt-5">
            <div class="row">
                <div class="col-12">
                    <div
                        class="card coffee-banner border-0 bg-transparent landing-element mx-auto text-white overflow-hidden position-relative">
                        <!-- Background Video -->
                        <video autoplay muted loop playsinline
                            class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover z-n1">
                            <source src="assets/video/bgVideo.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>

                        <!-- Color overlay -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 z-0" style="background: #2e1a00bb">
                        </div>
                        <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-around px-5
                    py-5">
                            <div class="coffee-text me-md-4 text-center text-md-start landing-element">
                                <h1 class="title" style="color: var(--secondary-color)">Saisyd Café: The Hidden Farm
                                </h1>
                                <p class="lead" style="font-family: var(--primaryFont);">Minimalist café that serves
                                    good
                                    food and coffee — perfect for slow mornings, casual catch-ups, and cozy evenings
                                    with
                                    friends and family. Whether you're here to study, unwind, or simply savor the
                                    moment,
                                    Saisyd Café welcomes you with warmth in every cup.</p>
                                <div
                                    class="coffee-buttons d-flex gap-3 mt-3 justify-content-center justify-content-md-start">
                                    <a href="menu.php"
                                        class="btn btn-fill d-flex justify-content-center align-items-center">
                                        <i class="fas fa-mug-hot me-2"></i> Order Now
                                    </a>
                                </div>
                            </div>
                            <div class="coffee-img mt-4 mt-md-0 landing-element">
                                <img src="assets/img/coffee.png" alt="Coffee Cup">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop / Large Screen View -->
            <div class="container my-5 d-none d-lg-block animate__animated animate__fadeIn wow" data-wow-delay="0.1s">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5 animate__animated animate__fadeInUp wow" data-wow-delay="0.1s">
                        <video class="custom-img w-100 rounded" src="assets/video/saisydVideo.mp4" autoplay muted loop
                            playsinline></video>
                    </div>
                    <div class="col-lg-7 animate__animated animate__fadeInUp wow" data-wow-delay="0.17s">
                        <div class="custom-card p-4">
                            <h1 class="title2 text-center">Welcome to Saisyd Café</h1>
                            <p class="lead mt-3 text-dark"
                                style="text-align: justify; font-family: var(--secondaryFont);">
                                At Saisyd Café, we believe that every great moment starts with a comforting sip, a
                                flavorful
                                bite, and a welcoming atmosphere. We've created more than just a place to eat and drink
                                —
                                we've built a cozy haven where you can slow down, breathe, and simply enjoy the present.
                                Whether you're here to study with your favorite latte, spend time with loved ones over a
                                hearty
                                meal, or find a quiet escape with a slice of cake, our doors are always open to you.
                                Each item on
                                our menu is thoughtfully crafted using quality ingredients and a whole lot of care,
                                because we
                                know it's the little things that make a big difference. From early mornings to late
                                evenings,
                                Saisyd Café is here to serve not just food and drinks, but also warmth, community, and a
                                sense
                                of belonging. Come in anytime — your seat is waiting, and the experience is yours to
                                savor.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tablet & Mobile Carousel View -->
            <div id="welcomeCarousel" class="carousel slide d-lg-none my-5 animate__animated animate__fadeIn wow"
                data-wow-delay="0.1s" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <!-- Slide 1: Video -->
                    <div class="carousel-item active" data-bs-interval="10000">
                        <video class="custom-img w-100 rounded" src="assets/video/saisydVideo.mp4" autoplay muted loop
                            playsinline></video>
                    </div>

                    <!-- Slide 2: Text -->
                    <div class="carousel-item" data-bs-interval="10000">
                        <div class="custom-card p-3">
                            <h1 class="title2 text-center">Welcome to Saisyd Café</h1>
                            <p class="lead mt-3 text-dark"
                                style="text-align: justify; font-family: var(--secondaryFont);">
                                At Saisyd Café, we believe that every great moment starts with a comforting sip, a
                                flavorful
                                bite, and a welcoming atmosphere. We've created more than just a place to eat and drink
                                —
                                we've built a cozy haven where you can slow down, breathe, and simply enjoy the present.
                                Whether you're here to study with your favorite latte, spend time with loved ones over a
                                hearty
                                meal, or find a quiet escape with a slice of cake, our doors are always open to you.
                                Each item on
                                our menu is thoughtfully crafted using quality ingredients and a whole lot of care,
                                because we
                                know it's the little things that make a big difference. From early mornings to late
                                evenings,
                                Saisyd Café is here to serve not just food and drinks, but also warmth, community, and a
                                sense
                                of belonging. Come in anytime — your seat is waiting, and the experience is yours to
                                savor.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Carousel controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel"
                    data-bs-slide="prev"
                    style="pointer-events: auto; background-color: var(--primary-color); border: none; border-radius: 50%; width: clamp(40px, 8vw, 55px); height: clamp(40px, 8vw, 55px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease;">
                    <span class="carousel-control-prev-icon"
                        style="filter: brightness(0) invert(1); width: clamp(16px, 3vw, 20px); height: clamp(16px, 3vw, 20px);"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel"
                    data-bs-slide="next"
                    style=" pointer-events: auto; background-color: var(--primary-color); border: none; border-radius: 50%; width: clamp(40px, 8vw, 55px); height: clamp(40px, 8vw, 55px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease;">
                    <span class="carousel-control-next-icon"
                        style="filter: brightness(0) invert(1); width: clamp(16px, 3vw, 20px); height: clamp(16px, 3vw, 20px);"></span>
                </button>
            </div>


            <div class="px-5">
                <div class="w-100"
                    style="height: 2px; background-color: var(--text-color-dark); box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                </div>
            </div>

            <!-- Popular Menu Section -->
            <div class="container my-5 animate__animated animate__fadeIn wow" data-wow-delay="0.1s">
                <div class="menu-container">
                    <!-- Title -->
                    <h2 class="heading menu-title text-center mb-4 animate__animated animate__fadeInDown wow"
                        data-wow-delay="0.1s">
                        POPULAR MENU
                    </h2>

                    <!-- Desktop Grid -->
                    <div class="row row-cols-2 row-cols-md-3 g-4 d-none d-md-flex">
                        <div class="col animate__animated animate__fadeInUp wow" data-wow-delay="0.11s">
                            <div class="popular-item">
                                <img src="assets/img/img-menu/latte.png" alt="Latte">
                                <div class="subheading menu-name1 mt-2">Latte</div>
                            </div>
                        </div>
                        <div class="col animate__animated animate__fadeInUp wow" data-wow-delay="0.12s">
                            <div class="popular-item">
                                <img src="assets/img/img-menu/green_tea.png" alt="Green Tea">
                                <div class="subheading menu-name1 mt-2">Green Tea</div>
                            </div>
                        </div>
                        <div class="col animate__animated animate__fadeInUp wow" data-wow-delay="0.13s">
                            <div class="popular-item">
                                <img src="assets/img/coffee.png" alt="Amerikano">
                                <div class="subheading menu-name1 mt-2">Amerikano</div>
                            </div>
                        </div>
                        <div class="col animate__animated animate__fadeInUp wow" data-wow-delay="0.14s">
                            <div class="popular-item">
                                <img src="assets/img/img-menu/espresso.png" alt="Espresso">
                                <div class="subheading menu-name1 mt-2">Espresso</div>
                            </div>
                        </div>
                        <div class="col animate__animated animate__fadeInUp wow" data-wow-delay="0.15s">
                            <div class="popular-item">
                                <img src="assets/img/img-menu/matcha_latte.png" alt="Matcha Latte">
                                <div class="subheading menu-name1 mt-2">Matcha Latte</div>
                            </div>
                        </div>
                        <div class="col animate__animated animate__fadeInUp wow" data-wow-delay="0.16s">
                            <div class="popular-item">
                                <img src="assets/img/img-menu/mocha.png" alt="Mocha">
                                <div class="subheading menu-name1 mt-2">Mocha</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Horizontal Scroll -->
                    <div class="scroll-container d-md-none px-3 mt-4">
                        <div class="popular-item animate__animated animate__fadeInUp wow" data-wow-delay="0.11s">
                            <img src="assets/img/img-menu/latte.png" alt="Latte">
                            <div class="subheading menu-name1 mt-2">Latte</div>
                        </div>
                        <div class="popular-item animate__animated animate__fadeInUp wow" data-wow-delay="0.12s">
                            <img src="assets/img/img-menu/green_tea.png" alt="Green Tea">
                            <div class="subheading menu-name1 mt-2">Green Tea</div>
                        </div>
                        <div class="popular-item animate__animated animate__fadeInUp wow" data-wow-delay="0.13s">
                            <img src="assets/img/coffee.png" alt="Amerikano">
                            <div class="subheading menu-name1 mt-2">Amerikano</div>
                        </div>
                        <div class="popular-item animate__animated animate__fadeInUp wow" data-wow-delay="0.14s">
                            <img src="assets/img/img-menu/espresso.png" alt="Espresso">
                            <div class="subheading menu-name1 mt-2">Espresso</div>
                        </div>
                        <div class="popular-item animate__animated animate__fadeInUp wow" data-wow-delay="0.15s">
                            <img src="assets/img/img-menu/matcha_latte.png" alt="Matcha Latte">
                            <div class="subheading menu-name1 mt-2">Matcha Latte</div>
                        </div>
                        <div class="popular-item animate__animated animate__fadeInUp wow" data-wow-delay="0.16s">
                            <img src="assets/img/img-menu/mocha.png" alt="Mocha">
                            <div class="subheading menu-name1 mt-2">Mocha</div>
                        </div>
                    </div>
                    <div class="text-center mt-4 animate__animated animate__pulse animate__infinite"
                        data-wow-delay="0.2s">
                        <a href="menu.php" class="see-more-btn">
                            <span>See More</span>
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ABOUT US -->
            <section id="about" class="about-us-section py-5 animate__animated animate__fadeIn wow"
                data-wow-delay="0.1s"
                style="background-image: url('assets/img/samplebg3.jpg'); background-size: cover; background-position: center;">
                <div class="container">
                    <div class="row align-items-center">
                        <!-- SPAGHETTI -->
                        <div class="col-lg-5 mb-4 mb-lg-0 position-relative text-center animate__animated animate__zoomIn wow"
                            data-wow-delay="0.1s">
                            <img src="assets/img/spaghetti.png" alt="Spaghetti"
                                class="img-fluid rounded-circle shadow-lg z-2 position-relative"
                                style="width: 250px; z-index: 2;">
                        </div>

                        <!-- Right Content -->
                        <div class="col-lg-7 animate__animated animate__fadeInUp wow" data-wow-delay="0.2s"
                            style="text-align: justify;">
                            <h2 class="heading fw-bold">About Us</h2>
                            <p class="lead mt-3" style="font-family: var(--secondaryFont);">
                                Saisyd Café began with a simple dream — to create a space where people could slow down,
                                feel
                                at
                                home, and enjoy good food and coffee in a cozy, welcoming setting. Built with passion
                                and
                                inspired by the joy of genuine connections, we opened our doors to offer more than just
                                a
                                menu.
                                Every cup we brew and every dish we serve is crafted with care, using quality
                                ingredients
                                and a
                                heart for hospitality. Whether you're a student looking for a quiet spot, a group of
                                friends
                                catching up, or someone in need of a warm pause in the middle of a busy day, Saisyd Café
                                is
                                here
                                for you. We're not just serving meals — we're building memories, one visit at a time.
                            </p>
                            <p class="lead" style="font-family: var(--secondaryFont);">
                                Over time, our café has become a gathering place for all kinds of moments — from quick
                                coffee
                                runs
                                to long conversations and everything in between. We take pride in being part of your
                                everyday
                                life,
                                serving not just food and drinks, but warmth, comfort, and connection. As we continue to
                                grow,
                                our
                                mission stays the same: to make everyone who walks through our doors feel seen, valued,
                                and
                                right
                                at home here at Saisyd Café.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="background-color: rgba(0, 0, 0, 0.4); z-index: 0;"></div>
            </section>

            <section class="carousel__container animate__animated animate__fadeIn wow" data-wow-delay="0.1s">
                <h2 class="heading2 pb-lg-5 animate__animated animate__fadeInDown wow">INSIDE OF CAFE</h2>

                <!-- Centered Carousel Container -->
                <div class="centered-carousel-container animate__animated animate__fadeIn" data-wow-delay="0.2s">
                    <div class="centered-carousel-wrapper" id="cafeCarouselWrapper">

                        <!-- Slide 1 -->
                        <div class="centered-carousel-slide active">
                            <img src="assets/img/sampleImage3.png" class="carousel-img" alt="Cafe interior 1" />
                            <div class="slide-overlay">
                                <h3>Cozy Seating Area</h3>
                                <p>Perfect spot for studying and conversations</p>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="centered-carousel-slide next">
                            <img src="assets/img/sampleImage4.jpg" class="carousel-img" alt="Cafe interior 2" />
                            <div class="slide-overlay">
                                <h3>Coffee Bar</h3>
                                <p>Where the magic happens</p>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div class="centered-carousel-slide hidden">
                            <img src="assets/img/sampleImage5.jpg" class="carousel-img" alt="Cafe interior 3" />
                            <div class="slide-overlay">
                                <h3>Reading Corner</h3>
                                <p>Quiet space for relaxation</p>
                            </div>
                        </div>

                        <!-- Slide 4 -->
                        <div class="centered-carousel-slide hidden">
                            <img src="assets/img/sampleImage6.jpg" class="carousel-img" alt="Cafe interior 4" />
                            <div class="slide-overlay">
                                <h3>Dining Area</h3>
                                <p>Enjoy your meals in comfort</p>
                            </div>
                        </div>

                        <!-- Slide 1 -->
                        <div class="centered-carousel-slide active">
                            <img src="assets/img/sampleImage3.png" class="carousel-img" alt="Cafe interior 1" />
                            <div class="slide-overlay">
                                <h3>Cozy Seating Area</h3>
                                <p>Perfect spot for studying and conversations</p>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="centered-carousel-slide next">
                            <img src="assets/img/sampleImage4.jpg" class="carousel-img" alt="Cafe interior 2" />
                            <div class="slide-overlay">
                                <h3>Coffee Bar</h3>
                                <p>Where the magic happens</p>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div class="centered-carousel-slide hidden">
                            <img src="assets/img/sampleImage5.jpg" class="carousel-img" alt="Cafe interior 3" />
                            <div class="slide-overlay">
                                <h3>Reading Corner</h3>
                                <p>Quiet space for relaxation</p>
                            </div>
                        </div>

                        <!-- Slide 4 -->
                        <div class="centered-carousel-slide hidden">
                            <img src="assets/img/sampleImage6.jpg" class="carousel-img" alt="Cafe interior 4" />
                            <div class="slide-overlay">
                                <h3>Dining Area</h3>
                                <p>Enjoy your meals in comfort</p>
                            </div>
                        </div>



                    </div>

                    <!-- Navigation buttons -->
                    <div class="carousel-nav-btn carousel-nav-prev" id="cafePrevBtn">
                        <i class="ri-arrow-left-s-line"></i>
                    </div>
                    <div class="carousel-nav-btn carousel-nav-next" id="cafeNextBtn">
                        <i class="ri-arrow-right-s-line"></i>
                    </div>

                    <!-- Pagination dots -->
                    <div class="carousel-pagination" id="cafePagination"></div>
                </div>

                <!-- Keep your existing description paragraph -->
                <p class="lead2 animate__animated animate__fadeInUp wow" data-wow-delay="0.1s"
                    style="text-align: justify;">
                    The moment you walk into Saisyd Café, you're greeted with the soothing aroma of freshly brewed
                    coffee and a warm, inviting atmosphere. With soft lighting, comfortable seating, and a thoughtfully
                    designed
                    interior, our space is made for calm conversations, quiet focus, and cozy hangouts. Whether you're
                    settling in
                    for a solo study session or enjoying time with friends, the environment inside Saisyd Café is
                    designed to
                    make you feel right at home.
                </p>
            </section>

            <div class="px-5">
                <div class="w-100"
                    style="height: 2px; background-color: var(--text-color-dark); box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                </div>
            </div>

            <!-- LOCATION -->
            <section id="location" class="location-section pt-4 pb-5">
                <div class="container">
                    <div class="heading2 heading h3 mb-3 text-center animate__animated animate__fadeInDown wow">
                        LOCATION
                    </div>

                    <!-- Branch Nav -->
                    <div class="branch-nav text-center mb-5 animate__animated animate__fadeInDown wow"
                        data-wow-delay="0.1s">
                        <a href="#" id="link-suplang" onclick="showBranch(event, 'suplang')" class="active">Suplang
                            Branch</a> |
                        <a href="#" id="link-santor" onclick="showBranch(event, 'santor')">Santor Branch</a>
                    </div>

                    <!-- Suplang Branch -->
                    <div id="branch-suplang" class="branch-content animate__animated animate__fadeInUp wow">
                        <div class="row g-4">
                            <!-- Branch Image -->
                            <div class="col-lg-5">
                                <div class="location-img-wrapper">
                                    <img src="assets/img/sampleImage5.jpg" alt="Suplang Branch" class="location-img">
                                </div>
                            </div>

                            <!-- Branch Details -->
                            <div class="col-lg-7">
                                <div class="branch-details-card">
                                    <h3 class="branch-title mb-3">Brgy. Suplang</h3>

                                    <div class="branch-address mb-4">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <span>Sitio Mistica Purok 5, Tanauan City Batangas<br>
                                            <small class="text-muted">Located at AEJ Gamefarm</small></span>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Hours Card -->
                                        <div class="col-md-6">
                                            <div class="info-card hours-card">
                                                <div class="info-card-icon">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <h5 class="info-card-title">Operating Hours</h5>
                                                <div class="hours-details">
                                                    <div class="hour-row">
                                                        <span class="day">Monday - Sunday</span>
                                                        <span class="time">12:00 NN - 8:00 PM</span>
                                                    </div>
                                                    <div class="hour-row closed">
                                                        <span class="day">Every Tuesday</span>
                                                        <span class="time">CLOSED</span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Map Card -->
                                        <div class="col-md-6">
                                            <div class="info-card map-card">
                                                <div class="info-card-icon">
                                                    <i class="fas fa-location-dot"></i>
                                                </div>
                                                <h5 class="info-card-title">Find Us</h5>
                                                <div class="map-preview">
                                                    <img src="assets/img/sampleImage7.jpg" alt="Map"
                                                        class="img-fluid rounded mb-3">
                                                </div>
                                                <button class="btn-location w-100"
                                                    onclick="window.open('https://maps.app.goo.gl/VSnnLPFNB5ofu9Ky8', '_blank')">
                                                    <i class="fas fa-directions me-2"></i>Get Directions
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Santor Branch -->
                    <div id="branch-santor" class="branch-content d-none animate__animated animate__fadeInUp wow">
                        <div class="row g-4">
                            <!-- Branch Image -->
                            <div class="col-lg-5">
                                <div class="location-img-wrapper">
                                    <img src="assets/img/sampleImage4.jpg" alt="Santor Branch" class="location-img">
                                </div>
                            </div>

                            <!-- Branch Details -->
                            <div class="col-lg-7">
                                <div class="branch-details-card">
                                    <h3 class="branch-title mb-3">Brgy. Santor</h3>

                                    <div class="branch-address mb-4">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <span>Purok 2, Brgy. Santor, Tanauan City Batangas</span>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Hours Card -->
                                        <div class="col-md-6">
                                            <div class="info-card hours-card">
                                                <div class="info-card-icon">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <h5 class="info-card-title">Operating Hours</h5>
                                                <div class="hours-details">
                                                    <div class="hour-row">
                                                        <span class="day">Open Daily</span>
                                                        <span class="time">1:00 PM - 9:00 PM</span>
                                                    </div>
                                                </div>
                                                <div class="open-badge">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Open Every Day
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Map Card -->
                                        <div class="col-md-6">
                                            <div class="info-card map-card">
                                                <div class="info-card-icon">
                                                    <i class="fas fa-location-dot"></i>
                                                </div>
                                                <h5 class="info-card-title">Find Us</h5>
                                                <div class="map-preview">
                                                    <img src="assets/img/sampleImage7.jpg" alt="Map"
                                                        class="img-fluid rounded mb-3">
                                                </div>
                                                <button class="btn-location w-100"
                                                    onclick="window.open('https://maps.app.goo.gl/pK8bs5HG3mU7m51AA', '_blank')">
                                                    <i class="fas fa-directions me-2"></i>Get Directions
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- SIMPLIFIED PAYMENT -->
            <section class="py-5 text-center simplified-payment-section position-relative"
                style="background-image: url('assets/img/samplebg5.jpg'); background-size: cover; background-position: center; color:var(--card-bg-color)">
                <div class="container position-relative z-2">
                    <h2 class="heading2 heading mb-5 animate__animated animate__fadeInDown wow" data-wow-delay="0.1s">
                        SIMPLIFIED PAYMENT</h2>

                    <!-- Desktop View -->
                    <div class="row d-none d-md-flex">
                        <div class="col-md-4 mb-4 animate__animated animate__fadeInUp wow" data-wow-delay="0.2s">
                            <div class="mb-3 fs-1">
                                <img src="assets/img/light.png" alt="Flashlight Icon" style="height: 3em;">
                            </div>
                            <h5 class="fw-bold subheading2">Fast Transactions</h5>
                            <p class="h6">Enjoy quick and seamless payment processing for a hassle-free experience.</p>
                        </div>
                        <div class="col-md-4 mb-4 animate__animated animate__fadeInUp wow" data-wow-delay="0.3s">
                            <div class="mb-3 fs-1">
                                <img src="assets/img/lock.png" alt="Lock Icon" style="height: 3em;">
                            </div>
                            <h5 class="fw-bold subheading2">Secure Payments</h5>
                            <p class="h6">Your data is protected with advanced security measures for worry-free
                                transactions.</p>
                        </div>
                        <div class="col-md-4 mb-4 animate__animated animate__fadeInUp wow" data-wow-delay="0.4s">
                            <div class="mb-3 fs-1">
                                <img src="assets/img/human.png" alt="User Icon" style="height: 3em;">
                            </div>
                            <h5 class="fw-bold subheading2">User Convenience</h5>
                            <p class="h6">Simplified payment options designed to suit your needs and preferences.</p>
                        </div>
                    </div>

                    <!-- Mobile Carousel -->
                    <div id="paymentCarousel" class="carousel slide d-md-none animate__animated animate__fadeInUp wow"
                        data-bs-ride="carousel" data-wow-delay="0.2s">
                        <div class="carousel-inner">
                            <div class="carousel-item active text-center" data-bs-interval="6000">
                                <div class="mb-3 fs-1">
                                    <img src="assets/img/light.png" alt="Flashlight Icon" style="height: 3em;">
                                </div>
                                <h5 class="fw-bold subheading2">Fast Transactions</h5>
                                <p class="h6">Enjoy quick and seamless payment processing for a hassle-free experience.
                                </p>
                            </div>
                            <div class="carousel-item text-center" data-bs-interval="6000">
                                <div class="mb-3 fs-1">
                                    <img src="assets/img/lock.png" alt="Lock Icon" style="height: 3em;">
                                </div>
                                <h5 class="fw-bold subheading2">Secure Payments</h5>
                                <p class="h6">Your data is protected with advanced security measures for worry-free
                                    transactions.</p>
                            </div>
                            <div class="carousel-item text-center" data-bs-interval="6000">
                                <div class="mb-3 fs-1">
                                    <img src="assets/img/human.png" alt="User Icon" style="height: 3em;">
                                </div>
                                <h5 class="fw-bold subheading2">User Convenience</h5>
                                <p class="h6">Simplified payment options designed to suit your needs and preferences.
                                </p>
                            </div>
                        </div>

                        <!-- Carousel Controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#paymentCarousel"
                            data-bs-slide="prev" style="opacity: 0; pointer-events: auto;">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#paymentCarousel"
                            data-bs-slide="next" style="opacity: 0; pointer-events: auto;">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="background-color: rgba(0, 0, 0, 0.5); z-index: 1;"></div>
            </section>

            <!-- CONTACT & CONNECT SECTION - REPLACE the "Connect With Saisyd Café Online" section -->
            <section id="contact" class="pt-5 pb-1 text-center">
                <div class="container">
                    <!-- Main Title -->
                    <h2 class="heading payment-h2 fw-bold animate__animated animate__fadeInDown wow"
                        data-wow-delay="0.1s">
                        Contact & Connect With Us
                    </h2>
                    <p class="lead payment-p animate__animated animate__fadeInUp wow" data-wow-delay="0.2s">
                        Stay in the loop with our latest drinks, promos, and cozy café moments. Reach out anytime!
                    </p>

                    <!-- Contact Info Cards -->
                    <div class="row g-4 justify-content-center mt-4 mb-5">
                        <!-- Phone Card -->
                        <div class="col-lg-3 col-md-4 col-sm-6 animate__animated animate__fadeInUp wow"
                            data-wow-delay="0.25s">
                            <div class="contact-mini-card">
                                <div class="contact-mini-icon">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <h6 class="fw-bold mt-3 mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">Call Us</h6>
                                <p class="small mb-1" style="color: var(--text-color-dark);">+63 912 345 6789</p>
                                <p class="small mb-0" style="color: var(--text-color-dark);">+63 998 765 4321</p>
                            </div>
                        </div>

                        <!-- Email Card -->
                        <div class="col-lg-3 col-md-4 col-sm-6 animate__animated animate__fadeInUp wow"
                            data-wow-delay="0.3s">
                            <div class="contact-mini-card">
                                <div class="contact-mini-icon">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <h6 class="fw-bold mt-3 mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">Email Us
                                </h6>
                                <p class="small mb-0" style="color: var(--text-color-dark);">saisydcafe@gmail.com</p>
                            </div>
                        </div>

                        <!-- Location Card -->
                        <div class="col-lg-3 col-md-4 col-sm-6 animate__animated animate__fadeInUp wow"
                            data-wow-delay="0.35s">
                            <div class="contact-mini-card">
                                <div class="contact-mini-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <h6 class="fw-bold mt-3 mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">Visit Us
                                </h6>
                                <p class="small mb-1" style="color: var(--text-color-dark);"><strong>Suplang:</strong>
                                    Tanauan City</p>
                                <p class="small mb-0" style="color: var(--text-color-dark);"><strong>Santor:</strong>
                                    Tanauan City</p>
                            </div>
                        </div>



                        <!-- Social Media Section (Desktop View) -->
                        <div class="row justify-content-center mt-5 d-none d-md-flex">
                            <div class="col-6 col-md-3 mb-4 animate__animated animate__fadeInUp wow"
                                data-wow-delay="0.45s">
                                <a href="https://www.instagram.com/saisydcafe?utm_source=ig_web_button_share_sheet&igsh=MTY4eXNldmtzejk3NA=="
                                    target="_blank" class="social-circle instagram">
                                    <i class="fab fa-instagram fa-2x"></i>
                                </a>
                                <p class="lead more-p mt-3">Follow us on Instagram</p>
                            </div>
                            <div class="col-6 col-md-3 mb-4 animate__animated animate__fadeInUp wow"
                                data-wow-delay="0.5s">
                                <a href="https://www.facebook.com/saisydcafethehiddenfarm" target="_blank"
                                    class="social-circle facebook">
                                    <i class="fab fa-facebook-f fa-2x"></i>
                                </a>
                                <p class="lead more-p mt-3">Like us on Facebook</p>
                            </div>
                            <div class="col-6 col-md-3 mb-4 animate__animated animate__fadeInUp wow"
                                data-wow-delay="0.55s">
                                <a href="https://www.tiktok.com/@saisyd.cafe?is_from_webapp=1&sender_device=pc"
                                    target="_blank" class="social-circle tiktok">
                                    <i class="fab fa-tiktok fa-2x"></i>
                                </a>
                                <p class="lead more-p mt-3">Watch on TikTok</p>
                            </div>
                        </div>

                        <!-- Social Media Section (Mobile Carousel) -->
                        <div id="connectCarousel"
                            class="carousel slide d-md-none mt-5 animate__animated animate__fadeInUp wow"
                            data-bs-ride="carousel" data-wow-delay="0.45s">
                            <div class="carousel-inner text-center">
                                <div class="carousel-item active" data-bs-interval="5000">
                                    <a href="https://www.instagram.com/saisydcafe?utm_source=ig_web_button_share_sheet&igsh=MTY4eXNldmtzejk3NA=="
                                        target="_blank" class="social-circle instagram">
                                        <i class="fab fa-instagram fa-2x"></i>
                                    </a>
                                    <p class="lead more-p mt-3">Follow us on Instagram</p>
                                </div>
                                <div class="carousel-item" data-bs-interval="4000">
                                    <a href="https://www.facebook.com/saisydcafethehiddenfarm" target="_blank"
                                        class="social-circle facebook">
                                        <i class="fab fa-facebook-f fa-2x"></i>
                                    </a>
                                    <p class="lead more-p mt-3">Like us on Facebook</p>
                                </div>
                                <div class="carousel-item" data-bs-interval="4000">
                                    <a href="https://www.tiktok.com/@saisyd.cafe?is_from_webapp=1&sender_device=pc"
                                        target="_blank" class="social-circle tiktok">
                                        <i class="fab fa-tiktok fa-2x"></i>
                                    </a>
                                    <p class="lead more-p mt-3">Watch on TikTok</p>
                                </div>
                            </div>

                            <!-- Carousel Controls -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#connectCarousel"
                                data-bs-slide="prev" style="opacity: 0; pointer-events: auto;">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#connectCarousel"
                                data-bs-slide="next" style="opacity: 0; pointer-events: auto;">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>
            </section>

            <div class="px-5 animate__animated animate__fadeIn wow" data-wow-delay="0.2s">
                <div class="w-100"
                    style="height: 2px; background-color: var(--text-color-dark); box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                </div>
            </div>

            <!-- Google Reviews & Feedback Section -->
            <section class="py-5 position-relative text-center mt-5"
                style="background: url('assets/img/samplebg.jpg') center/cover no-repeat; color:var(--card-bg-color);">
                <div class="container position-relative z-2">
                    <h2 class="heading2 heading fw-bold mb-4 animate__animated animate__fadeInDown wow"
                        data-wow-delay="0.1s">
                        What Our Customers Say
                    </h2>
                    <p class="lead mb-5 animate__animated animate__fadeInUp wow" data-wow-delay="0.15s">
                        Real reviews from our Google Business Profile
                    </p>

                    <!-- Google Reviews Embed -->
                    <div class="row justify-content-center animate__animated animate__fadeInUp wow"
                        data-wow-delay="0.2s">
                        <div class="col-lg-10">
                            <div class="google-reviews-container p-4 rounded-4 shadow"
                                style="background-color: rgba(255, 255, 255, 0.95); min-height: 400px;">

                                <!-- Elfsight Widget  -->

                                <div class="elfsight-wrapper">

                                    <script
                                        src="https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/4.2.10/iframeResizer.min.js"></script>
                                    <iframe onload="iFrameResize(this)" src="https://62bd0c75c9a34489b26278d71cf40cfe.elf.site"
                                        style="border:none;width:100%;"></iframe>
                                </div>

                                <!-- Link to Google Reviews -->
                                <div class="text-center mt-3">
                                    <a href="https://www.google.com/search?sca_esv=76b1d1510d0b1bcc&sxsrf=AE3TifNsCqtRWIRoVQcEkf13eogDNkSdQg:1760461641771&si=AMgyJEtREmoPL4P1I5IDCfuA8gybfVI2d5Uj7QMwYCZHKDZ-E8NeGm16Wf5iLjUdHkTC6DS7dvpne5IUFtWL0q84c6MYqJeUTT2YZx0qDLDT7Dmd8ZrVq4IeLzOJWkdelOfftPLAYP6fYBLuLSgoULKlATlYfig15Q%3D%3D&q=Saisyd+Caf%C3%A9:+The+Hidden+Farm+Reviews&sa=X&ved=2ahUKEwjJubHWlqSQAxX9b_UHHR4iCWYQ0bkNegQIIxAE&cshid=1760461720250268&biw=1745&bih=859&dpr=1.1"
                                        target="_blank" class="btn btn-fill">
                                        <i class="fab fa-google me-2"></i>Read All Reviews on Google
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Testimonials  -->
                    <div class="mt-5">
                        <h4 class="fw-bold mb-4 animate__animated animate__fadeInDown wow" style="font-family: var(--primaryFont);
                        font-weight: 600;" data-wow-delay="0.3s">
                            Featured Reviews
                        </h4>

                        <!-- Desktop View -->
                        <div class="row justify-content-center g-3 d-none d-md-flex">
                            <div class="col-md-4 animate__animated animate__fadeIn wow" data-wow-delay="0.4s">
                                <div class="p-3 rounded-4 shadow"
                                    style="background-color: var(--gray); color: var(--text-color-light); height: 170px;">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="assets/img/louis.jpg" alt="User" class="rounded-circle me-3"
                                            style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-bold"
                                                style="text-align: justify; font-size: var(--h6); font-family: var(--primaryFont);">
                                                Louis Santos </h6>
                                        </div>
                                    </div>
                                    <p class="mb-0"
                                        style="text-align: justify; font-family: var(--primaryFont); font-size: var(--h6);">
                                        Saisyd Café is my favorite spot to study and unwind. Their drinks never
                                        disappoint,
                                        and
                                        the cozy vibe always helps me focus.</p>
                                </div>
                            </div>

                            <div class="col-md-4 animate__animated animate__fadeIn wow" data-wow-delay="0.5s">
                                <div class="p-3 rounded-4 shadow"
                                    style="background-color: var(--gray); color: var(--text-color-light); height: 170px;">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="assets/img/thea.jpg" alt="User" class="rounded-circle me-3"
                                            style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-bold"
                                                style="text-align: justify; font-size: var(--h6); font-family: var(--primaryFont);">
                                                Ma. Althea R Alberto</h6>
                                        </div>
                                    </div>
                                    <p class="mb-0"
                                        style="text-align: justify; font-family: var(--primaryFont); font-size: var(--h6);">
                                        Saisyd Café makes the perfect matcha! It's my favorite spot to chill and
                                        brainstorm
                                        ideas with friends.</p>
                                </div>
                            </div>

                            <div class="col-md-4 animate__animated animate__fadeIn wow" data-wow-delay="0.6s">
                                <div class="p-3 rounded-4 shadow"
                                    style="background-color: var(--gray); color: var(--text-color-light); height: 170px;">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="assets/img/brandon.jpg" alt="User" class="rounded-circle me-3"
                                            style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-bold"
                                                style="text-align: justify; font-size: var(--h6); font-family: var(--primaryFont);">
                                                Brandon Mauricio </h6>
                                        </div>
                                    </div>
                                    <p class="mb-0"
                                        style="text-align: justify; font-family: var(--primaryFont); font-size: var(--h6);">
                                        Their coffee and the vibe always help me stay focused during hectic weeks.
                                        Highly
                                        recommended!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Carousel -->
                        <div id="feedbackCarousel"
                            class="carousel slide d-md-none animate__animated animate__fadeInUp wow"
                            data-wow-delay="0.4s" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active" data-bs-interval="8000">
                                    <div class="p-3 rounded-4 shadow"
                                        style="background-color: var(--gray); color: var(--text-color-light); height: 170px;">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="assets/img/louis.jpg" alt="User" class="rounded-circle me-3"
                                                style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold"
                                                    style="text-align: justify; font-size: var(--h6); font-family: var(--primaryFont);">
                                                    Louis Santos </h6>
                                            </div>
                                        </div>
                                        <p class="mb-0"
                                            style="text-align: justify; font-family: var(--primaryFont); font-size: var(--h6);">
                                            Saisyd Café is my favorite spot to study and unwind. Their drinks never
                                            disappoint,and the cozy vibe always helps me focus.
                                        </p>
                                    </div>
                                </div>

                                <div class="carousel-item" data-bs-interval="8000">
                                    <div class="p-3 rounded-4 shadow"
                                        style="background-color: var(--gray); color: var(--text-color-light); height: 170px;">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="assets/img/thea.jpg" alt="User" class="rounded-circle me-3"
                                                style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold"
                                                    style="text-align: justify; font-size: var(--h6); font-family: var(--primaryFont);">
                                                    Ma. Althea R Alberto </h6>
                                            </div>
                                        </div>
                                        <p class="mb-0"
                                            style="text-align: justify; font-family: var(--primaryFont); font-size: var(--h6);">
                                            Saisyd Café makes the perfect matcha! It's my favorite spot to chill and
                                            brainstorm
                                            ideas with friends.
                                        </p>
                                    </div>
                                </div>

                                <div class="carousel-item" data-bs-interval="8000">
                                    <div class="p-3 rounded-4 shadow"
                                        style="background-color: var(--gray); color: var(--text-color-light); height: 170px;">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="assets/img/brandon.jpg" alt="User" class="rounded-circle me-3"
                                                style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold"
                                                    style="text-align: justify; font-size: var(--h6); font-family: var(--primaryFont);">
                                                    Brandon Mauricio
                                                </h6>
                                            </div>
                                        </div>
                                        <p class="mb-0"
                                            style=" text-align: justify; font-family: var(--primaryFont); font-size: var(--h6);">
                                            Their coffee and the vibe always help me stay focused during hectic weeks.
                                            Highly
                                            recommended!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#feedbackCarousel"
                            data-bs-slide="prev" style="opacity: 0; pointer-events: auto;">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#feedbackCarousel"
                            data-bs-slide="next" style="opacity: 0; pointer-events: auto;">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="background-color: rgba(0, 0, 0, 0.6); z-index: 1;"></div>
            </section>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-footer text-dark pt-5 pb-3">
        <div class="container">
            <div class="d-lg-none accordion" id="footerAccordion">
                <!-- SAISYD -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseSaisyd">
                            SAISYD
                        </button>
                    </h2>
                    <div id="collapseSaisyd" class="accordion-collapse collapse show">
                        <div class="accordion-body small" style="text-align: justify;">
                            Minimalist café that serves good
                            food and coffee — perfect for slow mornings, casual catch-ups, and cozy evenings with
                            friends and family. Whether you're here to study, unwind, or simply savor the moment,
                            Saisyd Café welcomes you with warmth in every cup.
                        </div>
                    </div>
                </div>

                <!-- MarketPlace -->
                <div class="accordion-item border-0" style="text-align: justify;">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseMarket">
                            Market Place
                        </button>
                    </h2>
                    <div id="collapseMarket" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ul class="list-unstyled mb-0">
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Services</a></li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Products</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Company -->
                <div class="accordion-item border-0" style="text-align: justify;">
                    <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseCompany">
                        Company
                    </button>
                    </h2>
                    <div id="collapseCompany" class="accordion-collapse collapse">
                        <div class="accordion-body" style="text-align: justify;">
                            <ul class="list-unstyled mb-0">
                                <li><a href="#" class="text-dark text-decoration-none footer-link">About Serve
                                        It</a>
                                </li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Help Center</a>
                                </li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Contact Us</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Follow Us -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFollow">
                            Follow Us
                        </button>
                    </h2>
                    <div id="collapseFollow" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <p class="mb-1">
                                <a href="https://www.tiktok.com/@saisyd.cafe?is_from_webapp=1&sender_device=pc"
                                    target="_blank" class="text-dark text-decoration-none footer-link">
                                    <i class="fab fa-tiktok me-2"></i>SAISYD
                                </a>
                            </p>
                            <p class="mb-1">
                                <a href="#" class="text-dark text-decoration-none footer-link">
                                    <i class="fab fa-facebook-f me-2"></i>Facebook
                                </a>
                            </p>
                            <p class="mb-1">
                                <a href="#" class="text-dark text-decoration-none footer-link">
                                    <i class="fab fa-google me-2"></i>Google
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop View (visible on large screens and up) -->
            <div class="row gy-4 d-none d-lg-flex">
                <div class="col-lg-4" style="text-align: justify;">
                    <h5 class="fw-bold">SAISYD</h5>
                    <p class="small">
                        Minimalist café that serves good
                        food and coffee — perfect for slow mornings, casual catch-ups, and cozy evenings with
                        friends and family. Whether you're here to study, unwind, or simply savor the moment,
                        Saisyd Café welcomes you with warmth in every cup.
                    </p>
                </div>
                <div class="col-lg-2" style="text-align: justify;">
                    <h6 class="fw-bold">Market Place</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Services</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Products</a></li>
                    </ul>
                </div>
                <div class="col-lg-2" style="text-align: justify;">
                    <h6 class="fw-bold">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark text-decoration-none footer-link">About Serve It</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Help Center</a></li>
                        <li><a href="#contact" class="text-dark text-decoration-none footer-link">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <h6 class="fw-bold">FOLLOW US</h6>
                    <p class="mb-1">
                        <a href="https://www.tiktok.com/@saisyd.cafe?is_from_webapp=1&sender_device=pc" target="_blank"
                            class="text-dark text-decoration-none footer-link">
                            <i class="fab fa-tiktok me-2"></i>SAISYD
                        </a>
                    </p>

                </div>
            </div>

            <!-- Footer Bottom -->
            <div
                class="border-top mt-4 pt-3 d-flex justify-content-between align-items-center flex-wrap flex-column flex-lg-row text-center text-lg-start">
                <p class="lead mb-0 small">
                    © 2024 Copyright:
                    <span class="fw-bold d-block d-lg-inline">SAISYD CAFE</span>
                </p>

                <div class="d-none d-lg-flex gap-3 fs-5">
                    <a href="#" class="text-dark"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-google"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <button id="backToTop" class="back-to-top-btn">
        <i class="fas fa-arrow-up"></i>
    </button>


    <!-- Scripts in correct order -->
    <script src="assets/js/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/navbar.js"></script>
</body>

</html>