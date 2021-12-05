import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { Link, useParams } from "react-router-dom";
import { Spin, Table } from "antd";
import { RouteNames } from "../../routes";
import { versionService } from "../../api/VersionService";
import {
  IVersion,
  IVersionAttributesCompare,
  IVersionModelOptions,
  VERSION_ACTION_UPDATE,
} from "../../models/IVersion";
import { useModelView } from "../../hooks/modelView.hook";
import "./VersionPage.css";
import { ColumnsType } from "antd/lib/table/Table";
import { withoutBaseUrl } from "../../utils/functions";

const modelRoutes = RouteNames.version;

const VersionPage: FC = () => {
  const { id } = useParams();

  const { data, isInit } = useModelView<IVersion, IVersionModelOptions>(
    id,
    versionService
  );

  if (!isInit) return <Spin spinning={true} />;

  if (!data) return null;

  const columns: ColumnsType<IVersionAttributesCompare> = [
    {
      title: "Поле",
      dataIndex: "label",
    },
    {
      title: "Данные версии",
      dataIndex: "version_value",
      render: (value) => {
        return <pre>{value}</pre>;
      },
    },
  ];

  if (data.action === VERSION_ACTION_UPDATE) {
    columns.push({
      title: "Текущая версия",
      dataIndex: "current_value",
      render: (value) => {
        return <pre>{value}</pre>;
      },
    });
  }

  return (
    <>
      <PageHeader
        title="Просмотр версии"
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Версии" }]}
      />

      <p>
        <b>
          {data.owner_update_url ? (
            <Link to={withoutBaseUrl(data.owner_update_url)}>{data.name}</Link>
          ) : (
            `${data.name} (${data.link_type} - ${data.link_id})`
          )}
        </b>
      </p>

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
