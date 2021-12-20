import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IPost, IPostModelOptions } from "../../models/IPost";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { Link } from "react-router-dom";
import { postService } from "../../api/PostService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.post;

const PostsPage: FC = () => {
  const dataGridHook = useDataGrid<IPost, IPostModelOptions>(
    postService,
    "post"
  );

  const getColumns = (modelOptions: IPostModelOptions): ColumnsType<IPost> => [
    // {
    //   title: "Id",
    //   dataIndex: "id",
    //   sorter: true,
    //   width: 80,
    // },
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      // filters: ,
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
    statusColumn<IPost>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader title="Записи" backPath={routeNames.home} />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        hasUrl={true}
      />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default PostsPage;
