<style>
    .photo-upload-card {
        border-radius: 12px;
        padding: 40px;
        min-height: 450px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-icon-wrapper {
        width: 80px;
        height: 80px;
        background-color: var(--bs-secondary-bg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .upload-icon-wrapper i {
        font-size: 2rem;
        color: #1A1851;
    }

    .preview-img {
        width: 100%;
        max-width: 250px;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        object-position: top center;
        border-radius: 8px;
        display: none;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        font-weight: 800;
        font-size: 0.85rem;
    }

    .upload-title {
        color: #000;
    }

    [data-bs-theme="dark"] .upload-title {
        color: #fff;
    }
</style>

<div id="photoTab">
    <h5 class="fw-bold mb-4">Upload Student Year Book</h5>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="photo-upload-card text-center shadow-sm">
                <img id="photoPreview" class="preview-img" src="" alt="Student Preview">

                <div id="uploadPlaceholder">
                    <div class="upload-icon-wrapper mx-auto">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-2 upload-title">Select Photo to Upload</h6>
                    <p class="text-muted small mb-3" style="font-size: 0.75rem;">Supported Format: PNG, JPG<br>(15mb each)</p>
                </div>

                <input type="file" id="studentPhotoInput" accept="image/png, image/jpeg" style="display: none;" onchange="previewSelectedPhoto(event)">

                <button class="btn-upload-left" onclick="document.getElementById('studentPhotoInput').click()">
                    Select Image <i class="bi bi-image ms-2"></i>
                </button>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="inputName" placeholder="e.g. Durain, Jussy Jay G.">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select class="form-select" id="photoDeptSelect" onchange="updatePrograms('photoDeptSelect', 'photoProgramSelect'); updatePhotoSections();">
                        <option value="" selected>Select Department</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Program <span class="text-danger">*</span></label>
                    <select class="form-select" id="photoProgramSelect" onchange="updatePhotoSections()">
                        <option value="" selected>Select Department first</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Section <span class="text-danger">*</span></label>
                    <select class="form-select" id="photoSectionSelect">
                        <option value="" selected>Select Program first</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Latin Honor <span class="text-danger">*</span></label>
                    <select class="form-select" id="inputLatin">
                        <option value="None" selected>None</option>
                        <option value="Magna Cum Laude">Magna Cum Laude</option>
                        <option value="Summa Cum Laude">Summa Cum Laude</option>
                        <option value="Cum Laude">Cum Laude</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Class Year <span class="text-danger">*</span></label>
                    <select class="form-select" id="inputYear">
                        <?php if (!empty($allYears)): ?>
                            <?php foreach ($allYears as $y): ?>
                                <option value="<?php echo htmlspecialchars($y['year']); ?>" <?php echo ($defaultYear == $y['year']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($y['year']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?php echo htmlspecialchars($defaultYear); ?>" selected><?php echo htmlspecialchars($defaultYear); ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Quote <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="inputQuote" rows="4" placeholder="Enter text here..."></textarea>
                </div>

                <div class="col-12 mt-3">
                    <button type="button" class="btn btn-navy w-100 py-2 fw-bold" onclick="simulateUpload()">Upload</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewSelectedPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('uploadPlaceholder').style.display = 'none';
            const previewImg = document.getElementById('photoPreview');
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = 'block';
            event.target.nextElementSibling.innerHTML = 'Change Photo <i class="bi bi-arrow-repeat ms-2"></i>';
        }
    }

    function simulateUpload() {
        const name = document.getElementById('inputName').value.trim();
        const deptId = document.getElementById('photoDeptSelect').value;
        const progId = document.getElementById('photoProgramSelect').value;
        const sectionId = document.getElementById('photoSectionSelect').value;
        const latin = document.getElementById('inputLatin').value;
        const year = document.getElementById('inputYear').value.trim();
        const quote = document.getElementById('inputQuote').value.trim();
        const photoInput = document.getElementById('studentPhotoInput');

        // CHECK 1: Ensure all text fields and dropdowns have valid selections
        if (!name || !deptId || !progId || !sectionId || !quote || !year) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Info',
                text: 'Please fill in all fields. Make sure a Department, Program, and Section are actively selected!'
            });
            return;
        }

        // CHECK 2: Ensure a photo is actually uploaded
        if (photoInput.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Photo',
                text: 'Please select a photo to upload.'
            });
            return;
        }

        let formData = new FormData();
        formData.append('photo', photoInput.files[0]);
        formData.append('name', name);
        formData.append('department_id', deptId);
        formData.append('program_id', progId);
        formData.append('section', sectionId);
        formData.append('latin_honor', latin);
        formData.append('class_year', year);
        formData.append('quote', quote);

        Swal.fire({
            title: 'Uploading...',
            text: 'Saving student profile to database.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send to PHP engine
        fetch('../../app/controllers/uploadPhotoController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                            icon: 'success',
                            title: 'Uploaded Successfully',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .then(() => {
                            location.reload();
                        });
                } else {
                    // If it crashes, the try/catch in PHP will tell us EXACTLY what broke right here!
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire('Error', 'Critical network error occurred.', 'error');
            });
    }
</script>