import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import { IUser, IUserModelOptions } from "../../models/IUser";
import { ColumnsType } from "antd/lib/table/Table";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { userService } from "../../api/UserService";
import useDataGrid from "../../hooks/dataGrid.hook";
import { Link } from "react-router-dom";
import { statusColumn } from "../../components/crud/grid/columns";

const modelRoutes = routeNames.user;

const UsersPage: FC = () => {
  const dataGridHook = useDataGrid<IUser, IUserModelOptions>(
    userService,
    "user"
  );

  const getColumns = (modelOptions: IUserModelOptions): ColumnsType<IUser> => [
    {
      title: "Id",
      dataIndex: "id",
      sorter: true,
      width: 70,
    },
    {
      title: "E-Mail",
      dataIndex: "email",
      sorter: true,
      ellipsis: true,
      render: (value, record) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{value}</Link>;
      },
    },
    {
      title: "Роль",
      dataIndex: "role_text",
      key: "role",
      sorter: true,
      filters: modelOptions.role,
    },
    statusColumn<IUser>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader title="Пользователи" backPath={routeNames.home} />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default UsersPage;
