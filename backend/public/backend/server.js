import express from 'express'
import dotenv from 'dotenv'
import db from './models/index.js'
import authRouter from './routes/authRoutes.js';
import cors from 'cors';

dotenv.config(); // Load environment variables from .env file

const app = express();

// Middleware
app.use(cors());
app.use(express.json()); // Parse JSON request bodies
app.use(express.urlencoded({ extended: true })); // Parse URL-encoded request bodies

// Simple root route
app.get('/', (req, res) => {
  res.json({ message: 'Welcome to the authentication application.' });
});

// Authentication routes
app.use('/api/auth', authRouter);

// Database synchronization
db.sequelize.sync()
  .then(() => {
    console.log('Database synced successfully.');
    // You can add initial data (e.g., admin user) here if needed
  })
  .catch(err => {
    console.error('Failed to sync database:', err.message);
  });

// Error handling middleware (optional but recommended)
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).send('Something broke!');
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}.`);
});
