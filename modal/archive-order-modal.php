<!-- Archive Order Confirmation Modal -->
<div class='modal fade' id='archiveConfirmModal' data-bs-backdrop='true' tabindex='-1'
    aria-labelledby='archiveConfirmModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content rounded-4 shadow-lg border-0' style='background: var(--bg-color);'>

            <div class='modal-header border-0 pb-0'>
                <h1 class='modal-title fs-4 fw-bold' id='archiveConfirmModalLabel'
                    style='font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;'>
                    Confirm Archive
                </h1>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'
                    style='filter: invert(50%);'></button>
            </div>

            <div class='modal-body'>
                <div class='text-center mb-4'>
                    <i class='bi bi-archive-fill' style='font-size: 4rem; color: var(--text-color-dark)'></i>
                </div>

                <div class='text-center mb-4'>
                    <p class='mb-2'
                        style='font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 1.1rem;'>
                        Are you sure you want to archive
                    </p>
                    <p class='fw-bold mb-2'
                        style='font-family: var(--primaryFont); color: var(--text-color-dark); font-size: 1.2rem;'>
                        <strong id='archiveOrderNumber'></strong>
                    </p>
                    <p style='font-family: var(--secondaryFont); color: var(--text-color-dark);'>
                        This will remove it from the orders list.
                    </p>
                    <p class='mt-3 mb-0'
                        style='color: var(--text-color-dark); font-family: var(--secondaryFont); font-weight: 600;'>
                        You can view archived orders in the order history.
                    </p>
                </div>

                <form method='POST' action='orders.php' id='archiveOrderForm'>
                    <input type='hidden' name='archive_order' value='1'>
                    <input type='hidden' name='orderID' id='archiveOrderID' value=''>

                    <div class='d-flex gap-3 justify-content-center'>
                        <button type='button' class='btn fw-bold px-4 py-2' data-bs-dismiss='modal' style='background: var(--card-bg-color); color: var(--text-color-dark); 
                           border: 2px solid var(--primary-color); border-radius: 10px;
                           font-family: var(--primaryFont); letter-spacing: 1px;
                           transition: all 0.3s ease; min-width: 100px;'
                            onmouseover='this.style.background="var(--primary-color)"; this.style.color="var(--text-color-light)";'
                            onmouseout='this.style.background="var(--card-bg-color)"; this.style.color="var(--text-color-dark)";'>
                            CANCEL
                        </button>

                        <button type="submit" class="btn fw-bold px-4 py-2" style="background:#dc3545; color:white; border:none; border-radius:10px;
                            font-family:var(--primaryFont); letter-spacing:1px; min-width:100px;
                            box-shadow:0 4px 8px rgba(220,53,69,0.3);
                            transition:background 0.3s ease; cursor:pointer;" onmouseover="this.style.background='#b02a37';"
                            onmouseout="this.style.background='#dc3545';">
                            <i class="bi bi-archive-fill me-2"></i>ARCHIVE
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    
    function showArchiveModal(orderID, orderNumber) {
       
        document.getElementById('archiveOrderNumber').textContent = 'Order #' + orderNumber;

       
        document.getElementById('archiveOrderID').value = orderID;

       
        const archiveModal = new bootstrap.Modal(document.getElementById('archiveConfirmModal'));
        archiveModal.show();
    }

    
    document.getElementById('archiveOrderForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('orders.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
              
                const alertContainer = document.getElementById('alertContainer');
                alertContainer.innerHTML = data;

                // Auto-hide alert after 3 seconds
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 3000);

              
                const archiveModal = bootstrap.Modal.getInstance(document.getElementById('archiveConfirmModal'));
                archiveModal.hide();

               
                fetchOrders(currentFilter);
                updateStatusCounts();
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>