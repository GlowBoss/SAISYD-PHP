<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body modalText text-center">
                <p style="font-family: var(--secondaryFont); color: var(--text-color-dark);">
                    Scan this QR code to view the customer menu.
                </p>

                <!-- QR Code -->
                <div class="d-flex justify-content-center my-4">
                    <img src="" alt="QR Code" style="width:200px; height:200px;">
                </div>

                <!-- Direct Link -->
                <a href="../menu.php" target="_blank" style="display:inline-block; margin-top:10px; font-family: var(--secondaryFont);
                          color: var(--primary-color); text-decoration: none; font-weight: bold;">
                    Open Menu Page <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 d-flex justify-content-end">
                <button type="button" class="btn fw-bold px-4 py-2" data-bs-dismiss="modal" style="background: var(--card-bg-color); color: var(--text-color-dark); border: 2px solid var(--primary-color); border-radius:10px;
                           transition: all 0.3s ease;"
                    onmouseover="this.style.background='var(--primary-color)'; this.style.color='var(--text-color-light)'; this.style.transform='translateY(-2px)';"
                    onmouseout="this.style.background='var(--card-bg-color)'; this.style.color='var(--text-color-dark)'; this.style.transform='translateY(0)';">
                    CLOSE
                </button>
            </div>
        </div>
    </div>
</div>
