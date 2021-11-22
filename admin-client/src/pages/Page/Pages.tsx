import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import useDataGrid from "../../hooks/dataGrid.hook";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IPage } from "../../models/IPage";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { pageService } from "../../api/PageService";
import { Link } from "react-router-dom";
import { pageGridActions } from "../../store/reducers/grids/pageGrid";

const modelRoutes = RouteNames.page;

const Pages: FC = () => {
  const dataGrid = useDataGrid<IPage>(pageService, "pageGrid", pageGridActions);

  const columns: ColumnsType<IPage> = [
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
      render: (text: any, record: IPage) => {
        return (
          <Link to={modelRoutes.view.replace(/:id/, record.id.toString())}>
            {text}
          </Link>
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
    statusColumn<IPage>({ filters: dataGrid.modelOptions?.status }),
  ];

  return (
    <>
      <PageHeader title="Страницы" backPath={RouteNames.home} />

      <DataGridTable
        dataGrid={dataGrid}
        columns={columns}
        scroll={{ x: 800 }}
      />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Pages;
