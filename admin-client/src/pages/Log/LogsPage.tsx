import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { Log, LogModelOptions } from "../../models/Log";
import { logService } from "../../api/LogService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.log;

const LogsPage: FC = () => {
  const dataGridHook = useDataGrid<Log, LogModelOptions>(logService, "log");

  const getColumns = (modelOptions: LogModelOptions): ColumnsType<Log> => [
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
      filters: modelOptions.name,
    },
    {
      title: "Url",
      dataIndex: "url",
      sorter: true,
      ellipsis: true,
    },
    {
      title: "Время",
      dataIndex: "created_at_datetime",
      key: "created_at",
      sorter: true,
      width: 200,
    },
  ];

  return (
    <>
      <PageHeader
        title="Логи"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Логи",
          },
        ]}
      />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />
    </>
  );
};

export default LogsPage;
