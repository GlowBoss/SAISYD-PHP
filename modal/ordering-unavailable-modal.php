<style>
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7);
    }

    #orderingUnavailableModal .modal-content {
        border: none;   
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
</style>

<div class="modal fade" id="orderingUnavailableModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="orderingUnavailableLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--card-bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-2 justify-content-center">
                <h1 class="modal-title fs-4 fw-bold text-center"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Ordering Currently Unavailable
                </h1>
            </div>

            <!-- Body -->
            <div class="modal-body text-center py-4">
                
                <!-- Warning Icon -->
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle-fill" 
                       style="font-size: 4rem; color: #e74c3c; animation: pulse 2s infinite;"></i>
                </div>

                <!-- Message -->
                <h4 class="mb-3" 
                    style="font-family: var(--primaryFont); color: var(--text-color-dark); font-weight: 600;">
                    We're Sorry!
                </h4>
                
                <p class="mb-4" 
                    style="font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: var(--lead); line-height: 1.6;">
                    Our ordering system is temporarily unavailable for maintenance. 
                </p>

                <div class="mb-4 p-3 rounded-3" style="background: rgba(196, 162, 119, 0.1);">
                    <small style="font-family: var(--secondaryFont); color: var(--text-color-dark);">
                        <i class="bi bi-telephone-fill me-2" style="color: var(--primary-color);"></i>
                        Please try again later or contact us directly for assistance.
                    </small>
                </div>

                <!-- OK/Continue Button -->
                <button type="button" class="btn fw-bold d-flex align-items-center justify-content-center gap-2 mx-auto"
                    onclick="redirectToHomepage()" style="
                        background: var(--primary-color);
                        color: var(--text-color-light);
                        border: none;
                        border-radius: 10px;
                        font-family: var(--primaryFont);
                        font-weight: bold;
                        letter-spacing: 1px;
                        padding: 12px 30px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
                        transition: all 0.3s ease;
                    " onmouseover="
                        this.style.background='var(--btn-hover1)';
                        this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.35)';
                        this.style.transform='translateY(-2px)';
                    " onmouseout="
                        this.style.background='var(--primary-color)';
                        this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.25)';
                        this.style.transform='translateY(0)';
                    ">
                    <i class="bi bi-house-fill"></i> 
                    RETURN TO HOMEPAGE
                </button>

            </div>
        </div>
    </div>
</div>


<script>
    // Function to redirect to homepage
    function redirectToHomepage() {
        window.location.href = 'index.php'; 
    }

    // Prevent modal from closing by clicking backdrop or pressing escape
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('orderingUnavailableModal');
        
        if (modalElement) {
            modalElement.addEventListener('hide.bs.modal', function (event) {

                if (!event.target.closest('button[onclick="redirectToHomepage()"]')) {
                    event.preventDefault();
                    return false;
                }
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                redirectToHomepage();
            });
        }
    });

    // Function to show the unavailable modal
    function showOrderingUnavailableModal() {
        const modal = new bootstrap.Modal(document.getElementById('orderingUnavailableModal'));
        modal.show();
    }
</script>