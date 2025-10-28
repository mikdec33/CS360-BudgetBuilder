document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();
  
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const errorMessage = document.getElementById("errorMessage");
  
    if (username === "Mikayla" && password === "sailboat") {
      window.location.href = "budget.html";
    } else if (username === "Aiden" && password === "sailboat") {
      window.location.href = "budget.html";
    }
    else {
      errorMessage.textContent = "Invalid credentials. Please try again.";
    }
});
  