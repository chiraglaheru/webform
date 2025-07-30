// db.js
import { Sequelize } from 'sequelize';
import dotenv from 'dotenv';
import ApplicationModel from './Application.js';
import QualificationModel from './Qualification.js';
import ExperienceModel from './Experience.js';
import AwardModel from './Award.js';
import PublicationModel from './Publication.js';
import CourseModel from './Course.js';
import PhdModel from './Phd.js';
import AdditionalInfoModel from './AdditionalInfo.js';

dotenv.config();

const sequelize = new Sequelize({
  database: process.env.DB_DATABASE,
  username: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  host: process.env.DB_HOST,
  port: 8889,
  dialect: 'mysql',
  dialectOptions: {
    socketPath: '/Applications/MAMP/tmp/mysql/mysql.sock'
  }
});

// Initialize models
const db = {};
db.Sequelize = Sequelize;
db.sequelize = sequelize;

db.Application = ApplicationModel(sequelize);
db.Qualification = QualificationModel(sequelize);
db.Experience = ExperienceModel(sequelize);
db.Award = AwardModel(sequelize);
db.Publication = PublicationModel(sequelize);
db.Course = CourseModel(sequelize);
db.Phd = PhdModel(sequelize);
db.AdditionalInfo = AdditionalInfoModel(sequelize);

// Setup associations
Object.values(db).forEach(model => {
  if (model.associate) {
    model.associate(db);
  }
});

export default db;
