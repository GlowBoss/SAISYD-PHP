// CATEGORY BUTTONS - active toggle
document.querySelectorAll(".categorybtn").forEach(btn => {
  btn.addEventListener("click", function () {
    document.querySelectorAll(".categorybtn")
      .forEach(b => b.classList.remove("categorybtn-active"));
    this.classList.add("categorybtn-active");
  });
});

// DROPDOWN - change text on select
document.querySelectorAll('.dropdown').forEach(dropdown => {
  const button = dropdown.querySelector('.dropdown-toggle');
  const items = dropdown.querySelectorAll('.dropdown-item');

  items.forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      const newText = item.textContent.trim();

      // update button text
      button.textContent = newText;

      // save value if needed
      button.dataset.value = newText.toLowerCase();
    });
  });
});
