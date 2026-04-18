<style>
    .modal-content-custom { border-radius: 15px; padding: 20px; }
    
    .user-table-wrapper { 
        border: 1px solid var(--bs-border-color); 
        border-radius: 8px; 
        height: 380px; 
        padding: 15px; 
        position: relative;
    }
    .table-scroll-area {
        max-height: 280px; 
        overflow-y: auto;
    }
    .table-scroll-area::-webkit-scrollbar { width: 6px; }
    .table-scroll-area::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
    
    .user-table th { font-size: 0.85rem; font-weight: 700; border-bottom: 2px solid var(--bs-border-color); position: sticky; top: 0; background-color: var(--bs-body-bg); z-index: 1;}
    .user-table td { font-size: 0.85rem; font-weight: 700; vertical-align: middle; }
    .action-delete { color: #dc3545; text-decoration: none; cursor: pointer; font-size: 0.8rem; font-weight: 700; }
</style>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-body p-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0">Add Users</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="row">
                    <div class="col-md-7 mb-4 mb-md-0">
                        <div class="user-table-wrapper">
                            <div class="table-scroll-area">
                                <table class="table table-borderless user-table" id="adminUserTable">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Password</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adminUserTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn-navy fw-bold position-absolute bottom-0 end-0 m-3 px-4" onclick="uploadAllPendingUsers()">Upload</button>
                        </div>
                    </div>
                    
                    <div class="col-md-4 offset-md-1">
                        <div class="mb-3">
                            <label class="form-label">ID Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newUserId" placeholder="ID Number">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="newUserRole">
                                <option value="user" selected>Student / Normal User</option>
                                <option value="admin">System Admin</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="newUserPass" placeholder="Password">
                        </div>
                        <button type="button" class="btn btn-navy w-100 py-2 fw-bold" onclick="stageUserForUpload()">Submit to Box</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Array to hold the users waiting to be uploaded
    let pendingUsersArray = [];

    function stageUserForUpload() {
        const idInput = document.getElementById('newUserId');
        const roleInput = document.getElementById('newUserRole');
        const passInput = document.getElementById('newUserPass');
        const tbody = document.getElementById('adminUserTableBody');

        const newId = idInput.value.trim();
        const newRole = roleInput.value;
        const newPass = passInput.value.trim();
        
        // Visual text for the badge
        const displayRole = newRole === 'admin' ? 'Admin' : 'Student';
        const badgeColor = newRole === 'admin' ? 'bg-danger' : 'bg-primary';

        if (!newId || !newPass) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please enter both an ID Number and Password.'
            });
            return;
        }

        // Prevent adding duplicates to the visual box
        if(pendingUsersArray.some(u => u.username === newId)) {
            Swal.fire({ icon: 'warning', title: 'Already added', text: 'This user is already in your pending list.'});
            return;
        }

        // Add to memory array
        pendingUsersArray.push({ username: newId, password: newPass, role: newRole });

        // Add to the UI box
        const newRow = document.createElement('tr');
        newRow.className = 'fade-in-up'; 
        newRow.id = 'pending-row-' + newId;
        newRow.innerHTML = `
            <td class="text-dark">${newId}</td>
            <td class="text-dark"><span class="badge ${badgeColor}">${displayRole}</span></td>
            <td class="text-dark">••••••••</td>
            <td class="text-end">
                <span class="action-delete ms-2" onclick="removePendingUser('${newId}')">Delete</span>
            </td>
        `;

        tbody.insertBefore(newRow, tbody.firstChild);

        // Clear inputs for the next one
        idInput.value = '';
        passInput.value = '';
    }

    function removePendingUser(username) {
        // Remove from memory
        pendingUsersArray = pendingUsersArray.filter(u => u.username !== username);
        // Remove from UI
        document.getElementById('pending-row-' + username).remove();
    }

    async function uploadAllPendingUsers() {
        if (pendingUsersArray.length === 0) {
            Swal.fire({ icon: 'info', text: 'There are no users in the box to upload.' });
            return;
        }

        // Show a loading spinner
        Swal.fire({
            title: 'Uploading Users...',
            text: 'Please wait while we save them to the database.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let successCount = 0;
        let errorMessages = [];

        // Send them to the backend
        for (let i = 0; i < pendingUsersArray.length; i++) {
            let user = pendingUsersArray[i];
            let formData = new FormData();
            formData.append('username', user.username);
            formData.append('password', user.password);
            formData.append('role', user.role); // Passing the role to PHP

            try {
                let response = await fetch('../../app/controllers/addUserController.php', {
                    method: 'POST',
                    body: formData
                });
                
                let data = await response.json();
                
                if (data.status === 'success') {
                    successCount++;
                    document.getElementById('pending-row-' + user.username).remove();
                } else {
                    errorMessages.push(`<b>${user.username}:</b> ${data.message}`);
                }
            } catch (error) {
                errorMessages.push(`<b>${user.username}:</b> Network connection error.`);
            }
        }

        // Clean up array
        pendingUsersArray = pendingUsersArray.filter(u => document.getElementById('pending-row-' + u.username) !== null);

        if (errorMessages.length === 0) {
            Swal.fire({
                icon: 'success',
                title: 'All Uploaded!',
                text: `${successCount} user(s) successfully added.`,
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: `Upload Complete (${successCount} successful)`,
                html: `Some users failed to upload:<br><br><div class="text-start" style="font-size: 0.85rem;">${errorMessages.join('<br>')}</div>`
            });
        }
    }
</script>