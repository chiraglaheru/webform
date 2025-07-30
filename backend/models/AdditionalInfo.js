import { DataTypes } from "sequelize";

export default (sequelize) => {
  const AdditionalInfo = sequelize.define("AdditionalInfo", {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },
    referenceName: { type: DataTypes.TEXT ,allowNull: true },
    appliedFor: { type: DataTypes.TEXT,allowNull: true },  
    currentSalary: { type: DataTypes.TEXT ,allowNull: false },
    expectedSalary: { type: DataTypes.TEXT ,allowNull: false},
    extraCurricular: { type: DataTypes.TEXT ,allowNull: true},
    applicationId: { type: DataTypes.INTEGER, allowNull: false, unique: true },
  });

  AdditionalInfo.associate = (models) => {
    AdditionalInfo.belongsTo(models.Application, {
      foreignKey: "applicationId",
    });
  };

  return AdditionalInfo;
};
