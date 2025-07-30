import { DataTypes } from 'sequelize';

export default (sequelize) => {
  const Phd = sequelize.define('Phd', {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },
    phdStatus: { type: DataTypes.STRING, allowNull: false },
    phdUniversity: { type: DataTypes.STRING, allowNull: false },
    phdYear: { type: DataTypes.STRING, allowNull: false },
    netYear: { type: DataTypes.STRING, allowNull: true },
    setYear: { type: DataTypes.STRING, allowNull: true },
    applicationId: { type: DataTypes.INTEGER, allowNull: false },
  });

  Phd.associate = (models) => {
    Phd.belongsTo(models.Application, { foreignKey: 'applicationId' });
  };

  return Phd;
};
