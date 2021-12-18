import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { Descriptions, Spin } from "antd";
import { RouteNames } from "../../routes";
import { logService } from "../../api/LogService";
import { ILog, ILogModelOptions } from "../../models/ILog";
import { useModelView } from "../../hooks/modelView.hook";

const modelRoutes = RouteNames.log;

const LogPage: FC = () => {
  const { id } = useParams();

  const { data, isInit } = useModelView<ILog, ILogModelOptions>(id, logService);

  if (!isInit) return <Spin spinning={true} />;

  if (!data) return null;

  return (
    <>
      <PageHeader
        title="Просмотр лога"
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Логи" }]}
      />

      <Descriptions bordered style={{ marginBottom: "30px" }}>
        <Descriptions.Item label="name">{data.name}</Descriptions.Item>
        <Descriptions.Item label="Время">
          {data.created_at_datetime}
        </Descriptions.Item>
        <hr />
        <Descriptions.Item label="Url">{data.url}</Descriptions.Item>
      </Descriptions>
      <pre style={{ whiteSpace: "pre-wrap" }}>{data.messages}</pre>
    </>
  );
};

export default LogPage;
