<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Airlyft Travel</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="panel.css" />
</head>

<body>

    <div class="panel">
        <div class="bg-blur"></div>

        <div class="scroll-wrapper">
            <header class="header-bar">
                <div class="header-left">
                    <img src="AirlyftGallery/LOGO.png" alt="Airlyft Logo" class="logo">
                    <div class="header-title">Airlyft Travel</div>
                </div>
                <nav class="navbar">
                    <ul>
                        <li class="active"><a href="panel.php"><i class='bx bxs-home'></i> Home</a></li>
                        <li><a href="#about"><i class='bx bxs-user-detail'></i> About Us</a></li>
                        <li><a href="#contacts"><i class='bx bxs-phone'></i> Contacts</a></li>
                        <li><a href="/airlyft/airlyft_login/login.php"><i class='bx bxs-user-plus'></i> Login</a></li>
                    </ul>
                </nav>
            </header>

            <div class="image-scroll-container">
                <div class="hero-section">
                    <video autoplay loop muted playsinline class="hero-video">
                        <source src="../AirlyftGallery\montage.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="hero-content">
                        <h1>Your Journey to Paradise</h1>
                        <p>Experience unparalleled luxury and exclusive destinations</p>
                        <a href="#destination-grid-section" class="hero-btn">Explore Destinations</a>
                    </div>
                </div>

                <h2 id="destination-grid-section" class="section-title">Our Curated Destinations</h2>
                <div class="destination-grid">
                    <a href="https://www.aman.com/resorts/amanpulo" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/amanpulo.png" alt="Amanpulo Resort">
                        <div class="card-content">
                            <h3>Amanpulo</h3>
                            <p>Secluded Luxury in the Philippines</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://balesin.com/island/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/balesin.png" alt="Balesin Island">
                        <div class="card-content">
                            <h3>Balesin Island</h3>
                            <p>Exclusive Island Getaway</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://www.amoritaresort.com/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/amorita.png" alt="Amorita Resort">
                        <div class="card-content">
                            <h3>Amorita Resort</h3>
                            <p>Bohol's Cliffside Sanctuary</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://huma-island-resort-spa.coronhotelsonline.com/en/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/huma.png" alt="Huma Island Resort">
                        <div class="card-content">
                            <h3>Huma Island Resort</h3>
                            <p>Overwater Villas in Coron</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://www.oyster.com/philippines/hotels/el-nido-resorts-apulit-island/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/elnido.png" alt="El Nido Resorts">
                        <div class="card-content">
                            <h3>El Nido Resorts</h3>
                            <p>Palawan's Pristine Beauty</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://www.banwaprivateisland.com/island" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/banwa.png" alt="Banwa Private Island">
                        <div class="card-content">
                            <h3>Banwa Private Island</h3>
                            <p>Ultimate Exclusive Retreat</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://naypaladhideaway.com/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/naypalad.png" alt="Nay Palad Island Resort">
                        <div class="card-content">
                            <h3>Nay Palad</h3>
                            <p>Barefoot Luxury in Siargao</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://alphaland.com.ph/baguiomountainlodges/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/alphaland.png" alt="Alphaland Baguio Mountain Lodges">
                        <div class="card-content">
                            <h3>Alphaland Baguio</h3>
                            <p>Mountain Serenity in Baguio</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://www.shangri-la.com/boracay/boracayresort/about/local-guide/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/shangrila.png" alt="Shangri-La Boracay">
                        <div class="card-content">
                            <h3>Shangri-La Boracay</h3>
                            <p>Boracay's Premier Resort</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://www.thefarmatsanbenito.com/villas/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/thefarmbenito.png" alt="Farm at San Benito Lipa">
                        <div class="card-content">
                            <h3>Farm San Benito</h3>
                            <p>Holistic Wellness Retreat</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>

                    <a href="https://www.aureohotels.com/" target="_blank" rel="noopener noreferrer" class="destination-card">
                        <img src="AirlyftGallery/aureolu.png" alt="Aureo La Union">
                        <div class="card-content">
                            <h3>Aureo La Union</h3>
                            <p>Coastal Charm in La Union</p>
                            <span class="card-btn">View Details</span>
                        </div>
                    </a>
                </div>

                <h2 class="section-title">Our Luxury Fleet</h2>
                <div class="aircraft-grid">
                    <div class="aircraft-card" onclick="openLightbox('cessna')">
                        <img src="AirlyftGallery/cessna01/CESSNA 206-1.png" alt="CESSNA 206">
                        <div class="card-content">
                            <h3>Cessna 206</h3>
                            <p>5 Passengers</p>
                            <span class="card-btn">View Photos</span>
                        </div>
                    </div>

                    <div class="aircraft-card" onclick="openLightbox('caravan')">
                        <img src="AirlyftGallery/cessna02/CESSNA GRAND CARAVAN EX-1.png" alt="CESSNA GRAND CARAVAN EX">
                        <div class="card-content">
                            <h3>Cessna G-Caravan EX</h3>
                            <p>10 Passengers</p>
                            <span class="card-btn">View Photos</span>
                        </div>
                    </div>

                    <div class="aircraft-card" onclick="openLightbox('Airbus')">
                        <img src="AirlyftGallery/helicopter01/Airbus H160-1.png" alt="Airbus H160">
                        <div class="card-content">
                            <h3>Airbus H160</h3>
                            <p>8 Passengers</p>
                            <span class="card-btn">View Photos</span>
                        </div>
                    </div>

                    <div class="aircraft-card" onclick="openLightbox('Sikorsky')">
                        <img src="AirlyftGallery/helicopter02/Sikorsky S-76D-1.png" alt="Sikorsky S-76D">
                        <div class="card-content">
                            <h3>Sikorsky S-76D</h3>
                            <p>6 Passengers</p>
                            <span class="card-btn">View Photos</span>
                        </div>
                    </div>
                </div>

