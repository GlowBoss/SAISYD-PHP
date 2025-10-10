let currentEditingCard = null;


// IMAGE RESIZE
document.addEventListener('DOMContentLoaded', () => {
  const fileInput = document.getElementById('productImage');
  const finalSize = 1200;

  fileInput.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (event) {
      const img = new Image();
      img.onload = function () {
        // Create square canvas
        const canvas = document.createElement('canvas');
        canvas.width = finalSize;
        canvas.height = finalSize;
        const ctx = canvas.getContext('2d');

        // Calculate scale to fill the square
        const scale = Math.max(finalSize / img.width, finalSize / img.height);
        const x = (finalSize - img.width * scale) / 2;
        const y = (finalSize - img.height * scale) / 2;
        ctx.drawImage(img, x, y, img.width * scale, img.height * scale);


        canvas.toBlob(function (blob) {
          const resizedFile = new File([blob], file.name, {
            type: file.type,
            lastModified: Date.now()
          });
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(resizedFile);
          fileInput.files = dataTransfer.files;
        }, file.type, 0.9); // 90% quality
      };
      img.src = event.target.result;
    };
    reader.readAsDataURL(file);
  });
});


// CREATE INGREDIENT ROW
function createIngredientRow(name = "", qty = "", unit = "") {
  const row = document.createElement("div");
  row.classList.add("row", "g-2", "mb-2", "ingredient-row", "align-items-center");
  row.innerHTML = `
    <div class="col-12 col-md-5 position-relative">
      <input type="text" class="form-control ingredient-search" name="ingredient_name[]" value="${name}" 
             placeholder="Name" required
             style="border: 2px solid var(--primary-color); border-radius: 10px; 
                    font-family: var(--secondaryFont); background: var(--card-bg-color);
                    color: var(--text-color-dark); padding: 12px;">
      <input type="hidden" class="ingredient-id" name="ingredient_id[]">
      <button type="button" class="cancel-search" 
          style="position:absolute; right:8px; top:50%; transform:translateY(-50%);
                 border:none; background:none; color:#333; font-size:18px; display:none; cursor:pointer;">&times;</button>
    </div>

    <div class="col-6 col-md-3">
      <input type="number" class="form-control" name="ingredient_qty[]" value="${qty}" 
             placeholder="Quantity" required
             style="border: 2px solid var(--primary-color); border-radius: 10px; 
                    font-family: var(--secondaryFont); background: var(--card-bg-color);
                    color: var(--text-color-dark); padding: 12px;">
    </div>

    <div class="col-6 col-md-3">
      <select class="form-select measurement-select" name="ingredient_unit[]" required
              style="border: 2px solid var(--primary-color); border-radius: 10px; 
                     font-family: var(--secondaryFont); background: var(--card-bg-color);
                     color: var(--text-color-dark); padding: 12px;">
        <option value="" disabled ${unit === "" ? "selected" : ""}>Select Unit</option>
        <option value="pcs" ${unit === "pcs" ? "selected" : ""}>Piece (pcs)</option>
        <option value="box" ${unit === "box" ? "selected" : ""}>Box</option>
        <option value="pack" ${unit === "pack" ? "selected" : ""}>Pack</option>
        <option value="g" ${unit === "g" ? "selected" : ""}>Gram (g)</option>
        <option value="kg" ${unit === "kg" ? "selected" : ""}>Kilogram (kg)</option>
        <option value="oz" ${unit === "oz" ? "selected" : ""}>Ounce (oz)</option>
        <option value="ml" ${unit === "ml" ? "selected" : ""}>Milliliter (ml)</option>
        <option value="L" ${unit === "L" ? "selected" : ""}>Liter (L)</option>
        <option value="pump" ${unit === "pump" ? "selected" : ""}>Pump</option>
        <option value="tbsp" ${unit === "tbsp" ? "selected" : ""}>Tablespoon (tbsp)</option>
        <option value="tsp" ${unit === "tsp" ? "selected" : ""}>Teaspoon (tsp)</option>
      </select>
    </div>

    <div class="col-12 col-md-1 d-flex justify-content-center mt-2 mt-md-0">
      <button type="button" class="btn btn-sm btn-del remove-ingredient" 
              style="border-radius: 8px; font-family: var(--primaryFont);">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  `;
  return row;
}


