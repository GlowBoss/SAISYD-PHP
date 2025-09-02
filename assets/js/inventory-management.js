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

