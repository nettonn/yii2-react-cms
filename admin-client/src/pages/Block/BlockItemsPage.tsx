import React, { FC, useMemo } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IBlockItem, IBlockItemModelOptions } from "../../models/IBlockItem";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { Link, useParams } from "react-router-dom";
import useDataGrid from "../../hooks/dataGrid.hook";
import BlockItemService from "../../api/BlockItemService";
import { useQuery } from "react-query";
import { blockService } from "../../api/BlockService";
import { IBlock } from "../../models/IBlock";

const modelRoutes = routeNames.blockItem;
const blockRoutes = routeNames.block;

const BlockItemsPage: FC = () => {
  const { blockId } = useParams();

  const { data: blockData } = useQuery(
    [blockService.viewQueryKey(), blockId],
    async ({ signal }) => {
      if (!blockId) throw Error("Id not set");
      return await blockService.view<IBlock>(blockId, signal);
    },
    {
      refetchOnMount: false,
    }
  );

  const blockItemService = useMemo(
    () => new BlockItemService(blockId),
    [blockId]
  );

  const dataGridHook = useDataGrid<IBlockItem, IBlockItemModelOptions>(
    blockItemService,
    "blockItem"
  );

  const getColumns = (
    modelOptions: IBlockItemModelOptions
  ): ColumnsType<IBlockItem> => [
    // {
    //   title: "Id",
    //   dataIndex: "id",
    //   sorter: true,
    //   width: 160,
    // },
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      // filters: ,
      ellipsis: true,
      render: (value, record) => {
        return (
          <Link to={modelRoutes.updateUrl(blockId, record.id)}>{value}</Link>
        );
      },
    },
    {
      title: "Сортировка",
      dataIndex: "sort",
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
    statusColumn<IBlockItem>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="Элементы блока"
        backPath={blockRoutes.updateUrl(blockId)}
        breadcrumbItems={[
          { path: blockRoutes.index, label: "Блоки" },
          {
            path: blockRoutes.updateUrl(blockId),
            label: blockData ? blockData.name : blockId ?? "",
          },
        ]}
      />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
      />
      <IndexPageActions createPath={modelRoutes.createUrl(blockId)} />
    </>
  );
};

export default BlockItemsPage;
