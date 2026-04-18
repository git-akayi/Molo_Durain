<style>
    .hero-section {
        background-color: var(--navy-dark);
        height: 450px;
        overflow: hidden;
    }
    .serif-font {
        font-family: 'Cinzel', serif;
        letter-spacing: 4px; 
        font-weight: 700;
    }
    .btn-side-arrow {
        width: 45px; height: 45px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.3s ease; backdrop-filter: blur(5px);
    }
    .btn-side-arrow i { font-size: 1.2rem; color: white; }
    .carousel-control-prev:hover .btn-side-arrow,
    .carousel-control-next:hover .btn-side-arrow {
        background: rgba(255, 255, 255, 0.3); transform: scale(1.1);
    }
    .carousel-indicators [data-bs-target] {
        width: 8px; height: 8px; border-radius: 50%;
        background-color: rgba(255,255,255,0.4); border: none;
        margin: 0 5px; transition: all 0.3s ease;
    }
    .carousel-indicators .active {
        background-color: white; transform: scale(1.2);
    }
</style>


<header id="heroCarousel" class="carousel slide hero-section" data-bs-ride="carousel">
    <div class="carousel-indicators mb-4">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active dot"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" class="dot"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" class="dot"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" class="dot"></button>
    </div>

    <div class="carousel-inner h-100">
        <div class="carousel-item active h-100">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white">
                <h1 class="display-3 serif-font text-uppercase">Welcome to USTP</h1>
                <h5 class="fw-bold mt-2">E-Gallery</h5>
                <p class="fst-italic opacity-75 mt-3">The USTP E-Gallery is the digital version of the physical yearbook.</p>
            </div>
        </div>

        <div class="carousel-item h-100">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white">
                <h1 class="display-3 serif-font text-uppercase">Congratulations</h1>
                <h5 class="fw-bold mt-2">Class of 2029</h5>
                <p class="fst-italic opacity-75 mt-3">"The future belongs to those who believe in the beauty of their dreams."</p>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="btn-side-arrow"><i class="bi bi-chevron-left"></i></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="btn-side-arrow"><i class="bi bi-chevron-right"></i></span>
    </button>
</header>