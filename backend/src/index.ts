import express from "express";
import { user_handler } from "./user_handler";

const app = express();

app.use(express.json());
app.use("/users", user_handler);

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});