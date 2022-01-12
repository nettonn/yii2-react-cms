import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { IQueue, IQueueModelOptions } from "../../models/IQueue";
import { queueService } from "../../api/QueueService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.queue;

const QueuesPage: FC = () => {
  const dataGridHook = useDataGrid<IQueue, IQueueModelOptions>(
    queueService,
    "queue"
  );

  const getColumns = (
    modelOptions: IQueueModelOptions
  ): ColumnsType<IQueue> => [
    {
      title: "Id",
      dataIndex: "id",
      sorter: true,
      width: 80,
      render: (value, record) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{value}</Link>;
      },
    },
    {
      title: "Канал",
      dataIndex: "channel",
      sorter: true,
      filters: modelOptions.channel,
    },
    {
      title: "Добавлено",
      dataIndex: "pushed_at_datetime",
      key: "pushed_at",
      sorter: true,
      width: 200,
    },
    {
      title: "Завершено",
      dataIndex: "done_at_datetime",
      key: "done_at",
      sorter: true,
      width: 200,
    },
    {
      title: "Попытка",
      dataIndex: "attempt",
      sorter: true,
      width: 120,
    },
  ];

  return (
    <>
      <PageHeader
        title="Задачи"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Задачи",
          },
        ]}
      />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />
    </>
  );
};

export default QueuesPage;
