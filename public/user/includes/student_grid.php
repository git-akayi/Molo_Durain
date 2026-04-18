<style>
    #current-section-title {
        font-size: 1.1rem;
        border-left: 2px solid #dee2e6;
        padding-left: 15px;
    }
    #student-container {
        transition: all 0.3s ease;
        min-height: 400px; 
    }
</style>

<section id="student-grid-view" style="display:none; padding: 20px;">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center">
            <button class="btn-back me-3" onclick="goBackToSections()">
                <i class="bi bi-arrow-left"></i>
            </button>
            <h5 id="current-section-title" class="fw-bold m-0 text-uppercase" style="letter-spacing: 2px; color: var(--navy-dark);">
                SECTION NAME
            </h5>
        </div>
    </div>

    <div class="mb-4 d-flex align-items-center">
        <h6 class="fw-bold text-muted m-0 me-2" style="font-size: 0.9rem;">Class of</h6>
        <select class="form-select form-select-sm w-auto fw-bold text-muted border-secondary cursor-pointer" id="yearSelectGrid" style="font-size: 0.85rem;">
            <option value="2029" selected>2029</option>
            <option value="2028">2028</option>
            <option value="2027">2027</option>
            <option value="2026">2026</option>
        </select>
    </div>

    <div id="student-container" class="row g-3"></div>

    <div class="d-flex justify-content-center mt-5">
        <nav aria-label="Page navigation">
            <ul class="pagination custom-pagination" id="grid-pagination"></ul>
        </nav>
    </div>
</section>

<script>
    function goBackToSections() {
        const gridView = document.getElementById('student-grid-view');
        gridView.style.display = 'none';
        gridView.classList.remove('fade-in-up');
        document.getElementById('section-view').style.display = 'block';
    }

    let gridItemsPerPage = 15;
    let gridCurrentPage = 1;
    let gridTotalStudents = []; 

    document.getElementById('yearSelectGrid').addEventListener('change', function() {
        let currentSection = document.getElementById('current-section-title').innerText;
        generateSectionStudents(currentSection, this.value);
    });

    function renderGridPagination(totalItems) {
        const paginationContainer = document.getElementById('grid-pagination');
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(totalItems / gridItemsPerPage);

        if (totalPages <= 1) return;

        let prevClass = gridCurrentPage === 1 ? 'disabled' : '';
        paginationContainer.innerHTML += `<li class="page-item ${prevClass}"><a class="page-link prev-next" href="#" onclick="changeGridPage(event, ${gridCurrentPage - 1})"><i class="bi bi-chevron-left"></i></a></li>`;

        for (let i = 1; i <= totalPages; i++) {
            let activeClass = gridCurrentPage === i ? 'active' : '';
            paginationContainer.innerHTML += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="changeGridPage(event, ${i})">${i}</a></li>`;
        }

        let nextClass = gridCurrentPage === totalPages ? 'disabled' : '';
        paginationContainer.innerHTML += `<li class="page-item ${nextClass}"><a class="page-link prev-next" href="#" onclick="changeGridPage(event, ${gridCurrentPage + 1})"><i class="bi bi-chevron-right"></i></a></li>`;
    }

    function displayGridPage() {
        const container = document.getElementById('student-container');
        container.innerHTML = ""; 

        const startIndex = (gridCurrentPage - 1) * gridItemsPerPage;
        const endIndex = startIndex + gridItemsPerPage;
        const visibleSlice = gridTotalStudents.slice(startIndex, endIndex);

        visibleSlice.forEach(studentHTML => {
            container.innerHTML += studentHTML;
        });

        renderGridPagination(gridTotalStudents.length);
    }

    window.changeGridPage = function(event, newPage) {
        event.preventDefault();
        gridCurrentPage = newPage;
        displayGridPage();
    }

    // --- FETCH REAL STUDENTS FOR THE SECTION GRID ---
    function generateSectionStudents(sectionCode, year) {
        gridCurrentPage = 1;
        gridTotalStudents = [];
        
        const container = document.getElementById('student-container');
        container.innerHTML = '<div class="text-center w-100 py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 fw-bold text-muted">Loading Class...</p></div>';

        fetch(`../../app/controllers/getGalleryData.php?action=students&section=${encodeURIComponent(sectionCode)}&year=${year}`)
            .then(res => res.json())
            .then(data => {
                if(data.length === 0) {
                    container.innerHTML = '<div class="text-center w-100 py-5 text-muted"><i class="bi bi-people" style="font-size: 3rem;"></i><p class="mt-3 fw-bold">No students found in this section yet.</p></div>';
                    document.getElementById('grid-pagination').innerHTML = '';
                    return;
                }

                data.forEach(student => {
                    let imgSrc = "../admin/" + student.photo_path;
                    let quote = student.quote ? `"${student.quote}"` : '';

                    gridTotalStudents.push(`
                        <div class="col-6 col-md-2-4 mb-4 fade-in-up">
                            <div class="honor-profile text-center">
                                <img src="${imgSrc}" class="mb-2 shadow-sm" alt="Student" onerror="this.src='assets/Img/Student/Durain.jpg'">
                                <div class="px-1">
                                    <small class="fw-bold text-uppercase d-block text-truncate" style="font-size: 0.7rem;" title="${student.full_name}">${student.full_name}</small>
                                    <small class="d-block text-truncate text-muted" style="font-size: 0.65rem; font-style: italic;" title="${quote}">${quote}</small>
                                </div>
                            </div>
                        </div>
                    `);
                });
                
                displayGridPage();
            })
            .catch(err => console.error("Error loading students:", err));
    }

    function openClassYear(sectionCode) {
        document.getElementById('home').style.display = 'none';
        document.getElementById('latin-honor').style.display = 'none';
        document.getElementById('departments').style.display = 'none';
        document.getElementById('section-view').style.display = 'none';
        
        const gridView = document.getElementById('student-grid-view');
        gridView.style.display = 'block';
        gridView.classList.add('fade-in-up');

        document.getElementById('current-section-title').innerText = sectionCode;
        
        let currentYear = document.getElementById('yearSelectGrid').value;
        generateSectionStudents(sectionCode, currentYear);
    }
</script>