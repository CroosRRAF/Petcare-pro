// Footer JavaScript
function subscribeNewsletter() {
  const email = document.getElementById("newsletter-email").value;

  if (!email) {
    alert("Please enter your email address");
    return;
  }

  // Email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert("Please enter a valid email address");
    return;
  }

  // Here you would typically send the email to your backend
  // For now, we'll just show a success message
  alert("Thank you for subscribing to our newsletter!");
  document.getElementById("newsletter-email").value = "";

  // You can add AJAX call here to send data to backend
  // Example:
  // fetch('/Petcare-GrocesaryShop/Zyora-PetCare/api/subscribe.php', {
  //     method: 'POST',
  //     headers: {
  //         'Content-Type': 'application/json',
  //     },
  //     body: JSON.stringify({ email: email })
  // })
  // .then(response => response.json())
  // .then(data => {
  //     if (data.success) {
  //         alert('Thank you for subscribing!');
  //         document.getElementById('newsletter-email').value = '';
  //     }
  // });
}

// Allow Enter key to submit newsletter
document.addEventListener("DOMContentLoaded", function () {
  const newsletterEmail = document.getElementById("newsletter-email");
  if (newsletterEmail) {
    newsletterEmail.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        subscribeNewsletter();
      }
    });
  }
});
