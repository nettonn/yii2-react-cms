import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IMenu, IMenuModelOptions } from "../../models/IMenu";
import { ColumnsType } from "antd/lib/table/interface";
import { MenuOutlined } from "@ant-design/icons";
import { statusColumn } from "../../components/crud/grid/columns";
import { menuService } from "../../api/MenuService";
import { Link } from "react-router-dom";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.menu;
const menuItemRoutes = RouteNames.menuItem;

const MenuGridPage: FC = () => {
  const dataGridHook = useDataGrid<IMenu, IMenuModelOptions>(
    menuService,
    "menu"
  );

  const getColumns = (modelOptions: IMenuModelOptions): ColumnsType<IMenu> => [
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
    statusColumn<IMenu>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader title="Меню" backPath={RouteNames.home} />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
        actionButtons={(record: IMenu) => [
          <Link
            key="menuItems"
            to={menuItemRoutes.indexUrl(record.id)}
            title="Пункты меню"
          >
            <MenuOutlined />
          </Link>,
        ]}
      />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default MenuGridPage;
