let currentEditingCard = null;

document.addEventListener("DOMContentLoaded", function () {
  // ADD MODAL INGREDIENT HANDLING
  const addContainer = document.getElementById("ingredients-container");
  const addBtn = document.getElementById("add-ingredient");

  if (addBtn && addContainer) {
    addBtn.addEventListener("click", function () {
      const newRow = document.createElement("div");
      newRow.classList.add("row", "g-2", "mb-2", "ingredient-row");
      newRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="ingredient_name[]" placeholder="Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="ingredient_qty[]" placeholder="Quantity" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="ingredient_unit[]"
                    placeholder="Unit (pcs, kg, ml)" required>  
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm remove-ingredient">&times;</button>
                </div>
            `;
      addContainer.appendChild(newRow);
    });

    addContainer.addEventListener("click", function (e) {
      if (e.target.classList.contains("remove-ingredient")) {
        e.target.closest(".ingredient-row").remove();
      }
    });
  }

  // EDIT MODAL INGREDIENT HANDLING
  const editContainer = document.getElementById("edit-ingredients-container");
  const editAddBtn = document.getElementById("edit-add-ingredient");

  if (editAddBtn && editContainer) {
    editAddBtn.addEventListener("click", function () {
      const newRow = document.createElement("div");
      newRow.classList.add("row", "g-2", "mb-2", "ingredient-row");
      newRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="ingredient_name[]" placeholder="Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="ingredient_qty[]" placeholder="Quantity" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="ingredient_unit[]"
                    placeholder="Unit (pcs, kg, ml)" required>    
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm remove-ingredient">&times;</button>
                </div>
            `;
      editContainer.appendChild(newRow);
    });

    editContainer.addEventListener("click", function (e) {
      if (e.target.classList.contains("remove-ingredient")) {
        e.target.closest(".ingredient-row").remove();
      }
    });
  }

  // EDIT BUTTON (fills form)
  document.addEventListener("click", function (e) {
    if (e.target.closest(".btn-warning")) {
      const card = e.target.closest(".menu-item");
      currentEditingCard = card;

      const name = card.querySelector(".menu-name").textContent;
      const price = card
        .querySelector(".menu-price")
        .textContent.replace("₱", "");
      const size = card.querySelector(".menu-size").textContent;
      const category = "coffee";

      const editForm = document.querySelector("#editModal form");
      editForm.item_name.value = name;
      editForm.menu_price.value = price;
      editForm.menu_size.value = size;
      editForm.item_group.value = category;
    }
  });

  // EDIT FORM SUBMIT
  document
    .querySelector("#editModal form")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      if (!currentEditingCard) return;

      const name = this.item_name.value.trim();
      const price = this.menu_price.value.trim();
      const size = this.menu_size.value.trim();

      currentEditingCard.querySelector(".menu-name").textContent = name;
      currentEditingCard.querySelector(".menu-price").textContent = `₱${price}`;
      currentEditingCard.querySelector(".menu-size").textContent = size;

      const modal = bootstrap.Modal.getInstance(
        document.getElementById("editModal")
      );
      modal.hide();
    });

  // ADD FORM SUBMIT (confirmModal)
  document
    .querySelector("#confirmModal form")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const name = this.item_name.value.trim();
      const category = this.item_group.value;
      const price = this.menu_price.value.trim();
      const size = this.menu_size.value.trim();

      if (!name || !category || !price || !size) {
        alert("Please fill in all required fields.");
        return;
      }

      const productHTML = `
            <div class="col">
                <div class="menu-item border p-3 rounded shadow-sm text-center width-auto">
                    <img src="../assets/img/coffee.png" alt="${name}" class="img-fluid mb-2" style="max-height: 150px;">
                    <div class="lead menu-name fw-bold">${name}</div>
                    <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                        <span class="lead fw-bold menu-price">₱${price}</span>
                        <span class="lead menu-size">${size}</span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <button class="btn btn-warning btn-sm rounded-4 flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal"
                            data-bs-target="#editModal">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm rounded-4 flex-grow-1 flex-sm-grow-0 delete-btn">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>`;

      document
        .getElementById("productGrid")
        .insertAdjacentHTML("beforeend", productHTML);

      // reattach delete event
      document.querySelectorAll(".delete-btn").forEach((button) => {
        button.onclick = function () {
          const card = this.closest(".col");
          if (confirm("Are you sure you want to delete this item?")) {
            card.remove();
          }
        };
      });

      // close modal
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("confirmModal")
      );
      modal.hide();

      // clear form
      this.reset();
    });
});

document.getElementById("addItemForm").addEventListener("submit", function (e) {
  e.preventDefault(); // prevent normal form submit

  // run built-in validation
  if (this.checkValidity()) {
    // ✅ form is valid → close modal
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("confirmModal")
    );
    modal.hide();

    // 👉 you can also add AJAX here to save the data
  } else {
    // ❌ invalid → let browser show validation errors
    this.reportValidity();
  }
});
