import { DataTypes } from 'sequelize';

export default (sequelize) => {
  const Application = sequelize.define('Application', {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },

    postAppliedFor: { type: DataTypes.STRING },
    title: { type: DataTypes.STRING },
    firstName: { type: DataTypes.STRING },
    middleName: { type: DataTypes.STRING },
    lastName: { type: DataTypes.STRING },
    gender: { type: DataTypes.STRING },
    dob: { type: DataTypes.STRING },
    age: { type: DataTypes.INTEGER },
    email: { type: DataTypes.STRING },
    altEmail: { type: DataTypes.STRING },
    mobile: { type: DataTypes.STRING },
    altMobile: { type: DataTypes.STRING },

    address: { type: DataTypes.STRING },
    state: { type: DataTypes.STRING },
    city: { type: DataTypes.STRING },
    pinCode: { type: DataTypes.STRING },

    caste: { type: DataTypes.STRING },
    aadhar: { type: DataTypes.STRING },
    pan: { type: DataTypes.STRING },

    resumeFile: { type: DataTypes.STRING },
    declaration: { type: DataTypes.BOOLEAN },

    maritalStatus: { type: DataTypes.STRING },
    institute: { type: DataTypes.STRING },
  });

  Application.associate = (models) => {
    Application.hasMany(models.Qualification, { foreignKey: 'applicationId', onDelete: 'CASCADE' });
    Application.hasMany(models.Experience, { foreignKey: 'applicationId', onDelete: 'CASCADE' });
    Application.hasMany(models.Award, { foreignKey: 'applicationId', onDelete: 'CASCADE' });
    Application.hasMany(models.Publication, { foreignKey: 'applicationId', onDelete: 'CASCADE' });
    Application.hasMany(models.Course, { foreignKey: 'applicationId', onDelete: 'CASCADE' });
    Application.hasMany(models.Phd, { foreignKey: 'applicationId', onDelete: 'CASCADE' });
    Application.hasOne(models.AdditionalInfo, { foreignKey: 'applicationId', onDelete: 'CASCADE' });
  };

  return Application;
};
