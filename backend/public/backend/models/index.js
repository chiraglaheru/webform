import { Sequelize } from 'sequelize';
import dotenv from 'dotenv';
import userModel from './userModel.js';

dotenv.config();

const sequelize = new Sequelize('form_data', 'root', '1234', {
  dialect: 'mysql',
  dialectOptions: {
    socketPath: '/Applications/MAMP/tmp/mysql/mysql.sock'
  }
});



const db = {};
db.Sequelize = Sequelize;
db.sequelize = sequelize;
db.User = userModel(sequelize); // Correct usage

export default db;