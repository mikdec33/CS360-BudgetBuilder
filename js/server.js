const express = require("express");
const bodyParser = require("body-parser");
const authRoutes = require("./routes/auth");
const budgetRoutes = require("./routes/budget");
const exportRoutes = require("./routes/export");

const app = express();
app.use(bodyParser.json());

app.use("/login", authRoutes);
app.use("/budget", budgetRoutes);
app.use("/export", exportRoutes);

app.listen(3000, () => console.log("Server running on port 3000"));

