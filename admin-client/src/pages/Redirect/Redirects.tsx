import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import useDataGrid from "../../hooks/dataGrid.hook";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { IRedirect } from "../../models/IRedirect";
import { redirectService } from "../../api/RedirectService";
import { redirectGridActions } from "../../store/reducers/grids/redirectGrid";
import { statusColumn } from "../../components/crud/grid/columns";

const modelRoutes = RouteNames.redirect;

const Redirects: FC = () => {
  const dataGrid = useDataGrid<IRedirect>(
    redirectService,
    "redirectGrid",
    redirectGridActions
  );

  const columns: ColumnsType<IRedirect> = [
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
          <Link to={modelRoutes.view.replace(/:id/, record.id.toString())}>
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
    statusColumn<IRedirect>({ filters: dataGrid.modelOptions?.status }),
  ];

  return (
    <>
      <PageHeader title="Редиректы" backPath={RouteNames.home} />

      <DataGridTable dataGrid={dataGrid} columns={columns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Redirects;
