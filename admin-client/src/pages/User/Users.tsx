import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import useDataGrid from "../../hooks/dataGrid.hook";
import { IUser } from "../../models/IUser";
import { ColumnsType } from "antd/es/table";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { userGridActions } from "../../store/reducers/grids/userGrid";
import { userService } from "../../api/UserService";

const modelRoutes = RouteNames.user;

const Users: FC = () => {
  const dataGrid = useDataGrid<IUser>(userService, "userGrid", userGridActions);

  const columns: ColumnsType<IUser> = [
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
      // filters: ,
    },
    {
      title: "Роль",
      dataIndex: "role_text",
      key: "role",
      sorter: true,
      filters: dataGrid.modelOptions?.role,
    },
    {
      title: "Статус",
      dataIndex: "status_text",
      key: "status",
      sorter: true,
      filters: dataGrid.modelOptions?.status,
    },
  ];

  return (
    <>
      <PageHeader title="Пользователи" backPath={RouteNames.home} />

      <DataGridTable dataGrid={dataGrid} columns={columns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Users;
