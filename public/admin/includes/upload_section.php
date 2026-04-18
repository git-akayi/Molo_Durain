<div class="modal fade" id="sectionModal" tabindex="-1" aria-labelledby="sectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-custom" style="padding: 20px;">
            <div class="modal-body">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0">Sections</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select" id="modalDeptSelect" onchange="updatePrograms('modalDeptSelect', 'modalProgramSelect')">
                            <option value="" selected>Select Department</option>
                            <option value="engineering">Engineering</option>
                            <option value="csis">Computer Science and Information Systems</option>
                            <option value="technology">Technology</option>
                            <option value="ls">Life Sciences</option>
                            <option value="ns">Natural Sciences</option>
                            <option value="ss">Social Sciences</option>
                            <option value="ah">Art and Humanities</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Program <span class="text-danger">*</span></label>
                        <select class="form-select" id="modalProgramSelect">
                            <option value="" selected>Select Department first</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modalSectionInput" placeholder="e.g. 4R1">
                    </div>
                    
                    <div class="col-12 text-end mt-4">
                        <button type="button" class="btn btn-navy px-4 py-2 fw-bold" onclick="addNewSection()">Add Section</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>