// -----------------------------
// AUTOCOMPLETE INIT
// -----------------------------
function initAutocompleteForRow(row) {
  $(row).find(".ingredient-search").autocomplete({
    source: ingredientsData,
    minLength: 1,
    appendTo: row.closest(".modal"),
    select: function (event, ui) {
      $(this).val(ui.item.label);
      $(this).siblings(".ingredient-id").val(ui.item.id);
      $(this).closest(".ingredient-row").find(".measurement-select")
        .data("correct-unit", ui.item.unit);
      return false;
    },
    change: function (event, ui) {
      if (!ui.item) {
        Swal.fire({
          icon: 'error',
          title: 'Ingredient Not Found',
          text: 'The ingredient you entered is not in the Inventory.',
          confirmButtonColor: 'var(--primary-color)'
        });
        $(this).val("");
        $(this).siblings(".ingredient-id").val("");

      }

    }
  });
}

// ----------------------------
// INIT INGREDIENT CONTAINER
// -----------------------------
function initIngredientContainer(containerId, addBtnId) {
  const container = document.getElementById(containerId);
  const addBtn = document.getElementById(addBtnId);

  if (container && addBtn) {
    addBtn.addEventListener("click", () => {
      const newRow = createIngredientRow();
      container.appendChild(newRow);
      initAutocompleteForRow(newRow);
    });

    container.addEventListener("click", (e) => {
      if (e.target.classList.contains("remove-ingredient")) {
        e.target.closest(".ingredient-row").remove();
      }
    });
  }
}

