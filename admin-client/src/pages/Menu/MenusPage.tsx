import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { Menu, MenuModelOptions } from "../../models/Menu";
import { ColumnsType } from "antd/lib/table/interface";
import { MenuOutlined } from "@ant-design/icons";
import { statusColumn } from "../../components/crud/grid/columns";
import { menuService } from "../../api/MenuService";
import { Link } from "react-router-dom";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.menu;
const menuItemRoutes = routeNames.menuItem;

const MenusPage: FC = () => {
  const dataGridHook = useDataGrid<Menu, MenuModelOptions>(menuService, "menu");

  const getColumns = (modelOptions: MenuModelOptions): ColumnsType<Menu> => [
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
    statusColumn<Menu>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="Меню"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Меню",
          },
        ]}
      />

      <DataGridTable
        {...dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
        actionButtons={(record: Menu) => [
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

export default MenusPage;
