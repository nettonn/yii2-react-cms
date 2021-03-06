import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { Descriptions, Spin } from "antd";
import { routeNames } from "../../routes";
import { logService } from "../../api/LogService";
import { Log, LogModelOptions } from "../../models/Log";
import { useModelView } from "../../hooks/modelView.hook";

const modelRoutes = routeNames.log;

const LogPage: FC = () => {
  const { id } = useParams();

  const { data, isInit } = useModelView<Log, LogModelOptions>(id, logService);

  if (!isInit) return <Spin spinning={true} />;

  if (!data) return null;

  return (
    <>
      <PageHeader
        title="Просмотр лога"
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Логи" },
          {
            path: modelRoutes.updateUrl(id),
            label: data?.name ?? id,
          },
        ]}
      />

      <Descriptions bordered style={{ marginBottom: "30px" }}>
        <Descriptions.Item label="Название">{data.name}</Descriptions.Item>
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
