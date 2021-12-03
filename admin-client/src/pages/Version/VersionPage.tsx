import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { Spin, Table } from "antd";
import { RouteNames } from "../../routes";
import { versionService } from "../../api/VersionService";
import {
  IVersion,
  IVersionModelOptions,
  VERSION_ACTION_UPDATE,
} from "../../models/IVersion";
import { useModelView } from "../../hooks/modelView.hook";
import "./VersionPage.css";

const modelRoutes = RouteNames.version;

const VersionPage: FC = () => {
  const { id } = useParams();

  const { data, isInit } = useModelView<IVersion, IVersionModelOptions>(
    id,
    versionService
  );

  if (!isInit) return <Spin spinning={true} />;

  if (!data) return null;

  const columns = [
    {
      title: "Поле",
      dataIndex: "label",
    },
    {
      title: "Данные версии",
      dataIndex: "version_value",
    },
  ];

  if (data.action === VERSION_ACTION_UPDATE) {
    columns.push({
      title: "Текущая версия",
      dataIndex: "current_value",
    });
  }

  return (
    <>
      <PageHeader
        title="Просмотр версии"
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Версии" }]}
      />

      <Table
        columns={columns}
        dataSource={data.attributes_compare}
        rowKey="attribute"
        rowClassName={(record) => {
          if (record.is_diff) {
            return "ant-table-row-version-diff";
          }
          return "";
        }}
        pagination={{
          pageSize: 1000,
          hideOnSinglePage: true,
        }}
      />
    </>
  );
};

export default VersionPage;
