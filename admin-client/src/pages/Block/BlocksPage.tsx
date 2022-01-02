import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IBlock, IBlockModelOptions } from "../../models/IBlock";
import { ColumnsType } from "antd/lib/table/interface";
import { MenuOutlined } from "@ant-design/icons";
import { statusColumn } from "../../components/crud/grid/columns";
import { blockService } from "../../api/BlockService";
import { Link } from "react-router-dom";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.block;
const blockItemRoutes = routeNames.blockItem;

const BlocksPage: FC = () => {
  const dataGridHook = useDataGrid<IBlock, IBlockModelOptions>(
    blockService,
    "block"
  );

  const getColumns = (
    modelOptions: IBlockModelOptions
  ): ColumnsType<IBlock> => [
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
    statusColumn<IBlock>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader title="Блоки" backPath={routeNames.home} />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
        actionButtons={(record: IBlock) =>
          record.has_items && [
            <Link
              key="blockItems"
              to={blockItemRoutes.indexUrl(record.id)}
              title="Элементы"
            >
              <MenuOutlined />
            </Link>,
          ]
        }
      />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default BlocksPage;
