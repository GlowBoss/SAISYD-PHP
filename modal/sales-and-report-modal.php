<!-- Download Excel Modal -->
<div class="modal fade" id="confirmModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="confirmModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

      <!-- Header -->
      <div class="modal-header border-0 pb-0">
        <h1 class="modal-title fs-4 fw-bold" id="confirmModalLabel"
          style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
          Export Sales Report
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
          <p class="mb-2" style="font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 1.1rem;">
            Export sales report data to Excel file?
          </p>
          <p class="fw-bold mb-2"
            style="font-family: var(--primaryFont); color: var(--text-color-dark); font-size: 1.2rem;">
            <strong>sales_and_report.csv</strong>
          </p>
          <p style="font-family: var(--secondaryFont); color: var(--text-color-dark);">
            This will include transactions, sales report, and summary statistics.
          </p>
        </div>

        <!-- Action Buttons -->
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
          <button type="button" class="btn addbtn btnModal btnDownload fw-bold px-4 py-2" style="
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
      </div>
    </div>
  </div>
</div>

<!-- Success Toast -->
<div id="orderToast" class="toast align-items-center border-0 fade position-fixed bottom-0 end-0 m-3 z-3" role="alert"
  aria-live="assertive" aria-atomic="true" data-bs-delay="3000" data-bs-autohide="true"
  style="background-color: var(--text-color-dark); color: var(--text-color-light); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
  <div class="d-flex align-items-center">
    <i class="bi bi-check-circle-fill ms-3" style="font-size: 1.2rem; color: var(--accent-color);"></i>
    <div class="toast-body" style="font-family: var(--secondaryFont);">
      Excel file exported successfully!
    </div>
    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
      style="filter: invert(1);"></button>
  </div>
</div>