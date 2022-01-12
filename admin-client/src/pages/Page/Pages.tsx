import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IPage, IPageModelOptions } from "../../models/IPage";
import { ColumnsType } from "antd/lib/table/Table";
import { statusColumn } from "../../components/crud/grid/columns";
import { pageService } from "../../api/PageService";
import { Link } from "react-router-dom";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.page;

const Pages: FC = () => {
  const dataGridHook = useDataGrid<IPage, IPageModelOptions>(
    pageService,
    "page"
  );

  const getColumns = (modelOptions: IPageModelOptions): ColumnsType<IPage> => [
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
    statusColumn<IPage>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="Страницы"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Страницы",
          },
        ]}
      />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
        hasUrl={true}
      />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Pages;
