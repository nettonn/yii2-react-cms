import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { Chunk, ChunkModelOptions } from "../../models/Chunk";
import { chunkService } from "../../api/ChunkService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.chunk;

const ChunksPage: FC = () => {
  const dataGridHook = useDataGrid<Chunk, ChunkModelOptions>(
    chunkService,
    "chunk"
  );

  const getColumns = (modelOptions: ChunkModelOptions): ColumnsType<Chunk> => [
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
      title: "Ключ",
      dataIndex: "key",
      sorter: true,
      width: 120,
    },
    {
      title: "Тип",
      dataIndex: "type_label",
      key: "type",
      sorter: true,
      filters: modelOptions.type,
    },
    {
      title: "Создано",
      dataIndex: "created_at_date",
      key: "created_at",
      sorter: true,
      width: 120,
    },
    {
      title: "Изменено",
      dataIndex: "updated_at_date",
      key: "updated_at",
      sorter: true,
      width: 120,
    },
  ];

  return (
    <>
      <PageHeader
        title="Чанки"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Чанки",
          },
        ]}
      />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default ChunksPage;
