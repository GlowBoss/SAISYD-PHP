document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggle = document.getElementById('itemGroupDropdown');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const hiddenInput = document.getElementById('itemGroupInput');

    if (!dropdownToggle) return; // check if modal exists

    dropdownItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const value = this.getAttribute('data-value');
            const text = this.textContent;

            dropdownToggle.textContent = text;
            hiddenInput.value = value;
        });
    });
});


// Edit modal Save
document.addEventListener("DOMContentLoaded", () => {
  const editForm = document.querySelector("#editItemModal form");

  if (editForm) {
    editForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(editForm);

      fetch("inventory-edit.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Inventory updated successfully!");
          location.reload();
        } else {
          alert("Error: " + (data.message || "Update failed"));
        }
      })
      .catch(err => {
        console.error("AJAX error:", err);
        alert("Something went wrong.");
      });
    });
  }
});


