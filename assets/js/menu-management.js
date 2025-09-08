let currentEditingCard = null;

document.addEventListener("DOMContentLoaded", function () {
  // -----------------------------
  // HELPER: CREATE INGREDIENT ROW
  // -----------------------------
  function createIngredientRow(name = "", qty = "", unit = "") {
    const row = document.createElement("div");
    row.classList.add("row", "g-2", "mb-2", "ingredient-row");
    row.innerHTML = `
      <div class="col-md-5">
        <input type="text" class="form-control" name="ingredient_name[]" value="${name}" placeholder="Name" required>
      </div>
      <div class="col-md-3">
        <input type="number" class="form-control" name="ingredient_qty[]" value="${qty}" placeholder="Quantity" required>
      </div>
      <div class="col-md-3">
        <input type="text" class="form-control" name="ingredient_unit[]" value="${unit}" placeholder="Unit (pcs, kg, ml)" required>
      </div>
      <div class="col-md-1 d-flex align-items-center">
        <button type="button" class="btn btn-sm remove-ingredient">&times;</button>
      </div>
    `;
    return row;
  }

  // -----------------------------
  // ADD NEW INGREDIENT (Add / Edit Modal)
  // -----------------------------
  function initIngredientContainer(containerId, addBtnId) {
    const container = document.getElementById(containerId);
    const addBtn = document.getElementById(addBtnId);

    if (addBtn && container) {
      addBtn.addEventListener("click", () => {
        const newRow = createIngredientRow();
        container.appendChild(newRow);
      });

      container.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-ingredient")) {
          e.target.closest(".ingredient-row").remove();
        }
      });
    }
  }

  initIngredientContainer("ingredients-container", "add-ingredient"); // Add Modal
  initIngredientContainer("edit-ingredients-container", "edit-add-ingredient"); // Edit Modal

  // -----------------------------
  // EDIT BUTTONS (All Cards)
  // -----------------------------
  document.querySelectorAll(".edit-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const productId = button.dataset.id;
      currentEditingCard = button.closest(".menu-item"); // Track which card is being edited

      fetch(`../assets/menu-management-get-products.php?id=${productId}`)
        .then((res) => res.json())
        .then((data) => {
          const product = data.product;
          const ingredients = data.ingredients;

          if (!product) {
            console.error("Invalid product data", data);
            return;
          }

          const modal = document.getElementById("editModal");
          const form = modal.querySelector("form");

          // Fill product info
          form.product_id.value = product.productID;
          form.item_name.value = product.productName;
          form.menu_price.value = product.price;

          // Fill ingredients
          const editContainer = document.getElementById(
            "edit-ingredients-container"
          );
          editContainer.innerHTML = ""; // Clear previous
          ingredients.forEach((ing) => {
            const row = createIngredientRow(
              ing.ingredientName,
              ing.requiredQuantity,
              ing.measurementUnit
            );
            editContainer.appendChild(row);
          });
        })
        .catch((err) => console.error("Fetch error:", err));
    });
  });

  // -----------------------------
  // EDIT FORM SUBMIT
  // -----------------------------
  document
    .querySelector("#editModal form")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const form = this;
      const productId = form.product_id.value;
      const name = form.item_name.value.trim();
      const price = form.menu_price.value.trim();

      // Collect ingredients
      const ingredientNames = Array.from(
        form.querySelectorAll('input[name="ingredient_name[]"]')
      ).map((input) => input.value.trim());
      const ingredientQtys = Array.from(
        form.querySelectorAll('input[name="ingredient_qty[]"]')
      ).map((input) => input.value.trim());
      const ingredientUnits = Array.from(
        form.querySelectorAll('input[name="ingredient_unit[]"]')
      ).map((input) => input.value.trim());

      // Prepare payload
      const data = {
        productID: productId,
        name,
        price,
        ingredients: ingredientNames.map((name, i) => ({
          name,
          qty: ingredientQtys[i],
          unit: ingredientUnits[i],
        })),
      };

      // Send AJAX request
      fetch("../assets/menu-management-update-products.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then((res) => res.json())
        .then((response) => {
          if (response.success) {
            // Show toast
            const toastEl = document.getElementById("updateToast");
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Optionally update the card dynamically without reloading
            if (currentEditingCard) {
              currentEditingCard.querySelector(".menu-name").textContent = name;
              currentEditingCard.querySelector(
                ".menu-price"
              ).textContent = `₱${price}`;
            }

            // Close edit modal
            const modal = bootstrap.Modal.getInstance(
              document.getElementById("editModal")
            );
            modal.hide();
          } else {
            // Optionally, create an error toast
            alert("Error updating product: " + response.message);
          }
        })
        .catch((err) => console.error(err));
    });

  // -----------------------------
  // DELETE BUTTONS
  // -----------------------------
  document.querySelectorAll(".delete-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const card = button.closest(".col");
      if (confirm("Are you sure you want to delete this item?")) {
        card.remove();
      }
    });
  });

  // -----------------------------
  // ADD FORM SUBMIT (New Product)
  // -----------------------------
  document
    .getElementById("addItemForm")
    ?.addEventListener("submit", function (e) {
      e.preventDefault();

      if (!this.checkValidity()) {
        this.reportValidity();
        return;
      }

      const form = this;
      const name = form.item_name.value.trim();
      const price = form.menu_price.value.trim();

      const productHTML = `
      <div class="col-6 col-md-4 col-lg-2">
        <div class="menu-item border p-3 rounded shadow-sm text-center">
          <img src="../assets/img/coffee.png" alt="${name}" class="img-fluid mb-2 menu-img">
          <div class="lead menu-name fs-6">${name}</div>
          <div class="d-flex justify-content-center align-items-center gap-2 my-2">
            <span class="lead fw-bold menu-price">₱${price}</span>
          </div>
          <div class="d-flex flex-wrap justify-content-center gap-2">
            <button class="btn btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-id="">
              <i class="bi-pencil-square"></i> Edit
            </button>
            <button class="btn btn-sm delete-btn">
              <i class="bi-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>
    `;

      document
        .getElementById("productGrid")
        .insertAdjacentHTML("beforeend", productHTML);

      // Reattach edit/delete handlers for the new card
      document.querySelectorAll(".edit-btn").forEach((button) => {
        button.addEventListener("click", editButtonHandler);
      });
      document.querySelectorAll(".delete-btn").forEach((button) => {
        button.addEventListener("click", deleteButtonHandler);
      });

      // Close modal and reset form
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("confirmModal")
      );
      modal.hide();
      form.reset();
    });

  // -----------------------------
  // HANDLERS FOR NEWLY ADDED ELEMENTS
  // -----------------------------
  function editButtonHandler() {
    const productId = this.dataset.id;
    currentEditingCard = this.closest(".menu-item");
    // Fetch and populate modal (same as above)
  }

  function deleteButtonHandler() {
    const card = this.closest(".col");
    if (confirm("Are you sure you want to delete this item?")) card.remove();
  }
});
