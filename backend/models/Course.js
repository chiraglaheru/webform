import { DataTypes } from 'sequelize';

export default (sequelize) => {
  const Course = sequelize.define('Course', {
    id: { type: DataTypes.INTEGER, autoIncrement: true, primaryKey: true },
    courseCollegeName: { type: DataTypes.STRING, allowNull: true },
    courseClassName: { type: DataTypes.STRING , allowNull: true },
    courseSubjectName: { type: DataTypes.STRING, allowNull: true },
    courseYearsOfExp: { type: DataTypes.STRING, allowNull: true },
    courseFromDate: { type: DataTypes.DATE, allowNull: true },
    courseToDate: { type: DataTypes.DATE, allowNull: true },
    courseDepartmentType: { type: DataTypes.STRING, allowNull: true },
    courseTypeOfContract: { type: DataTypes.STRING, allowNull: true },
    courseLastSalary: { type: DataTypes.STRING, allowNull: true },
    courseApprovedByUni: { type: DataTypes.STRING, allowNull: true },
    courseLetterNumber: { type: DataTypes.STRING, allowNull: true },
    courseLetterDate: { type: DataTypes.STRING, allowNull: true },
    applicationId: { type: DataTypes.INTEGER, allowNull: false },
  });

  Course.associate = (models) => {
    Course.belongsTo(models.Application, { foreignKey: 'applicationId' });
  };

  return Course;
};
