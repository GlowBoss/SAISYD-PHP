<style>
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }


    body.modal-blur-active > *:not(.modal):not(.modal-backdrop):not(script) {
        filter: blur(8px);
        transition: filter 0.3s ease;
    }

    body.modal-blur-active .modal,
    body.modal-blur-active .modal-backdrop {
        filter: none !important;
    }

  
    body.modal-blur-active {
        overflow: hidden;
    }

    #orderingUnavailableModal .modal-content {
        border: none;   
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.4s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }


    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-spinner {
        display: none;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 0.8s linear infinite;
        margin-left: 8px;
    }

    .btn-loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .btn-loading .loading-spinner {
        display: inline-block;
    }

    .btn-loading .button-text {
        opacity: 0.7;
    }


    @media (max-width: 576px) {
        #orderingUnavailableModal .modal-body {
            padding: 1.5rem 1rem !important;
        }
        
        #orderingUnavailableModal .modal-body i.bi-exclamation-triangle-fill {
            font-size: 3rem !important;
        }
        
        #orderingUnavailableModal .modal-body h4 {
            font-size: 1.25rem !important;
        }
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

             
                <button type="button" id="returnHomeBtn" class="btn fw-bold d-flex align-items-center justify-content-center gap-2 mx-auto"
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
                        min-width: 220px;
                    " onmouseover="
                        if (!this.classList.contains('btn-loading')) {
                            this.style.background='var(--btn-hover1)';
                            this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.35)';
                            this.style.transform='translateY(-2px)';
                        }
                    " onmouseout="
                        if (!this.classList.contains('btn-loading')) {
                            this.style.background='var(--primary-color)';
                            this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.25)';
                            this.style.transform='translateY(0)';
                        }
                    ">
                    <span class="button-text">
                        <i class="bi bi-house-fill"></i> 
                        RETURN TO HOMEPAGE
                    </span>
                    <div class="loading-spinner"></div>
                </button>

            </div>
        </div>
    </div>
</div>


<script>
  
    function redirectToHomepage() {
        const btn = document.getElementById('returnHomeBtn');
        const buttonText = btn.querySelector('.button-text');
        
     
        btn.classList.add('btn-loading');
        btn.disabled = true;
        
        buttonText.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Returning...';
        
      
        setTimeout(() => {
            window.location.href = 'index.php'; 
        }, 800);
    }

   
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('orderingUnavailableModal');
        
        if (modalElement) {
            modalElement.addEventListener('hide.bs.modal', function (event) {
            
                const btn = document.getElementById('returnHomeBtn');
                if (!btn.classList.contains('btn-loading')) {
                    event.preventDefault();
                    return false;
                }
            });

            
            modalElement.addEventListener('hidden.bs.modal', function () {
               
                document.body.classList.remove('modal-blur-active');
                document.body.style.overflow = '';
            });
        }
    });

    
    function showOrderingUnavailableModal() {
        const modalElement = document.getElementById('orderingUnavailableModal');
        
        if (modalElement) {
          
            document.body.classList.add('modal-blur-active');
            
        
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }, 100);
        }
    }
</script>