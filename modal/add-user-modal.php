<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-4 fw-bold" id="confirmModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Add New User
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body modalText text-start">
                <form id="addUserForm" method="POST">
                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Full Name <span style="color:#dc3545;">*</span></label>
                                <input type="text" name="fullName" placeholder="Enter full name" required
                                    class="form-control"
                                    style="border: 2px solid var(--primary-color); border-radius:10px; padding:12px;
                                           font-family: var(--secondaryFont); background: var(--card-bg-color); color: var(--text-color-dark);">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Email <span style="color:#dc3545;">*</span></label>
                                <input type="email" name="email" placeholder="Enter email" required
                                    class="form-control"
                                    style="border: 2px solid var(--primary-color); border-radius:10px; padding:12px;
                                           font-family: var(--secondaryFont); background: var(--card-bg-color); color: var(--text-color-dark);">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Password <span style="color:#dc3545;">*</span></label>
                                <input type="password" name="password" placeholder="Enter password" required
                                    class="form-control"
                                    style="border: 2px solid var(--primary-color); border-radius:10px; padding:12px;
                                           font-family: var(--secondaryFont); background: var(--card-bg-color); color: var(--text-color-dark);">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Phone Number</label>
                                <input type="text" name="accNumber" placeholder="Enter phone number"
                                    class="form-control"
                                    style="border: 2px solid var(--primary-color); border-radius:10px; padding:12px;
                                           font-family: var(--secondaryFont); background: var(--card-bg-color); color: var(--text-color-dark);">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Username <span style="color:#dc3545;">*</span></label>
                                <input type="text" name="username" placeholder="Enter username" required
                                    class="form-control"
                                    style="border: 2px solid var(--primary-color); border-radius:10px; padding:12px;
                                           font-family: var(--secondaryFont); background: var(--card-bg-color); color: var(--text-color-dark);">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Confirm Password <span style="color:#dc3545;">*</span></label>
                                <input type="password" name="confirm_password" placeholder="Confirm password" required
                                    class="form-control"
                                    style="border: 2px solid var(--primary-color); border-radius:10px; padding:12px;
                                           font-family: var(--secondaryFont); background: var(--card-bg-color); color: var(--text-color-dark);">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Role <span style="color:#dc3545;">*</span></label>
                                <select name="role" required class="form-select"
                                    style="border: 2px solid var(--primary-color); border-radius:10px; padding:12px;
                                           font-family: var(--secondaryFont); background: var(--card-bg-color); color: var(--text-color-dark);">
                                    <option value="" disabled selected>Select role</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <button type="button" class="btn fw-bold px-4 py-2" data-bs-dismiss="modal"
                            style="background: var(--card-bg-color); color: var(--text-color-dark); border: 2px solid var(--primary-color); border-radius:10px;
                                   transition: all 0.3s ease;" 
                            onmouseover="this.style.background='var(--primary-color)'; this.style.color='var(--text-color-light)'; this.style.transform='translateY(-2px)';" 
                            onmouseout="this.style.background='var(--card-bg-color)'; this.style.color='var(--text-color-dark)'; this.style.transform='translateY(0)';">
                            CANCEL
                        </button>
                        <button type="submit" name="btnAddUser" class="btn fw-bold px-4 py-2"
                            style="background: var(--text-color-dark); color: var(--text-color-light); border:none; border-radius:10px; 
                                   box-shadow: 0 4px 8px rgba(196, 162, 119, 0.3); transition: all 0.3s ease;"
                            onmouseover="this.style.background='var(--primary-color)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(196,162,119,0.4)';"
                            onmouseout="this.style.background='var(--text-color-dark)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 8px rgba(196,162,119,0.3)';">
                            <i class="bi bi-plus-circle-fill me-2"></i>CONFIRM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
