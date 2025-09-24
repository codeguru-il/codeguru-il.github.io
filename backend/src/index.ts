import express from "express";
import { user_handler } from "./user_handler";
import cors from "cors";
import mongoose from "mongoose";

const app = express();

app.use(express.json());
app.use(cors());
app.use("/api/users", user_handler);

mongoose.connect(
  process.env.MONGODB_URI || "mongodb://localhost:27017/codeguru"
);

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
