import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IPost, IPostModelOptions } from "../../models/IPost";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { Link } from "react-router-dom";
import { postGridActions } from "../../store/reducers/grids/postGrid";
import { postService } from "../../api/PostService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = RouteNames.post;

const Posts: FC = () => {
  const dataGridHook = useDataGrid(postService, "postGrid", postGridActions);

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
      render: (text: any, record: IPost) => {
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
    statusColumn<IPost>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader title="Записи" backPath={RouteNames.home} />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        hasUrl={true}
      />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Posts;
