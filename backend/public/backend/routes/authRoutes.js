import express from 'express';
import db from '../models/index.js';
import jwt from 'jsonwebtoken';

const authRouter = express.Router();
const User = db.User;
const JWT_SECRET = process.env.JWT_SECRET;

authRouter.post('/signin', async (req, res) => {
  try {
    const { username, password } = req.body;
    console.log('Signin attempt:', { username });
    const user = await User.findOne({ where: { username } });
    if (!user) {
      return res.status(404).json({ message: 'User not found.' });
    }

    const passwordIsValid = password === user.password;
    if (!passwordIsValid) {
      return res.status(401).json({ message: 'Invalid Password!' });
    }

    const token = jwt.sign(
      { id: user.id, username: user.username },
      JWT_SECRET,
      { expiresIn: '1h' }
    );

    res.status(200).json({
      id: user.id,
      username: user.username,
      accessToken: token
    });
  } catch (error) {
    console.error('Error during signin:', error);
    res.status(500).json({ message: 'Internal server error during signin.' });
  }
});

export default authRouter;