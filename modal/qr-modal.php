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
                    <div id="qrcode"></div>
                </div>

                <!-- Direct Link -->
                <a href="../menu.php" target="_blank" style="display:inline-block; margin-top:10px; font-family: var(--secondaryFont);
                          color: var(--primary-color); text-decoration: none; font-weight: bold;">
                    Open Menu Page <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 d-flex justify-content-end">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var qrContainer = document.getElementById("qrcode");
    var qrModal = document.getElementById("qrModal");

    qrModal.addEventListener("shown.bs.modal", function () {
        qrContainer.innerHTML = "";

        // The URL you want the QR code to open
        var menuUrl = "https://saisydcafe.com/menu.php";

        // API endpoint with parameters
        var apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(menuUrl);

        var qrImage = document.createElement("img");
        qrImage.src = apiUrl;
        qrImage.alt = "QR Code";
        qrImage.width = 200;
        qrImage.height = 200;
        qrImage.classList.add("rounded", "shadow-sm");

        qrContainer.appendChild(qrImage);
    });
});
</script>

