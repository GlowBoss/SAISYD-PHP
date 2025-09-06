<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Add Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control" name="item_name" placeholder="Enter item name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item Group</label>
                                <div class="dropdown">
                                    <button class="form-select text-start" type="button" id="itemGroupDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Select Category
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="itemGroupDropdown">
                                        <li><a class="dropdown-item" href="#" data-value="fruits">Fruits</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="vegetables">Vegetables</a>
                                        </li>
                                        <li><a class="dropdown-item" href="#" data-value="dairy">Dairy</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="beverages">Beverages</a></li>
                                    </ul>
                                    <input type="hidden" name="item_group" id="itemGroupInput" value="">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit Cost</label>
                                <input type="text" class="form-control" name="unitCost" placeholder="Enter unit cost">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">On Hand Quantity</label>
                                <input type="text" class="form-control" name="quantity" placeholder="Enter quantity">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <input type="text" class="form-control" name="unit" placeholder="Kg, pcs, etc.">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" class="form-control" name="supplier"
                                    placeholder="Enter supplier name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Purchase</label>
                                <input type="text" class="form-control" name="lastPurchase"
                                    placeholder="Enter last purchase date">
                            </div>
                        </div>
                    </div>

                    <!-- Standardized Modal Buttons -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btnModal btnCancel me-2"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btnModal btnAdd">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Download Excel Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modalText fw-bold" id="confirmModalLabel">Download Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p class="modalText">Do you want to download the Excel file?</p>
                <div class="border rounded p-3 bg-light text-dark mb-3">
                    <h6 class="mb-2 text-center modalText">File Name: inventory_management.xls</h6>
                </div>
                <div class="d-grid gap-2">
                    <!-- Updated button ID -->
                    <a href="../admin/export_inventory.php" class="btn btnExportDownload" id="downloadInventoryBtn">
                        Download
                    </a>

                    <button type="button" class="btn btnExportCancel" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div id="inventoryToast" class="toast align-items-center text-bg-success position-fixed bottom-0 end-0 m-3 z-3" role="alert"
    aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body modalText">
            ✅ Excel file downloaded successfully!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
            aria-label="Close"></button>
    </div>
</div>