<a href="/airlyft/airlyft_login/login.php" class="book-now-link">
    <button class="book-now-btn">BOOK YOUR LUXURY JOURNEY NOW!</button>
</a>            
</div>

            <div id="lightbox-cessna" class="lightbox">
                <span class="lightbox-close" onclick="closeLightbox('cessna')">&#10006;</span>
                <img id="lightbox-img-cessna" src="" alt="CESSNA 206 Full view" />
                <div class="lightbox-controls">
                    <button class="lightbox-arrow" onclick="prevImage('cessna')">&lt;</button>
                    <button class="lightbox-arrow" onclick="nextImage('cessna')">&gt;</button>
                </div>
            </div>

            <div id="lightbox-caravan" class="lightbox">
                <span class="lightbox-close" onclick="closeLightbox('caravan')">&#10006;</span>
                <img id="lightbox-img-caravan" src="" alt="CESSNA GRAND CARAVAN EX Full view" />
                <div class="lightbox-controls">
                    <button class="lightbox-arrow" onclick="prevImage('caravan')">&lt;</button>
                    <button class="lightbox-arrow" onclick="nextImage('caravan')">&gt;</button>
                </div>
            </div>

            <div id="lightbox-Airbus" class="lightbox">
                <span class="lightbox-close" onclick="closeLightbox('Airbus')">&#10006;</span>
                <img id="lightbox-img-Airbus" src="" alt="Airbus H160 Full view" />
                <div class="lightbox-controls">
                    <button class="lightbox-arrow" onclick="prevImage('Airbus')">&lt;</button>
                    <button class="lightbox-arrow" onclick="nextImage('Airbus')">&gt;</button>
                </div>
            </div>

            <div id="lightbox-Sikorsky" class="lightbox">
                <span class="lightbox-close" onclick="closeLightbox('Sikorsky')">&#10006;</span>
                <img id="lightbox-img-Sikorsky" src="" alt="Sikorsky S-76D Full view" />
                <div class="lightbox-controls">
                    <button class="lightbox-arrow" onclick="prevImage('Sikorsky')">&lt;</button>
                    <button class="lightbox-arrow" onclick="nextImage('Sikorsky')">&gt;</button>
                </div>
            </div>


            <section class="about-section" id="about">
                <div class="about-container">
                    <h2>About Us</h2>
                    <p>
                        At <strong>Airlyft Luxury Destinations</strong>, we believe that travel isn't just about the destination—it's about the journey, the experience, and the unforgettable moments you collect along the way.
                    </p>
                    <p>
                        Our mission is to redefine luxury travel in the Philippines by connecting discerning travelers to the most exclusive island escapes, serene mountain hideaways, and opulent wellness sanctuaries.
                    </p>
                    <p>
                        With our seamless airlift booking service, curated destination gallery, and commitment to exceptional service, we make planning your dream getaway effortless. Whether you're chasing sunsets on Amanpulo, rejuvenating at The Farm in San Benito, or exploring the hidden gems of La Union, Airlyft takes you there—in style.
                    </p>
                    <p>
                        Let us be your wings to paradise.
                    </p>
                </div>
            </section>

            <div class="contact-section" id="contacts">
                <img src="AirlyftGallery/LOGO.png" alt="Airlyft Logo" class="contact-logo">
                <div class="contact-info">
                    <h2>Contact Us</h2>
                    <p>Email: Airlyft_Support@gmail.com</p>
                    <p>Phone: +123 456 7890</p>

            <footer>© 2025 Airlyft Travel Co. All rights reserved.</footer>

    <script>
        // Lightbox JavaScript (Original - no changes needed for this part)
        const aircraftImages = {
            cessna: [
                'AirlyftGallery/cessna01/CESSNA 206-1.png',
                'AirlyftGallery/cessna01/CESSNA 206-2.png',
                'AirlyftGallery/cessna01/CESSNA 206-3.png'
            ],
            caravan: [
                'AirlyftGallery/cessna02/CESSNA GRAND CARAVAN EX-1.png',
                'AirlyftGallery/cessna02/CESSNA GRAND CARAVAN EX-2.png',
                'AirlyftGallery/cessna02/CESSNA GRAND CARAVAN EX-3.png'
            ],
            Airbus: [
                'AirlyftGallery/helicopter01/Airbus H160-1.png',
                'AirlyftGallery/helicopter01/Airbus H160-2.png',
                'AirlyftGallery/helicopter01/Airbus H160-3.png',
                'AirlyftGallery/helicopter01/Airbus H160-4.png'
            ],
            Sikorsky: [
                'AirlyftGallery/helicopter02/Sikorsky S-76D-1.png',
                'AirlyftGallery/helicopter02/Sikorsky S-76D-2.png',
                'AirlyftGallery/helicopter02/Sikorsky S-76D-3.png',
                'AirlyftGallery/helicopter02/Sikorsky S-76D-4.png'
            ]
        };

        let currentIndex = {};

        function openLightbox(aircraft) {
            document.getElementById(`lightbox-${aircraft}`).style.display = 'flex';
            currentIndex[aircraft] = 0;
            updateLightboxImage(aircraft);
        }

        function closeLightbox(aircraft) {
            document.getElementById(`lightbox-${aircraft}`).style.display = 'none';
        }

        function nextImage(aircraft) {
            if (!aircraftImages[aircraft]) return;
            currentIndex[aircraft] = (currentIndex[aircraft] + 1) % aircraftImages[aircraft].length;
            updateLightboxImage(aircraft);
        }

        function prevImage(aircraft) {
            if (!aircraftImages[aircraft]) return;
            currentIndex[aircraft] = (currentIndex[aircraft] - 1 + aircraftImages[aircraft].length) % aircraftImages[aircraft].length;
            updateLightboxImage(aircraft);
        }

        function updateLightboxImage(aircraft) {
            document.getElementById(`lightbox-img-${aircraft}`).src = aircraftImages[aircraft][currentIndex[aircraft]];
        }

        // Close lightbox when clicking on the background (not on the image or buttons)
        document.querySelectorAll('.lightbox').forEach(lightbox => {
            lightbox.addEventListener('click', (e) => {
                // Check if the click target is the lightbox itself, not its children
                if (e.target === lightbox) {
                    lightbox.style.display = 'none';
                }
            });
        });

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>