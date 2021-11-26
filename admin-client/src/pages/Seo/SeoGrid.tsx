import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { ISeo, ISeoModelOptions } from "../../models/ISeo";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { seoService } from "../../api/SeoService";
import { Link } from "react-router-dom";
import { seoGridActions } from "../../store/reducers/grids/seoGrid";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.seo;

const SeoGrid: FC = () => {
  const dataGridHook = useDataGrid<ISeo, ISeoModelOptions>(
    seoService,
    "seoGrid",
    seoGridActions
  );

  const getColumns = (modelOptions: ISeoModelOptions): ColumnsType<ISeo> => [
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
      render: (text: any, record: ISeo) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{text}</Link>;
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
    statusColumn<ISeo>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader title="SEO" backPath={RouteNames.home} />

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

export default SeoGrid;
