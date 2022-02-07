import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { Version, VersionModelOptions } from "../../models/Version";
import { versionService } from "../../api/VersionService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.version;

const VersionsPage: FC = () => {
  const dataGridHook = useDataGrid<Version, VersionModelOptions>(
    versionService,
    "version"
  );

  const getColumns = (
    modelOptions: VersionModelOptions
  ): ColumnsType<Version> => [
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
      ellipsis: true,
      render: (value, record) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{value}</Link>;
      },
    },
    {
      title: "Модель",
      dataIndex: "link_class_label",
      key: "link_class",
      sorter: true,
      filters: modelOptions.link_class,
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
      <PageHeader
        title="Версии"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Версии",
          },
        ]}
      />

      <DataGridTable {...dataGridHook} getColumns={getColumns} />
    </>
  );
};

export default VersionsPage;
