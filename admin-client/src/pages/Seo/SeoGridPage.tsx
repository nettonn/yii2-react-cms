import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { Seo, SeoModelOptions } from "../../models/Seo";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { seoService } from "../../api/SeoService";
import { Link } from "react-router-dom";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.seo;

const SeoGridPage: FC = () => {
  const dataGridHook = useDataGrid<Seo, SeoModelOptions>(seoService, "seo");

  const getColumns = (modelOptions: SeoModelOptions): ColumnsType<Seo> => [
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
      title: "Url",
      dataIndex: "url",
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
    statusColumn<Seo>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="SEO"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "SEO",
          },
        ]}
      />

      <DataGridTable
        {...dataGridHook}
        getColumns={getColumns}
        scroll={{ x: 800 }}
        hasUrl={true}
      />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default SeoGridPage;
