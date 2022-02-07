import React, { FC, useMemo } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { MenuItem, MenuItemModelOptions } from "../../models/MenuItem";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { Link, useParams } from "react-router-dom";
import useDataGrid from "../../hooks/dataGrid.hook";
import MenuItemService from "../../api/MenuItemService";
import { useQuery } from "react-query";
import { menuService } from "../../api/MenuService";
import { Menu } from "../../models/Menu";

const modelRoutes = routeNames.menuItem;
const menuRoutes = routeNames.menu;

const MenuItemsPage: FC = () => {
  const { menuId } = useParams();

  const { data: menuData } = useQuery(
    [menuService.viewQueryKey(), menuId],
    async ({ signal }) => {
      if (!menuId) throw Error("Id not set");
      return await menuService.view<Menu>(menuId, signal);
    },
    {
      refetchOnMount: false,
    }
  );

  const menuItemService = useMemo(() => new MenuItemService(menuId), [menuId]);

  const dataGridHook = useDataGrid<MenuItem, MenuItemModelOptions>(
    menuItemService,
    "menuItem"
  );

  const getColumns = (
    modelOptions: MenuItemModelOptions
  ): ColumnsType<MenuItem> => [
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      ellipsis: true,
      render: (value, record) => {
        return (
          <Link to={modelRoutes.updateUrl(menuId, record.id)}>{value}</Link>
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
    statusColumn<MenuItem>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="Пункты меню"
        backPath={menuRoutes.updateUrl(menuId)}
        breadcrumbItems={[
          { path: menuRoutes.index, label: "Меню" },
          {
            path: menuRoutes.updateUrl(menuId),
            label: menuData ? menuData.name : menuId ?? "",
          },
          {
            path: modelRoutes.indexUrl(menuId),
            label: "Пункты меню",
          },
        ]}
      />

      <DataGridTable
        {...dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
      />
      <IndexPageActions createPath={modelRoutes.createUrl(menuId)} />
    </>
  );
};

export default MenuItemsPage;
