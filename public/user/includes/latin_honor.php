<style>
    .search-box .form-control {
        background-color: #E8E8E8 !important;
        border: none;
        font-size: 0.9rem;
        padding: 10px 20px 10px 40px;
    }

    .search-box .form-control:focus {
        z-index: 1;
        box-shadow: 0 0 0 0.25rem rgba(26, 24, 81, 0.1);
    }

    #no-results p {
        font-family: 'Inter', sans-serif;
        letter-spacing: 1px;
        font-weight: 500;
    }
</style>

<section id="latin-honor" style="display:none;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-4"></div>
        <div class="col-md-8 d-flex justify-content-end">
            <div class="input-group search-box w-50">
                <input type="text" id="search-latin" class="form-control rounded-pill" placeholder="Search Latin Honors..." autocomplete="off">
                <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="z-index: 5; color: #6c757d;">
                    <i class="bi bi-search"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center mb-4">
        <p class="fw-bold m-0 me-2">Class of</p>
        <select class="form-select form-select-sm w-auto fw-bold border-secondary cursor-pointer" id="yearSelectLatin">
            <option value="2029" selected>2029</option>
            <option value="2028">2028</option>
            <option value="2027">2027</option>
            <option value="2026">2026</option>
        </select>
    </div>

    <div id="latin-grid" class="row g-3"></div>

    <div id="no-results" class="text-center py-5" style="display: none;">
        <i class="bi bi-person-x" style="font-size: 3rem; color: #ccc;"></i>
        <p class="mt-3 text-muted">No students found matching that name.</p>
    </div>

    <div class="d-flex justify-content-center mt-5">
        <nav aria-label="Page navigation">
            <ul class="pagination custom-pagination" id="latin-pagination"></ul>
        </nav>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const itemsPerPage = 15;
        let currentPage = 1;
        let allCards = [];
        
        const grid = document.getElementById('latin-grid');
        const paginationContainer = document.getElementById('latin-pagination');
        const yearSelect = document.getElementById('yearSelectLatin');
        const searchInput = document.getElementById('search-latin');
        const noResults = document.getElementById('no-results');

        // --- FETCH REAL DATABASE STUDENTS ---
        function loadStudentsForYear(year) {
            grid.innerHTML = '<div class="text-center w-100 py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted fw-bold">Loading Honors...</p></div>';
            allCards = [];
            
            fetch(`../../app/controllers/getGalleryData.php?action=latin&year=${year}`)
                .then(res => res.json())
                .then(data => {
                    grid.innerHTML = '';
                    
                    if(data.length === 0) {
                        noResults.style.display = 'block';
                        noResults.innerHTML = '<i class="bi bi-award text-muted" style="font-size: 3rem;"></i><p class="mt-3 text-muted">No Latin Honors recorded for this year.</p>';
                        paginationContainer.innerHTML = '';
                        return;
                    }
                    noResults.style.display = 'none';

                    data.forEach(student => {
                        const tempDiv = document.createElement('div');
                        tempDiv.className = "col-6 col-md-2-4 mb-4 latin-card-item fade-in-up";
                        
                        // User side needs to look back into the admin folder for the image
                        let imgSrc = "../admin/" + student.photo_path;

                        tempDiv.innerHTML = `
                            <div class="honor-profile text-center">
                                <img src="${imgSrc}" class="mb-2 shadow-sm" alt="Student" onerror="this.src='assets/Img/Student/Durain.jpg'">
                                <div class="px-1">
                                    <small class="fw-bold text-uppercase d-block text-truncate" title="${student.full_name}">${student.full_name}</small>
                                    <small class="text-muted d-block" style="font-size: 0.65rem; color: #FFB11B !important; font-weight: bold;">${student.latin_honor.toUpperCase()}</small>
                                    <small class="fw-bold">${student.prog_abbr || 'N/A'}</small>
                                </div>
                            </div>
                        `;
                        allCards.push(tempDiv);
                        grid.appendChild(tempDiv);
                    });
                    
                    currentPage = 1;
                    displayItems(allCards);
                })
                .catch(err => console.error("Error loading latin honors:", err));
        }

        yearSelect.addEventListener('change', function() {
            loadStudentsForYear(this.value);
        });

        function renderPagination(totalItems) {
            paginationContainer.innerHTML = '';
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalPages <= 1) return; 

            let prevClass = currentPage === 1 ? 'disabled' : '';
            paginationContainer.innerHTML += `<li class="page-item ${prevClass}"><a class="page-link prev-next" href="#" onclick="changeLatinPage(event, ${currentPage - 1})"><i class="bi bi-chevron-left"></i></a></li>`;

            for (let i = 1; i <= totalPages; i++) {
                let activeClass = currentPage === i ? 'active' : '';
                paginationContainer.innerHTML += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="changeLatinPage(event, ${i})">${i}</a></li>`;
            }

            let nextClass = currentPage === totalPages ? 'disabled' : '';
            paginationContainer.innerHTML += `<li class="page-item ${nextClass}"><a class="page-link prev-next" href="#" onclick="changeLatinPage(event, ${currentPage + 1})"><i class="bi bi-chevron-right"></i></a></li>`;
        }

        function displayItems(cards) {
            allCards.forEach(card => card.style.display = 'none');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const visibleSlice = cards.slice(startIndex, endIndex);
            visibleSlice.forEach(card => card.style.display = 'block');
            renderPagination(cards.length);
        }

        window.changeLatinPage = function(event, newPage) {
            event.preventDefault();
            currentPage = newPage;
            triggerLatinSearch(); 
        }

        function triggerLatinSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            let filteredCards = [];

            allCards.forEach(card => {
                const nameElement = card.querySelector('.fw-bold.text-uppercase');
                if (nameElement && nameElement.innerText.toLowerCase().includes(searchTerm)) {
                    filteredCards.push(card);
                }
            });

            if (filteredCards.length === 0) {
                noResults.style.display = 'block';
                noResults.innerHTML = '<i class="bi bi-person-x" style="font-size: 3rem; color: #ccc;"></i><p class="mt-3 text-muted">No students found matching that name.</p>';
                grid.style.display = 'none';
                paginationContainer.innerHTML = '';
            } else {
                noResults.style.display = 'none';
                grid.style.display = 'flex';
                displayItems(filteredCards);
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                currentPage = 1; 
                triggerLatinSearch();
            });
        }

        loadStudentsForYear(yearSelect.value);
    });
</script>