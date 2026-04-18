<style>
    .yearbook-card {
        position: relative; border-radius: 15px;
        overflow: hidden; height: 320px; background-color: #eee;
    }
    .yearbook-card img {
        width: 100%; height: 100%;
        object-fit: cover; object-position: top center; display: block;
    }
    .card-overlay {
        position: absolute; bottom: 0; width: 100%;
        background: linear-gradient(to top, rgba(26, 24, 81, 0.95) 0%, rgba(26, 24, 81, 0.7) 50%, transparent 100%);
        color: white; padding: 20px 15px; text-align: center;
    }
    .card-overlay h6 { font-size: 0.9rem; font-weight: bold; margin-bottom: 5px; }
    .card-overlay p { font-size: 0.7rem; opacity: 0.8; }
    .quote-container {
        background-color: #ffffff; border-radius: 15px;
        overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .bg-light-gray { background-color: #F8F9FA; min-height: 400px; }
    .italic { font-style: italic; color: #333; line-height: 1.6; }
    .btn-navy-small { background: var(--navy-dark); color: white; border: none; padding: 5px 12px; border-radius: 4px; }
    .btn-navy-small:hover, .btn-navy-small:focus, .btn-navy-small:active {
        background-color: var(--navy-dark) !important; color: white !important;
        transform: none !important; box-shadow: none !important; outline: none !important;
    }
    .dot-sm { width: 6px; height: 6px; background: #ccc; border-radius: 50%; cursor: pointer; }
    .dot-sm.active { background: var(--navy-dark); }
    .carousel-controls-container {
        position: absolute; bottom: 40px; right: 50px; left: 48%; z-index: 5;
    }
    .carousel-item { transition: transform 0.6s ease-in-out; }
    .carousel-indicators-custom .active { background: var(--navy-dark); }
</style>


<section id="home">
    <h5 class="fw-bold mb-4">Recently</h5>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="yearbook-card">
                <img src="assets/Img/Student/TABANIAG, J-VHONNE L IMG_4870.jpeg" alt="Valedictory Address">
                <div class="card-overlay">
                    <h6>VALEDICTORY ADDRESS</h6>
                    <p>As we enter the next chapter, preparing for board exams or seeking jobs – and if the path <br> ahead seems impossible, don't forget that you've already conquered so much and endured even more.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="yearbook-card">
                <img src="assets/Img/Student/Cabanlit, Anika Jasmine.jpg" alt="Valedictory Address">
                <div class="card-overlay">
                    <h6>VALEDICTORY ADDRESS</h6>
                    <p>The version of you that the world needs already within you.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="yearbook-card">
                <img src="assets/Img/Student/Fugnit, Remiel Charles.jpg" alt="Valedictory Address">
                <div class="card-overlay">
                    <h6>VALEDICTORY ADDRESS</h6>
                    <p>Together, we have grown and now we help others grow.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="featuredCarousel" class="carousel slide quote-container mt-5" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="row g-0">
                    <div class="col-md-5">
                        <div class="yearbook-card rounded-0" style="height: 400px;">
                            <img src="assets/Img/Student/TUBA, SHANE ABBY.png" alt="Featured student">
                            <div class="card-overlay">
                                <h6 class="text-uppercase m-0">Valedictory Address</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 bg-light-gray p-5">
                        <p class="fs-5 italic mb-4">"We did not survive – we created our own way forward. We turned every hardship into a stepping stone. And wherever we go next, may continue making paths for ourselves and for those who will come after us."</p>
                        <div class="mt-2">
                            <div class="fw-bold fs-5">Ms. Shane Abby Tuba</div>
                            <div class="text-secondary small">Class of 2029</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="row g-0">
                    <div class="col-md-5">
                        <div class="yearbook-card rounded-0" style="height: 400px;">
                            <img src="assets/Img/Student/Caboverde, Chanice.JPG" alt="Featured student">
                            <div class="card-overlay">
                                <h6 class="text-uppercase m-0">Valedictory Address</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 bg-light-gray p-5">
                        <p class="fs-5 italic mb-4">"The journey was never easy, but the view from the top makes every struggle worth it. To the Class of 2029, we made it."</p>
                        <div class="mt-2">
                            <div class="fw-bold fs-5">Ms. Chanice Caboverde</div>
                            <div class="text-secondary small">Class of 2029</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="row g-0">
                    <div class="col-md-5">
                        <div class="yearbook-card rounded-0" style="height: 400px;">
                            <img src="assets/Img/Student/Justiniani, Jonathan.jpg" alt="Featured student">
                            <div class="card-overlay">
                                <h6 class="text-uppercase m-0">Valedictory Address</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 bg-light-gray p-5">
                        <p class="fs-5 italic mb-4">"Education is the most powerful weapon which you can use to change the world. Let's go change it together."</p>
                        <div class="mt-2">
                            <div class="fw-bold fs-5">Mr. Jonathan Justiniani</div>
                            <div class="text-secondary small">Class of 2029</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carousel-controls-container">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-navy-small rounded-circle p-0" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev" style="width:35px; height:35px;">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="carousel-indicators-custom d-flex gap-1">
                    <div class="dot-sm active" data-bs-target="#featuredCarousel" data-bs-slide-to="0"></div>
                    <div class="dot-sm" data-bs-target="#featuredCarousel" data-bs-slide-to="1"></div>
                    <div class="dot-sm" data-bs-target="#featuredCarousel" data-bs-slide-to="2"></div>
                </div>

                <button class="btn btn-navy-small rounded-circle p-0" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next" style="width:35px; height:35px;">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    // Run after the DOM loads to ensure the carousel element exists
    document.addEventListener('DOMContentLoaded', () => {
        var myCarousel = document.getElementById('featuredCarousel');
        if (myCarousel) {
            myCarousel.addEventListener('slide.bs.carousel', function (e) {
                let dots = document.querySelectorAll('.carousel-indicators-custom .dot-sm');
                dots.forEach(dot => dot.classList.remove('active'));
                dots[e.to].classList.add('active');
            });
        }
    });
</script>