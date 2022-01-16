import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { Redirect, RedirectModelOptions } from "../../models/Redirect";
import { redirectService } from "../../api/RedirectService";
import { statusColumn } from "../../components/crud/grid/columns";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.redirect;

const RedirectsPage: FC = () => {
  const dataGridHook = useDataGrid<Redirect, RedirectModelOptions>(
    redirectService,
    "redirect"
  );

  const getColumns = (
    modelOptions: RedirectModelOptions
  ): ColumnsType<Redirect> => [
    {
      title: "Id",
      dataIndex: "id",
      sorter: true,
      width: 80,
    },
    {
      title: "Откуда",
      dataIndex: "from",
      sorter: true,
      ellipsis: true,
      render: (value, record) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{value}</Link>;
      },
    },
    {
      title: "Куда",
      dataIndex: "to",
      sorter: true,
      ellipsis: true,
    },
    {
      title: "Код",
      dataIndex: "code",
      sorter: true,
      width: 70,
    },
    {
      title: "Изменено",
      dataIndex: "updated_at_date",
      key: "updated_at",
      sorter: true,
      width: 120,
    },
    statusColumn<Redirect>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="Редиректы"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Редиректы",
          },
        ]}
      />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default RedirectsPage;
