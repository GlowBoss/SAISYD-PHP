document.addEventListener("DOMContentLoaded", function () {

    function setupDownload(downloadBtnId, toastId) {
        const downloadBtn = document.getElementById(downloadBtnId);
        const toastEl = document.getElementById(toastId);

        if (downloadBtn && toastEl) {
            downloadBtn.addEventListener("click", function (e) {
                e.preventDefault(); // prevent default link navigation
                const url = this.href;

                // Trigger download via hidden iframe
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = url;
                document.body.appendChild(iframe);

                // Show toast
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        }
    }

    // Setup for inventory
    setupDownload("downloadInventoryBtn", "inventoryToast");

    // Setup for sales
    setupDownload("downloadSalesBtn", "orderToast");
});
