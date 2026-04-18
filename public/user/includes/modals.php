<style>
     .program-modal-content {
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
         border-radius: 20px;
         border: none;
     }
     .modal-backdrop.show {
         backdrop-filter: blur(5px);
         background-color: rgba(0, 0, 0, 0.5);
     }
     .program-btn {
         padding: 15px;
         margin-bottom: 15px;
         border-radius: 12px;
         text-align: center;
         font-weight: 700;
         font-size: 0.9rem;
         cursor: pointer;
         transition: transform 0.2s ease;
         box-shadow: 0 2px 5px rgba(0,0,0,0.1);
     }
     .program-btn.navy { background-color: #1A1851; color: white; }
     .program-btn.gold { background-color: #FFB11B; color: #1A1851; }
     .program-btn:hover {
         transform: scale(1.03);
         filter: brightness(1.1);
     }
</style>

<?php
if (isset($conn)) {
    // 1. Fetch all departments
    $deptModalsQuery = "SELECT * FROM departments ORDER BY name ASC";
    $deptModalsResult = $conn->query($deptModalsQuery);
    
    if ($deptModalsResult && $deptModalsResult->num_rows > 0) {
        while ($dept = $deptModalsResult->fetch_assoc()) {
            $deptId = $dept['id'];
            $deptAbbr = htmlspecialchars($dept['abbreviation']);
            
            // Generate the Modal wrapper for this specific department
            echo '
            <div class="modal fade" id="userDeptModal_' . $deptId . '" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content program-modal-content p-4">
                        <div class="modal-body">
                            <h5 class="text-center fw-bold mb-5 text-uppercase" style="letter-spacing: 2px;">' . $deptAbbr . ' PROGRAMS</h5>
                            <div class="row g-3">';
                            
            // 2. Fetch programs belonging to this department ONLY
            $progQuery = "SELECT * FROM programs WHERE department_id = ? ORDER BY name ASC";
            $progStmt = $conn->prepare($progQuery);
            $progStmt->bind_param("i", $deptId);
            $progStmt->execute();
            $progRes = $progStmt->get_result();
            
            if ($progRes && $progRes->num_rows > 0) {
                $progCount = 0;
                echo '<div class="col-md-6">'; // Open first column
                
                $halfPoint = ceil($progRes->num_rows / 2);
                
                while ($prog = $progRes->fetch_assoc()) {
                    // Split into the second column halfway through
                    if ($progCount > 0 && $progCount == $halfPoint) {
                        echo '</div><div class="col-md-6">'; 
                    }
                    
                    $btnClass = ($progCount % 2 == 0) ? 'navy' : 'gold';
                    $progNameEscaped = htmlspecialchars(addslashes($prog['name']));
                    
                    // The button calls renderSections() in section_view.php!
                    echo '
                        <div class="program-btn ' . $btnClass . '" onclick="renderSections(\'' . $progNameEscaped . '\')">
                            ' . htmlspecialchars($prog['name']) . '
                        </div>
                    ';
                    $progCount++;
                }
                echo '</div>'; // Close column
            } else {
                echo '<div class="col-12 text-center text-muted py-4">No programs listed for this department yet.</div>';
            }
            $progStmt->close();
                            
            echo '      </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn px-4 fw-bold" data-bs-dismiss="modal" style="background-color: #1A1851; color: white; border-radius: 8px;">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
    }
}
?>