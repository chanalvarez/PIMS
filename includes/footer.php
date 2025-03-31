    </div>

    <footer class="footer mt-5 py-3">
        <div class="container text-center">
            <p class="text-muted mb-0">
                <i class="fas fa-code me-2"></i>Phone Inventory System management system
                <span class="mx-2">•</span>
                <i class="fas fa-clock me-2"></i><?php echo date('Y'); ?>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add loading state to forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                this.classList.add('loading');
            });
        });

        // Add loading state to buttons
        document.querySelectorAll('button[type="submit"]').forEach(button => {
            button.addEventListener('click', function() {
                this.classList.add('loading');
            });
        });
    </script>
</body>
</html> 