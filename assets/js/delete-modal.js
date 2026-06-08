document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        const name = this.dataset.name;   // name to show in modal
        const href = this.dataset.href;   // delete link

        // Fill modal content
        document.getElementById('modalItemName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = href;

        // Show Bootstrap modal
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });
});
