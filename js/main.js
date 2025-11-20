// =========================
// LOGIN HANDLER
// =========================
document.getElementById("loginForm")?.addEventListener("submit", function (e) {
  e.preventDefault();

  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();
  const errorMessage = document.getElementById("errorMessage");

  fetch("/login", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username, password })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      window.location.href = "budget.html";
    } else {
      errorMessage.textContent = "Invalid credentials. Please try again.";
    }
  })
  .catch(err => {
    console.error("Login error:", err);
    errorMessage.textContent = "Server error. Please try again later.";
  });
});

// =========================
// WIZARD NAVIGATION LOGIC
// (only runs on budget.html)
// =========================
let currentPage = 0;
const pages = document.querySelectorAll(".wizard-page");

function showPage(index) {
  if (index < 0 || index >= pages.length) return;
  pages[currentPage].classList.remove("active");
  currentPage = index;
  pages[currentPage].classList.add("active");
}

// Attach listeners dynamically
document.addEventListener("click", function(e) {
  if (e.target && e.target.id === "nextBtn") {
    showPage(currentPage + 1);
  }
  if (e.target && e.target.id === "backBtn") {
    showPage(currentPage - 1);
  }
});

// Handle calculation request
document.getElementById("calcBtn")?.addEventListener("click", async () => {
  const staffId = document.getElementById("staffId").value;
  const effortPercent = document.getElementById("piEffort").value;
  const year = document.getElementById("effortYear").value;

  try {
    const res = await fetch("/budget/calculate", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ staffId, effortPercent, year })
    });
    const data = await res.json();

    // Display results
    document.getElementById("calcResults").innerHTML = `
      <p>Salary: $${data.salary.toFixed(2)}</p>
      <p>Fringe: $${data.fringeCost.toFixed(2)}</p>
      <p>F&A Overhead: $${data.overhead.toFixed(2)}</p>
    `;
  } catch (err) {
    console.error("Error calculating budget:", err);
    document.getElementById("calcResults").textContent = "Calculation failed.";
  }
});


