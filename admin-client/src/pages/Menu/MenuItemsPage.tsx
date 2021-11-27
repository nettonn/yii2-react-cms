import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IMenuItem, IMenuItemModelOptions } from "../../models/IMenuItem";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { menuItemService } from "../../api/MenuItemService";
import { Link, useParams } from "react-router-dom";
import { menuItemGridActions } from "../../store/reducers/grids/menuItemGrid";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.menuItem;
const menuRoutes = RouteNames.menu;

const MenuItemsPage: FC = () => {
  const { menuId } = useParams();

  menuItemService.menuId = menuId;

  const dataGridHook = useDataGrid<IMenuItem, IMenuItemModelOptions>(
    menuItemService,
    "menuItemGrid",
    menuItemGridActions
  );

  const getColumns = (
    modelOptions: IMenuItemModelOptions
  ): ColumnsType<IMenuItem> => [
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
      render: (text: any, record: IMenuItem) => {
        return (
          <Link to={modelRoutes.updateUrl(menuId, record.id)}>{text}</Link>
        );
      },
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
    statusColumn<IMenuItem>({ filters: modelOptions.status }),
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
            label: menuId ? menuId : "",
          },
        ]}
      />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
      />
      <IndexPageActions createPath={modelRoutes.createUrl(menuId)} />
    </>
  );
};

export default MenuItemsPage;