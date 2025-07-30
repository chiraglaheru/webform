import { DataTypes } from 'sequelize';

export default (sequelize) => {
  const Award = sequelize.define('Award', {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },
    awardTitle: { type: DataTypes.STRING, allowNull: true },
    awardOrganizationName: { type: DataTypes.STRING, allowNull: true },
    awardNature: { type: DataTypes.TEXT, allowNull: true },
    awardOrganizationRecognition: { type: DataTypes.TEXT, allowNull: true },
    applicationId: { type: DataTypes.INTEGER, allowNull: true },
  });

  Award.associate = (models) => {
    Award.belongsTo(models.Application, { foreignKey: 'applicationId' });
  };

  return Award;
};
