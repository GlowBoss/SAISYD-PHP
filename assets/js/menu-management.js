let currentEditingCard = null;



document.addEventListener("DOMContentLoaded", function () {
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
});



