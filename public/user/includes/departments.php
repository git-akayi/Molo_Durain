<style>
    .dept-btn {
        height: 160px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
        text-transform: uppercase;
    }
    .dept-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .dept-navy { background-color: #1A1851; }
    .dept-gold { background-color: #FFB11B; color: #1A1851 !important; }
</style>

<section id="departments" style="display:none; padding: 20px;">
    <h4 class="text-center fw-bold mb-5" style="letter-spacing: 2px; color: var(--navy-dark);">DEPARTMENTS</h4>
    
    <div class="row g-4 justify-content-center">
        <?php
        if (isset($conn)) {
            $deptQuery = "SELECT * FROM departments ORDER BY name ASC";
            $deptResult = $conn->query($deptQuery);
            $count = 0;
            
            if ($deptResult && $deptResult->num_rows > 0) {
                while ($dept = $deptResult->fetch_assoc()) {
                    // Alternate colors matching Figma
                    $bgClass = ($count % 2 == 0) ? 'dept-navy' : 'dept-gold';
                    
                    echo '
                    <div class="col-md-4 col-sm-6 fade-in-up">
                        <div class="dept-btn ' . $bgClass . '" data-bs-toggle="modal" data-bs-target="#userDeptModal_' . $dept['id'] . '">
                            ' . htmlspecialchars($dept['name']) . '
                        </div>
                    </div>';
                    $count++;
                }
            } else {
                echo '<div class="text-center text-muted col-12 py-5">No departments added yet.</div>';
            }
        }
        ?>
    </div>
</section>