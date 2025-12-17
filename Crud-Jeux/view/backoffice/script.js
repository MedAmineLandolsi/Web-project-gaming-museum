// Wait for DOM to load
document.addEventListener("DOMContentLoaded", () => {
    const fields = {
        nom: document.querySelector("input[name='nom']"),
        description: document.querySelector("textarea[name='description']"),
        prix: document.querySelector("input[name='prix']"),
        stock: document.querySelector("input[name='stock']"),
        categorie: document.querySelector("input[name='categorie']")
    };

    const form = document.querySelector("form");

    // Add basic controlled input listeners
    Object.keys(fields).forEach(key => {
        let field = fields[key];

        field.addEventListener("input", () => {
            validateField(field, key);
        });
    });

    // Validation rules
    function validateField(field, key) {

        // Remove previous error state
        field.classList.remove("input-error");

        // Text fields must not be empty
        if (["nom", "description", "categorie"].includes(key)) {
            if (field.value.trim() === "") {
                field.classList.add("input-error");
            }
        }

        // Numeric validation
        if (["prix", "stock"].includes(key)) {
            if (field.value < 0 || field.value.trim() === "") {
                field.classList.add("input-error");
            }
        }
    }

    // Global validation before submitting
    form?.addEventListener("submit", (e) => {
        let valid = true;

        Object.keys(fields).forEach(key => {
            let field = fields[key];
            validateField(field, key);

            if (field.classList.contains("input-error")) {
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert("Veuillez remplir correctement tous les champs.");
        }
    });
});
