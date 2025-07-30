import { DataTypes } from 'sequelize';

export default (sequelize) => {
  const Publication = sequelize.define('Publication', {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },
    scopusPublications: { type: DataTypes.STRING, allowNull: false },
    scopusId: { type: DataTypes.STRING, allowNull: false},
    presentedInConference: { type: DataTypes.STRING, allowNull: false },
    paperTitle: { type: DataTypes.STRING, allowNull: false },
    journalName: { type: DataTypes.STRING, allowNull: false },
    publicationYear: { type: DataTypes.STRING, allowNull: false },
    approvedPapers: { type: DataTypes.STRING, allowNull: false },
    applicationId: { type: DataTypes.INTEGER, allowNull: false },
  });

  Publication.associate = (models) => {
    Publication.belongsTo(models.Application, { foreignKey: 'applicationId' });
  };

  return Publication;
};
