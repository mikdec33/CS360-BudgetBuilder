// =========================
// LOGIN HANDLER
// =========================
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
        // Save the actual logged-in userId to localStorage
        localStorage.setItem("userId", data.userId);

        // Redirect to dashboard
        window.location.href = "dashboard.html";
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
// DASHBOARD LOGIC
// =========================
async function loadBudgets(userId) {
  try {
    const res = await fetch("/api/budget/list", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ userId })
    });
    const data = await res.json();

    if (data.success) {
      const listDiv = document.getElementById("budgetList");
      listDiv.innerHTML = "";
      data.budgets.forEach(b => {
        const item = document.createElement("div");
        item.className = "budget-item";
        item.innerHTML = `
          <h3>${b.title} (${b.start_year}–${b.end_year})</h3>
          <button onclick="editBudget(${b.budget_id})">Edit</button>
        `;
        listDiv.appendChild(item);
      });
    }
  } catch (err) {
    console.error("Error loading budgets:", err);
  }
}

function editBudget(budgetId) {
  window.location.href = `budget.html?budgetId=${budgetId}`;
}

document.getElementById("newBudgetBtn")?.addEventListener("click", () => {
  window.location.href = "wizard.html"; // start fresh wizard
});

// =========================
// WIZARD NAVIGATION LOGIC
// =========================
let currentPage = 0;
const pages = document.querySelectorAll(".wizard-page");

function showPage(index) {
  if (index < 0 || index >= pages.length) return;
  pages[currentPage].classList.remove("active");
  currentPage = index;
  pages[currentPage].classList.add("active");
}

document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "nextBtn") {
    showPage(currentPage + 1);
  }
  if (e.target && e.target.id === "backBtn") {
    showPage(currentPage - 1);
  }
});

// =========================
// BUDGET CALCULATION REQUEST
// =========================
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

// =========================
// BUDGET LOADER (edit mode)
// =========================
function getQueryParam(name) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(name);
}

async function loadBudget() {
  const budgetId = getQueryParam("budgetId");
  if (!budgetId) return;

  try {
    const res = await fetch(`/api/budget/${budgetId}`);
    const data = await res.json();
    if (!data.success) return;

    const b = data.budget;

    // Overview
    document.getElementById("budgetTitle")?.value = b.title || "";
    document.getElementById("startYear")?.value = b.start_year || "";
    document.getElementById("endYear")?.value = b.end_year || "";

    // Personnel
    if (b.personnel && b.personnel.length > 0) {
      const pi = b.personnel.find(p => p.staff_id === 1);
      const coPi = b.personnel.find(p => p.staff_id === 2);
      if (pi) document.getElementById("piEffort").value = pi.effort_percent;
      if (coPi) document.getElementById("coPiEffort").value = coPi.effort_percent;
    }

    // Student support
    if (b.students && b.students.length > 0) {
      const s = b.students[0];
      document.getElementById("semester")?.value = s.semester || "";
      document.getElementById("tuitionType")?.value = s.tuition_type || "in-state";
      document.getElementById("studentFTE")?.value = s.fte_percent || "";
    }

    // Travel
    if (b.travel && b.travel.length > 0) {
      const t = b.travel[0];
      document.getElementById("destinationType")?.value = t.destination_type || "domestic";
      document.getElementById("durationDays")?.value = t.duration_days || "";
    }

    // Subawards
    if (b.subawards && b.subawards.length > 0) {
      const sa = b.subawards[0];
      document.getElementById("subInstitution")?.value = sa.institution_name || "";
      document.getElementById("subTotal")?.value = sa.subaward_total || "";
      document.getElementById("subRate")?.value = sa.subaward_f_and_a_rate || "";
    }
  } catch (err) {
    console.error("Error loading budget:", err);
  }
}

document.addEventListener("DOMContentLoaded", loadBudget);

// =========================
// WIZARD SUBMISSION LOGIC
// =========================
document.getElementById("submitBtn")?.addEventListener("click", async () => {
  const data = {
    title: document.getElementById("budgetTitle")?.value,
    startYear: document.getElementById("startYear")?.value,
    endYear: document.getElementById("endYear")?.value,
    piEffort: document.getElementById("piEffort")?.value,
    coPiEffort: document.getElementById("coPiEffort")?.value,
    semester: document.getElementById("semester")?.value,
    tuitionType: document.getElementById("tuitionType")?.value,
    studentFTE: document.getElementById("studentFTE")?.value,
    destinationType: document.getElementById("destinationType")?.value,
    durationDays: document.getElementById("durationDays")?.value,
    subInstitution: document.getElementById("subInstitution")?.value,
    subTotal: document.getElementById("subTotal")?.value,
    subRate: document.getElementById("subRate")?.value,
    userId: 1 // replace with actual logged-in userId
  };

  console.log("Collected Wizard Data:", data);

  try {
    const res = await fetch("/api/budget/submit", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      alert("Budget submitted successfully! ID: " + result.budgetId);
      window.location.href = "dashboard.html";
    } else {
      alert("Submission failed. Please try again.");
    }
  } catch (err) {
    console.error("Error submitting budget:", err);
    alert("Submission failed. Please try again.");
  }
});
