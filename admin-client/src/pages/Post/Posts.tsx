import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import useDataGrid from "../../hooks/dataGrid.hook";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { RouteNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IPost } from "../../models/IPost";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { Link } from "react-router-dom";
import { postGridActions } from "../../store/reducers/grids/postGrid";
import { postService } from "../../api/PostService";

const modelRoutes = RouteNames.post;

const Posts: FC = () => {
  const dataGrid = useDataGrid<IPost>(postService, "postGrid", postGridActions);

  const columns: ColumnsType<IPost> = [
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
    statusColumn<IPost>({ filters: dataGrid.modelOptions?.status }),
  ];

  return (
    <>
      <PageHeader title="Записи" backPath={RouteNames.home} />

      <DataGridTable dataGrid={dataGrid} columns={columns} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default Posts;
