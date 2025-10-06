<!-- Confirm Toggle Modal -->
<div class="modal fade" id="confirmToggleModal" data-bs-backdrop="true" tabindex="-1"
  aria-labelledby="confirmToggleLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

      <!-- Header -->
      <div class="modal-header border-0 pb-2 px-3 px-sm-4 d-flex justify-content-between align-items-center">
        <h1 class="modal-title fs-5 fs-sm-4 fw-bold" id="confirmToggleLabel"
          style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
          Confirm Action
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
          style="filter: invert(50%);"></button>
      </div>

      <!-- Body -->
      <div class="modal-body px-3 px-sm-4">
        <!-- Alert Icon -->
        <div class="text-center mb-3 mb-sm-4">
          <i class="bi bi-question-circle-fill" style="font-size: 3rem; color: var(--primary-color);"></i>
        </div>

        <!-- Message -->
        <div class="text-center mb-3 mb-sm-4">
          <p id="confirmToggleText" class="mb-2 fs-6 fs-sm-5"
            style="font-family: var(--secondaryFont); color: var(--text-color-dark); line-height: 1.4;">
            Are you sure you want to change this setting?
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3 justify-content-center">
          <!-- Cancel Button -->
          <button type="button" id="cancelToggle" class="btn fw-bold px-3 px-sm-4 py-2 order-2 order-sm-1"
            data-bs-dismiss="modal" style="
              background: var(--card-bg-color); 
              color: var(--text-color-dark); 
              border: 2px solid var(--primary-color);
              border-radius: 10px; 
              font-family: var(--primaryFont); 
              letter-spacing: 1px; 
              transition: all 0.3s ease;
              min-width: 100px;
              font-size: 0.9rem;
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
            " ontouchstart="
              this.style.background='var(--primary-color)'; 
              this.style.color='var(--text-color-light)';
            " ontouchend="
              setTimeout(() => {
                this.style.background='var(--card-bg-color)'; 
                this.style.color='var(--text-color-dark)';
              }, 150);
            ">
            CANCEL
          </button>

          <!-- Confirm Button -->
          <button type="button" id="confirmToggle" class="btn fw-bold px-3 px-sm-4 py-2 order-1 order-sm-2" style="
              background: var(--text-color-dark); 
              color: white; 
              border: none;
              border-radius: 10px; 
              font-family: var(--primaryFont); 
              letter-spacing: 1px; 
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
              transition: all 0.3s ease;
              min-width: 130px;
              font-size: 0.9rem;
              position: relative;
            " onmouseover="
              if (!this.disabled) {
                this.style.background='var(--primary-color)';  
                this.style.transform='translateY(-2px)';    
                this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.4)';
              }
            " onmouseout="
              if (!this.disabled) {
                this.style.background='var(--text-color-dark)'; 
                this.style.transform='translateY(0)';
                this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)';
              }
            " ontouchstart="
              if (!this.disabled) {
                this.style.background='var(--primary-color)';
              }
            " ontouchend="
              if (!this.disabled) {
                setTimeout(() => {
                  this.style.background='var(--text-color-dark)';
                }, 150);
              }
            ">
            <span class="btn-text">YES, CONTINUE</span>
            <span class="btn-spinner d-none">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <span class="ms-2">Loading...</span>
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Additional responsive styles */
  @media (max-width: 576px) {
    #confirmToggleModal .modal-dialog {
      margin: 0.5rem;
      max-width: calc(100% - 1rem);
    }

    #confirmToggleModal .modal-content {
      border-radius: 1rem !important;
    }

    #confirmToggleModal .modal-header .modal-title {
      font-size: 1.1rem !important;
      line-height: 1.3;
    }

    #confirmToggleModal .modal-body {
      padding: 1rem !important;
    }
  }

  @media (max-width: 375px) {
    #confirmToggleModal .modal-dialog {
      margin: 0.25rem;
      max-width: calc(100% - 0.5rem);
    }

    #confirmToggleModal .btn {
      font-size: 0.8rem !important;
      padding: 0.5rem 1rem !important;
    }

    #confirmToggleModal .modal-header .btn-close {
      padding: 0.25rem;
      margin: -0.25rem;
    }
  }

  /* Touch device optimizations */
  @media (hover: none) and (pointer: coarse) {
    #confirmToggleModal .btn:hover {
      transform: none !important;
    }

    #confirmToggleModal .btn:active {
      transform: translateY(1px) !important;
      transition: transform 0.1s ease !important;
    }
  }

  /* Loading state styles */
  #confirmToggle:disabled {
    opacity: 0.7;
    cursor: not-allowed !important;
    pointer-events: none;
  }

  #confirmToggle .btn-spinner {
    display: none;
  }

  #confirmToggle.loading .btn-text {
    display: none;
  }

  #confirmToggle.loading .btn-spinner {
    display: inline-block !important;
  }

  #confirmToggle.loading {
    transform: translateY(0) !important;
  }

  #confirmToggle.loading:hover {
    background: var(--text-color-dark) !important;
    transform: translateY(0) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const confirmToggleModal = document.getElementById('confirmToggleModal');

    if (confirmToggleModal) {
    
      confirmToggleModal.addEventListener('show.bs.modal', function () {
        const modalContent = this.querySelector('.modal-content');
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.9)';

        setTimeout(() => {
          modalContent.style.transition = 'all 0.3s ease';
          modalContent.style.opacity = '1';
          modalContent.style.transform = 'scale(1)';
        }, 10);
      });

    
      confirmToggleModal.addEventListener('click', function (e) {
        if (e.target === this) {
          if (window.innerWidth > 576) {
            bootstrap.Modal.getInstance(this).hide();
          }
        }
      });

      // Loading state functionality
      const confirmBtn = document.getElementById('confirmToggle');
      const cancelBtn = document.getElementById('cancelToggle');

      if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
      
          this.classList.add('loading');
          this.disabled = true;
          cancelBtn.disabled = true;

        });
      }

      confirmToggleModal.addEventListener('hidden.bs.modal', function () {
        confirmBtn.classList.remove('loading');
        confirmBtn.disabled = false;
        cancelBtn.disabled = false;
        confirmBtn.style.background = 'var(--text-color-dark)';
        confirmBtn.style.transform = 'translateY(0)';
        confirmBtn.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.3)';
      });
    }
  });

  function showConfirmLoading() {
    const confirmBtn = document.getElementById('confirmToggle');
    const cancelBtn = document.getElementById('cancelToggle');
    if (confirmBtn) {
      confirmBtn.classList.add('loading');
      confirmBtn.disabled = true;
      cancelBtn.disabled = true;
    }
  }

  function hideConfirmLoading() {
    const confirmBtn = document.getElementById('confirmToggle');
    const cancelBtn = document.getElementById('cancelToggle');
    if (confirmBtn) {
      confirmBtn.classList.remove('loading');
      confirmBtn.disabled = false;
      cancelBtn.disabled = false;
    }
  }


  function closeConfirmModal() {
    const confirmToggleModal = document.getElementById('confirmToggleModal');
    if (confirmToggleModal) {
      bootstrap.Modal.getInstance(confirmToggleModal).hide();
    }
  }
</script>