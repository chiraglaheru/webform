
import { DataTypes } from 'sequelize';

export default (sequelize) => {
  const Experience = sequelize.define('Experience', {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },
    organization: { type: DataTypes.STRING, allowNull: false },
    designation: { type: DataTypes.STRING, allowNull: false },
    fromDate: { type: DataTypes.DATE, allowNull: false },
    toDate: { type: DataTypes.DATE, allowNull: false },
    currentlyWorking: { type: DataTypes.BOOLEAN, allowNull: false },
    currentSalary: { type: DataTypes.STRING, allowNull: true },
    applicationId: { type: DataTypes.INTEGER, allowNull: false },
  });

  Experience.associate = (models) => {
    Experience.belongsTo(models.Application, { foreignKey: 'applicationId' });
  };

  return Experience;
};
