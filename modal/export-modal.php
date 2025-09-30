<!-- Export Confirmation Modal -->
<div class="modal fade" id="exportModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="exportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-4 fw-bold" id="exportModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Export Inventory
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Export Icon -->
                <div class="text-center mb-4">
                    <i class="bi bi-file-earmark-excel-fill" style="font-size: 4rem; color: #217346;"></i>
                </div>

                <!-- Message -->
                <div class="text-center mb-4">
                    <p class="mb-2"
                        style="font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 1.1rem;">
                        Export inventory data to Excel file?
                    </p>
                    <p class="fw-bold mb-2"
                        style="font-family: var(--primaryFont); color: var(--text-color-dark); font-size: 1.2rem;">
                        <strong>inventory_export.csv</strong>
                    </p>
                    <p style="font-family: var(--secondaryFont); color: var(--text-color-dark);">
                        This will include all inventory items with current stock levels, expiration dates, and status
                        information.
                    </p>
                </div>

                <!-- Action Buttons -->
                <form method="POST" action="../assets/export-inventory.php" target="_blank">
                    <input type="hidden" name="action" value="export">
                    <input type="hidden" name="format" value="excel">

                    <div class="d-flex gap-3 justify-content-center">
                        <!-- Cancel Button -->
                        <button type="button" class="btn fw-bold px-4 py-2" data-bs-dismiss="modal" style="
                                    background: var(--card-bg-color); 
                                    color: var(--text-color-dark); 
                                    border: 2px solid var(--primary-color);
                                    border-radius: 10px; 
                                    font-family: var(--primaryFont); 
                                    letter-spacing: 1px; 
                                    transition: all 0.3s ease;
                                    min-width: 100px;
                                " onmouseover="
                                    this.style.background='var(--primary-color)'; 
                                    this.style.color='var(--text-color-light)';
                                    this.style.transform='translateY(-2px)';
                                    this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';
                                " onmouseout="
                                    this.style.background='var(--card-bg-color)'; 
                                    this.style.color='var(--text-color-dark)';
                                    this.style.transform='translateY(0)';
                                    this.style.boxShadow='none';
                                ">
                            CANCEL
                        </button>

                        <!-- Export Button -->
                        <button type="submit" class="btn fw-bold px-4 py-2" style="
                            background: var(--text-color-dark); 
                            color: white; 
                            border: none;
                            border-radius: 10px; 
                            font-family: var(--primaryFont); 
                            letter-spacing: 1px; 
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
                            transition: all 0.3s ease;
                            min-width: 100px;
                        " onmouseover="
                            this.style.background='var(--primary-color)';  
                            this.style.transform='translateY(-2px)';    
                            this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.4)';
                        " onmouseout="
                            this.style.background='var(--text-color-dark)'; 
                            this.style.transform='translateY(0)';
                            this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)';
                        ">
                            <i class="bi bi-download me-2"></i>EXPORT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle form submission with loading state
        const exportForm = document.querySelector('#exportModal form');
        const exportBtn = exportForm.querySelector('button[type="submit"]');

        exportForm.addEventListener('submit', function (e) {
            // Show loading state
            exportBtn.disabled = true;
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>EXPORTING...';

            // Close modal after a brief delay
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();

                // Reset button after modal is closed
                setTimeout(() => {
                    exportBtn.disabled = false;
                    exportBtn.innerHTML = originalText;
                }, 500);
            }, 1000);
        });
    });
</script>