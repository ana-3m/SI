document.addEventListener("DOMContentLoaded", () => {
    const reviewsContainer = document.getElementById("reviews");

    // Fetch and display reviews
    function loadReviews() {
        fetch("/reviews")
            .then((response) => response.json())
            .then((reviews) => {
                reviewsContainer.innerHTML = ""; // Clear previous reviews
                reviews.forEach((review) => {
                    const div = document.createElement("div");
                    div.classList.add("review-item");
                    div.innerHTML = `
                        <p><strong>Name:</strong> ${review.uname}</p>
                        <p><strong>Email:</strong> ${review.email}</p>
                        <p><strong>Phone:</strong> ${review.phone}</p>
                        <p><strong>Satisfaction:</strong> ${review.satisfaction}</p>
                        <p><strong>Suggestions:</strong> ${review.suggestions}</p>
                    `;
                    reviewsContainer.appendChild(div);
                });
            });
    }

    // Submit review
    document.getElementById("submit-btn").addEventListener("click", () => {
        const uname = document.getElementById("uname").value;
        const email = document.getElementById("email").value;
        const phone = document.getElementById("phone").value;
        const satisfaction = document.querySelector('input[name="satisfaction"]:checked').value;
        const suggestions = document.getElementById("suggestions").value;

        if (uname && email && phone && suggestions) {
            const review = { uname, email, phone, satisfaction, suggestions };

            fetch("/submit-review", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(review),
            })
                .then((response) => response.text())
                .then((message) => {
                    alert(message);
                    loadReviews();
                    document.getElementById("feedback-form").reset();
                });
        } else {
            alert("Please fill out all fields.");
        }
    });

    // Initial load of reviews
    loadReviews();
});
