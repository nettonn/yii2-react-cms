import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { IRedirect, IRedirectModelOptions } from "../../models/IRedirect";
import { redirectService } from "../../api/RedirectService";
import { redirectGridActions } from "../../store/reducers/grids/redirectGrid";
import { statusColumn } from "../../components/crud/grid/columns";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.redirect;

const Redirects: FC = () => {
  const dataGridHook = useDataGrid(
    redirectService,
    "redirectGrid",
    redirectGridActions
  );

  const getColumns = (
    modelOptions: IRedirectModelOptions
  ): ColumnsType<IRedirect> => [
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
      // filters: ,
      ellipsis: true,
      render: (text: any, record: IRedirect) => {
        return (
          <Link to={modelRoutes.update.replace(/:id/, record.id.toString())}>
            {text}
          </Link>
        );
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
    statusColumn<IRedirect>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader title="Редиректы" backPath={RouteNames.home} />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Redirects;
