import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { PostSection, PostSectionModelOptions } from "../../models/PostSection";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { Link } from "react-router-dom";
import { postSectionService } from "../../api/PostSectionService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.postSection;
const postRoutes = routeNames.post;

const PostSectionsPage: FC = () => {
  const dataGridHook = useDataGrid<PostSection, PostSectionModelOptions>(
    postSectionService,
    "postSection"
  );

  const getColumns = (
    modelOptions: PostSectionModelOptions
  ): ColumnsType<PostSection> => [
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
      title: "Записи",
      dataIndex: "posts",
      render: (value, record) => (
        <Link to={postRoutes.indexUrl(record.id)}>Посмотреть</Link>
      ),
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
    statusColumn<PostSection>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="Разделы записей"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Разделы записей",
          },
        ]}
      />

      <DataGridTable {...dataGridHook} getColumns={getColumns} hasUrl={true} />

      <IndexPageActions createPath={modelRoutes.create} />
    </>
  );
};

export default PostSectionsPage;
