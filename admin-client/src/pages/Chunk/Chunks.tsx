import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { IChunk, IChunkModelOptions } from "../../models/IChunk";
import { chunkService } from "../../api/ChunkService";
import { chunkGridActions } from "../../store/reducers/grids/chunkGrid";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.chunk;

const Chunks: FC = () => {
  const dataGridHook = useDataGrid(chunkService, "chunkGrid", chunkGridActions);

  const getColumns = (
    modelOptions: IChunkModelOptions
  ): ColumnsType<IChunk> => [
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
      render: (text: any, record: IChunk) => {
        return (
          <Link to={modelRoutes.update.replace(/:id/, record.id.toString())}>
            {text}
          </Link>
        );
      },
    },
    {
      title: "Ключ",
      dataIndex: "key",
      sorter: true,
      width: 120,
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
      <PageHeader title="Чанки" backPath={RouteNames.home} />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Chunks;
