import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { IVersion, IVersionModelOptions } from "../../models/IVersion";
import { versionService } from "../../api/VersionService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.version;

const VersionsPage: FC = () => {
  const dataGridHook = useDataGrid<IVersion, IVersionModelOptions>(
    versionService,
    "version"
  );

  const getColumns = (
    modelOptions: IVersionModelOptions
  ): ColumnsType<IVersion> => [
    {
      title: "Id",
      dataIndex: "id",
      sorter: true,
      width: 80,
    },
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      // filters: ,
      ellipsis: true,
      render: (value, record) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{value}</Link>;
      },
    },
    {
      title: "Модель",
      dataIndex: "link_type_label",
      key: "link_type",
      sorter: true,
      filters: modelOptions.link_type,
    },
    {
      title: "ID модели",
      dataIndex: "link_id",
      sorter: true,
      width: 120,
      filters: modelOptions.link_id,
    },
    {
      title: "Действие",
      dataIndex: "action_text",
      key: "action",
      sorter: true,
      width: 120,
      filters: modelOptions.action,
    },
    {
      title: "Создано",
      dataIndex: "created_at_datetime",
      key: "created_at",
      sorter: true,
      width: 200,
    },
  ];

  return (
    <>
      <PageHeader title="Версии" backPath={RouteNames.home} />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />
    </>
  );
};

export default VersionsPage;
