const express = require("express");
const bodyParser = require("body-parser");
const authRoutes = require("./routes/auth");
const budgetRoutes = require("./routes/budget");
const exportRoutes = require("./routes/export");
const registerRouter = require("./routes/register");
const loginRouter = require("./routes/login");

const app = express();
app.use(bodyParser.json());

app.use("/login", authRoutes);
app.use("/budget", budgetRoutes);
app.use("/export", exportRoutes);
app.use("/register", registerRouter);
app.use("/login", loginRouter);

app.listen(3000, () => console.log("Server running on port 3000"));

