import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { Descriptions, Spin } from "antd";
import { RouteNames } from "../../routes";
import { queueService } from "../../api/QueueService";
import { IQueue, IQueueModelOptions } from "../../models/IQueue";
import { useModelView } from "../../hooks/modelView.hook";

const modelRoutes = RouteNames.queue;

const QueuePage: FC = () => {
  const { id } = useParams();

  const { data, isInit } = useModelView<IQueue, IQueueModelOptions>(
    id,
    queueService
  );

  if (!isInit) return <Spin spinning={true} />;

  if (!data) return null;

  return (
    <>
      <PageHeader
        title="Просмотр задачи"
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Задачи" }]}
      />

      <Descriptions bordered style={{ marginBottom: "30px" }}>
        <Descriptions.Item label="Добавлено">
          {data.pushed_at_datetime}
        </Descriptions.Item>
        <Descriptions.Item label="Канал">{data.channel}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="Завершено">
          {data.done_at_datetime}
        </Descriptions.Item>

        <Descriptions.Item label="Попытка">{data.attempt}</Descriptions.Item>
      </Descriptions>
      <pre style={{ whiteSpace: "pre-wrap" }}>{data.job_data}</pre>
    </>
  );
};

export default QueuePage;
