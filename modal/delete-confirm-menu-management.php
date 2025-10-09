<!-- Delete Confirmation Modal -->
<div class='modal fade' id='deleteConfirmModal' data-bs-backdrop='true' tabindex='-1'
    aria-labelledby='deleteConfirmModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content rounded-4 shadow-lg border-0' style='background: var(--bg-color);'>

            <div class='modal-header border-0 pb-0'>
                <h1 class='modal-title fs-4 fw-bold' id='deleteConfirmModalLabel'
                    style='font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;'>
                    Confirm Delete
                </h1>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'
                    style='filter: invert(50%);'></button>
            </div>

            <div class='modal-body'>
                <div class='text-center mb-4'>
                    <i class='bi bi-exclamation-triangle-fill' style='font-size: 4rem; color: #dc3545;'></i>
                </div>

                <div class='text-center mb-4'>
                    <p class='mb-2'
                        style='font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 1.1rem;'>
                        Are you sure you want to delete
                    </p>
                    <p class='fw-bold mb-2'
                        style='font-family: var(--primaryFont); color: var(--text-color-dark); font-size: 1.2rem;'>
                        <strong id='deleteItemName'></strong>
                    </p>
                    <p style='font-family: var(--secondaryFont); color: var(--text-color-dark);'>
                        from your menu?
                    </p>
                    <p class='mt-3 mb-0' style='color: #dc3545; font-family: var(--secondaryFont); font-weight: 600;'>
                        This action cannot be undone!
                    </p>
                </div>

                <form method='POST' action='menu-management.php'>
                    <input type='hidden' name='btnDeleteProduct' value='1'>
                    <input type='hidden' name='productID' id='deleteProductID' value=''>

                    <div class='d-flex gap-3 justify-content-center'>
                        <button type='button' class='btn fw-bold px-4 py-2' data-bs-dismiss='modal' style='background: var(--card-bg-color); color: var(--text-color-dark); 
                           border: 2px solid var(--primary-color); border-radius: 10px;
                           font-family: var(--primaryFont); letter-spacing: 1px;
                           transition: all 0.3s ease; min-width: 100px;'
                            onmouseover='this.style.background="var(--primary-color)"; this.style.color="var(--text-color-light)";'
                            onmouseout='this.style.background="var(--card-bg-color)"; this.style.color="var(--text-color-dark)";'>
                            CANCEL
                        </button>

                        <button type='submit' class='btn fw-bold px-4 py-2' style='background: #dc3545; color: white; border: none; border-radius: 10px;
                           font-family: var(--primaryFont); letter-spacing: 1px;
                           box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3); transition: all 0.3s ease;
                           min-width: 100px;' onmouseover='this.style.background="#b02a37";'
                            onmouseout='this.style.background="#dc3545";'>
                            <i class='bi bi-trash-fill me-2'></i>DELETE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>