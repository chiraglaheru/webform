import { DataTypes } from "sequelize";

export default (sequelize) => {
  const Qualification = sequelize.define("Qualification", {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },
    degree: { type: DataTypes.STRING, allowNull: false },
    degreeName: { type: DataTypes.STRING, allowNull: false },
    educationMode: { type: DataTypes.STRING, allowNull: false },
    universityName: { type: DataTypes.STRING, allowNull: false },
    specialization: { type: DataTypes.STRING, allowNull: false },
    percentage: { type: DataTypes.STRING, allowNull: false },
    cgpa: { type: DataTypes.STRING },
    yearOfPassing: { type: DataTypes.STRING, allowNull: false },
    applicationId: { type: DataTypes.INTEGER, allowNull: false },
  });

  Qualification.associate = (models) => {
    Qualification.belongsTo(models.Application, {
      foreignKey: "applicationId",
    });
  };

  return Qualification;
};