// -----------------------------
// DOCUMENT READY
// -----------------------------
document.addEventListener("DOMContentLoaded", function () {
  initIngredientContainer("ingredients-container", "add-ingredient");
  initIngredientContainer("edit-ingredients-container", "edit-add-ingredient");

  // -----------------------------
  // EDIT BUTTONS
  // -----------------------------
  document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", () => {
      const productId = button.dataset.id;
      currentEditingCard = button.closest(".menu-item");

      fetch(`../assets/menu-management-get-products-ingredients.php?id=${productId}`)
        .then(res => res.json())
        .then(data => {
          const product = data.product;
          const ingredients = data.ingredients;

          if (!product) return;

          const modal = document.getElementById("editModal");
          const form = modal.querySelector("form");

          form.product_id.value = product.productID;
          form.item_name.value = product.productName;
          form.menu_price.value = product.price;
          const categorySelect = form.querySelector("#edit_item_group");
          if (categorySelect) categorySelect.value = product.categoryID;
          const imageText = form.querySelector("#edit_current_image_text");
          if (imageText) imageText.value = product.image;

          const editContainer = document.getElementById("edit-ingredients-container");
          editContainer.innerHTML = "";

          // After creating row in edit modal
          ingredients.forEach((ing) => {
            const row = createIngredientRow(ing.ingredientName, ing.requiredQuantity, ing.measurementUnit);
            row.querySelector(".measurement-select").dataset.correctUnit = ing.measurementUnit;
            editContainer.appendChild(row);
            initAutocompleteForRow(row);
          });

        })
        .catch(err => console.error("Fetch error:", err));
    });
  });

  // -----------------------------
  // SUBMIT EDIT FORM
  // -----------------------------
  document.querySelector("#editModal form").addEventListener("submit", function (e) {
    e.preventDefault();
    const form = this;

    const productId = form.product_id.value;
    const name = form.item_name.value.trim();
    const price = form.menu_price.value.trim();
    const categoryID = form.item_group.value;

    const ingredientNames = Array.from(form.querySelectorAll('input[name="ingredient_name[]"]')).map(i => i.value.trim());
    const ingredientQtys = Array.from(form.querySelectorAll('input[name="ingredient_qty[]"]')).map(i => i.value.trim());
    const ingredientUnits = Array.from(form.querySelectorAll('select[name="ingredient_unit[]"]')).map(i => i.value.trim());

    const ingredients = ingredientNames.map((name, i) => ({
      name, qty: ingredientQtys[i], unit: ingredientUnits[i]
    }));

    const formData = new FormData();
    formData.append("productID", productId);
    formData.append("name", name);
    formData.append("price", price);
    formData.append("categoryID", categoryID);
    formData.append("ingredients", JSON.stringify(ingredients));

    const fileInput = form.querySelector('input[name="attachment"]');
    if (fileInput && fileInput.files[0]) formData.append("attachment", fileInput.files[0]);

    fetch("../assets/menu-management-update-products.php", { method: "POST", body: formData })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
          new bootstrap.Toast(document.getElementById("updateToast")).show();

          if (currentEditingCard) {
            currentEditingCard.querySelector(".menu-name").textContent = name;
            currentEditingCard.querySelector(".menu-price").textContent = `₱${price}`;
            if (fileInput && fileInput.files[0])
              currentEditingCard.querySelector(".menu-img").src = URL.createObjectURL(fileInput.files[0]);
            const availEl = currentEditingCard.querySelector(".text-muted");
            if (availEl && resp.product && resp.product.availableQuantity !== undefined)
              availEl.textContent = `Available: ${resp.product.availableQuantity} pcs`;
          }

          bootstrap.Modal.getInstance(document.getElementById("editModal")).hide();
        } else {
          alert("Error updating product: " + resp.message);
        }
      })
      .catch(err => console.error(err));
  });

  // -----------------------------
  // DELETE BUTTONS
  // -----------------------------
  function deleteButtonHandler() {
    const card = this.closest(".col");
    if (confirm("Are you sure you want to delete this item?")) card.remove();
  }
  document.querySelectorAll(".delete-btn").forEach(btn => btn.addEventListener("click", deleteButtonHandler));

  // -----------------------------
  // ADD NEW PRODUCT FORM
  // -----------------------------
  document.getElementById("addItemForm")?.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!this.checkValidity()) return this.reportValidity();

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
    document.getElementById("productGrid").insertAdjacentHTML("beforeend", productHTML);

    // Reattach handlers for new elements
    document.querySelectorAll(".edit-btn").forEach(button => button.addEventListener("click", editButtonHandler));
    document.querySelectorAll(".delete-btn").forEach(button => button.addEventListener("click", deleteButtonHandler));

    bootstrap.Modal.getInstance(document.getElementById("confirmModal")).hide();
    form.reset();
  });

  // -----------------------------
  // HANDLERS FOR DYNAMIC ELEMENTS
  // -----------------------------
  function editButtonHandler() {
    const productId = this.dataset.id;
    currentEditingCard = this.closest(".menu-item");
    // Fetch and populate modal (handled above)
  }

  // -----------------------------
  // UNIT VALIDATION
  // -----------------------------
  $(document).on("change", ".measurement-select", function () {
    const correctUnit = $(this).data("correct-unit"); // now always exists
    const chosenUnit = $(this).val();
    if (!correctUnit) return;

    const allowedUnits = {
      "g": ["g", "kg", "oz"],
      "kg": ["kg", "g"],
      "oz": ["oz", "g"],
      "ml": ["ml", "L", "pump", "tbsp", "tsp"],
      "L": ["L", "ml"],
      "pump": ["pump", "ml"],
      "tbsp": ["tbsp", "ml"],
      "tsp": ["tsp", "ml"],
      "pcs": ["pcs", "box", "pack"],
      "box": ["box", "pcs"],
      "pack": ["pack", "pcs"]
    };

    const validUnits = allowedUnits[correctUnit] || [correctUnit];
    if (!validUnits.includes(chosenUnit)) {
      Swal.fire({
        icon: 'error',
        title: 'Unit Mismatch',
        text: `This ingredient requires "${correctUnit}" (allowed: ${validUnits.join(", ")}), not "${chosenUnit}".`,
        confirmButtonColor: 'var(--primary-color)'
      });
      $(this).val("");
    }
  });

  $(document).on("input", ".ingredient-search", function () {
    const cancelBtn = $(this).siblings(".cancel-search");
    cancelBtn.toggle($(this).val().trim() !== "");
  });


  $(document).on("click", ".cancel-search", function () {
    skipAutocompleteChange = true; // set flag
    const input = $(this).siblings(".ingredient-search");
    input.val(""); // clear input
    input.siblings(".ingredient-id").val(""); // clear hidden ID
    $(this).hide(); // hide button
    setTimeout(() => skipAutocompleteChange = false, 10); // reset flag after event
  });



});
