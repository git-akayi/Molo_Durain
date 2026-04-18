<style>
    #section-view-title {
        font-size: 1.1rem;
        border-left: 2px solid #dee2e6;
        padding-left: 15px;
    }
    .section-card {
        height: 160px; border-radius: 12px;
        background: url('assets/Img/Logo/Graduation_Section.webp') center/cover no-repeat;
        position: relative; overflow: hidden; cursor: pointer; transition: transform 0.2s ease;
    }
    .section-card:hover { transform: translateY(-5px); }
    .section-blur-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(12px); 
        display: flex; justify-content: center; align-items: center;
        color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
</style>

<section id="section-view" style="display:none; padding: 20px;">
    <div class="row mb-5">
        <div class="col-12 d-flex align-items-center">
            <button class="btn-back me-3" onclick="goBackToDepartments()">
                <i class="bi bi-arrow-left"></i>
            </button>
            <h5 id="section-view-title" class="fw-bold m-0 text-uppercase" style="letter-spacing: 2px; color: var(--navy-dark); border-left: 2px solid #dee2e6; padding-left: 15px;">
                AVAILABLE SECTIONS
            </h5>
        </div>
    </div>

    <div id="section-grid" class="row g-4 justify-content-center"></div>

    <div class="d-flex justify-content-center mt-5">
        <nav aria-label="Page navigation">
            <ul class="pagination custom-pagination" id="section-pagination">
                </ul>
        </nav>
    </div>
</section>

<script>
    function goBackToDepartments() {
        document.getElementById('section-view').style.display = 'none';
        document.getElementById('departments').style.display = 'block';
    }

    let sectionItemsPerPage = 9; 
    let sectionCurrentPage = 1;
    let sectionTotalItems = [];

    // --- FETCH REAL SECTIONS FOR THE PROGRAM ---
    window.renderSections = function(programName) {
        // 1. Identify which modal is currently open and close it
        const activeModal = document.querySelector('.modal.show');
        if (activeModal) {
            const modal = bootstrap.Modal.getInstance(activeModal);
            modal.hide();
        }

        // 2. Hide the main departments container and show the section view
        document.getElementById('departments').style.display = 'none';
        document.getElementById('section-view').style.display = 'block';
        document.getElementById('section-view-title').innerText = programName.toUpperCase();

        const grid = document.getElementById('section-grid');
        grid.innerHTML = '<div class="text-center w-100 py-5"><div class="spinner-border text-primary" role="status"></div></div>';

        // 3. Fetch from DB Bridge
        fetch(`../../app/controllers/getGalleryData.php?action=sections&program=${encodeURIComponent(programName)}`)
            .then(res => res.json())
            .then(data => {
                sectionCurrentPage = 1;
                sectionTotalItems = [];

                if(data.length === 0) {
                    grid.innerHTML = '<div class="text-center w-100 py-5 text-muted fw-bold">No sections available for this program yet.</div>';
                    document.getElementById('section-pagination').innerHTML = '';
                    return;
                }

                data.forEach(sec => {
                    sectionTotalItems.push(`
                        <div class="col-md-4 col-sm-6 fade-in-up">
                            <div class="section-card shadow-sm" onclick="openClassYear('${sec.name}')">
                                <div class="section-blur-overlay">
                                    <h4 class="fw-bold m-0 text-center px-2">${sec.name}</h4>
                                </div>
                            </div>
                        </div>
                    `);
                });
                displaySectionPage();
            })
            .catch(err => console.error(err));
    }

    function displaySectionPage() {
        const container = document.getElementById('section-grid');
        container.innerHTML = ""; 

        const startIndex = (sectionCurrentPage - 1) * sectionItemsPerPage;
        const endIndex = startIndex + sectionItemsPerPage;
        const visibleSlice = sectionTotalItems.slice(startIndex, endIndex);

        visibleSlice.forEach(html => {
            container.innerHTML += html;
        });

        renderSectionPagination(sectionTotalItems.length);
    }

    function renderSectionPagination(totalItems) {
        const paginationContainer = document.getElementById('section-pagination');
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(totalItems / sectionItemsPerPage);

        if (totalPages <= 1) return;

        let prevClass = sectionCurrentPage === 1 ? 'disabled' : '';
        paginationContainer.innerHTML += `<li class="page-item ${prevClass}"><a class="page-link prev-next" href="#" onclick="changeSectionPage(event, ${sectionCurrentPage - 1})"><i class="bi bi-chevron-left"></i></a></li>`;

        for (let i = 1; i <= totalPages; i++) {
            let activeClass = sectionCurrentPage === i ? 'active' : '';
            paginationContainer.innerHTML += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="changeSectionPage(event, ${i})">${i}</a></li>`;
        }

        let nextClass = sectionCurrentPage === totalPages ? 'disabled' : '';
        paginationContainer.innerHTML += `<li class="page-item ${nextClass}"><a class="page-link prev-next" href="#" onclick="changeSectionPage(event, ${sectionCurrentPage + 1})"><i class="bi bi-chevron-right"></i></a></li>`;
    }

    window.changeSectionPage = function(event, newPage) {
        event.preventDefault();
        sectionCurrentPage = newPage;
        displaySectionPage();
    }
</script>