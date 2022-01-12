import React, { FC, useMemo } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import IndexPageActions from "../../components/crud/PageActions/IndexPageActions";
import { IPost, IPostModelOptions } from "../../models/IPost";
import { ColumnsType } from "antd/lib/table/interface";
import { statusColumn } from "../../components/crud/grid/columns";
import { Link, useParams } from "react-router-dom";
import PostService from "../../api/PostService";
import useDataGrid from "../../hooks/dataGrid.hook";
import { useQuery } from "react-query";
import { IPostSection } from "../../models/IPostSection";
import { postSectionService } from "../../api/PostSectionService";

const modelRoutes = routeNames.post;
const postSectionRoutes = routeNames.postSection;

const PostsPage: FC = () => {
  const { sectionId } = useParams();

  const { data: sectionData } = useQuery(
    [postSectionService.viewQueryKey(), sectionId],
    async ({ signal }) => {
      if (!sectionId) throw Error("Id not set");
      return await postSectionService.view<IPostSection>(sectionId, signal);
    },
    {
      refetchOnMount: false,
    }
  );

  const postService = useMemo(() => new PostService(sectionId), [sectionId]);

  const dataGridHook = useDataGrid<IPost, IPostModelOptions>(
    postService,
    "post"
  );

  const getColumns = (modelOptions: IPostModelOptions): ColumnsType<IPost> => [
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      ellipsis: true,
      render: (value, record) => {
        return (
          <Link to={modelRoutes.updateUrl(sectionId, record.id)}>{value}</Link>
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
    statusColumn<IPost>({ filters: modelOptions.status }),
  ];

  return (
    <>
      <PageHeader
        title="Записи"
        backPath={routeNames.home}
        breadcrumbItems={[
          { path: postSectionRoutes.index, label: "Разделы записей" },
          {
            path: postSectionRoutes.updateUrl(sectionId),
            label: sectionData ? sectionData.name : sectionId ?? "",
          },
          {
            path: modelRoutes.indexUrl(sectionId),
            label: "Записи",
          },
        ]}
      />

      <DataGridTable
        dataGridHook={dataGridHook}
        getColumns={getColumns}
        hasUrl={true}
      />

      <IndexPageActions createPath={modelRoutes.createUrl(sectionId)} />
    </>
  );
};

export default PostsPage;
