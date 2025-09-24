import express from "express";
import bcrypt from "bcrypt";
import cookie from "cookie";
import { hashPassword, User } from "./user";

export const user_handler = express.Router();



user_handler.get("/test", (req, res) => {
  res.status(200).json({ message: "User handler is working!" });
});

user_handler.post("/register", async (req, res) => {

  const { username, email, password } = req.body;
  if (!username || !email || !password) {
    return res
      .status(400)
      .json({ error: "Username, email, and password are required." });
  }

  const hashedPassword = await hashPassword(password);
  const newUser = new User({ username, email, password: hashedPassword });

  try {
    await newUser.save();
  } catch (error) {
    return res.status(500).json({ error: "Error saving user to database." });
  }
  res
    .status(201)
    .json({ message: "User created successfully", user: { username, email } });
});
user_handler.post("/login", async (req, res) => {
  const { email, password } = req.body;
  if (!email || !password) {
    return res.status(400).json({ error: "Email and password are required." });
  }

  const user = await User.findOne({ email });
  if (!user) {
    return res.status(401).json({ error: "Invalid email or password." });
  }

  const isMatch = await bcrypt.compare(password, user.password);
  if (!isMatch) {
    return res.status(401).json({ error: "Invalid email or password." });
  }
  
  res.status(200).json({
    message: "Login successful",
    user: { username: user.username, email: user.email },
  });
});
