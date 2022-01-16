import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { Descriptions, Spin } from "antd";
import { routeNames } from "../../routes";
import { orderService } from "../../api/OrderService";
import { Order, OrderModelOptions } from "../../models/Order";
import { useModelView } from "../../hooks/modelView.hook";
import FileList from "../../components/crud/FileList/FileList";

const modelRoutes = routeNames.order;

const OrderPage: FC = () => {
  const { id } = useParams();

  const { data, isInit } = useModelView<Order, OrderModelOptions>(
    id,
    orderService
  );

  if (!isInit) return <Spin spinning={true} />;

  if (!data) return null;

  return (
    <>
      <PageHeader
        title="Просмотр заявки"
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Заявки" },
          {
            path: modelRoutes.updateUrl(id),
            label: `${id}`,
          },
        ]}
      />

      <Descriptions bordered style={{ marginBottom: "20px" }} column={1}>
        <Descriptions.Item label="Тема">{data.subject}</Descriptions.Item>
        <Descriptions.Item label="Имя">{data.name}</Descriptions.Item>
        <Descriptions.Item label="Телефон">{data.phone}</Descriptions.Item>
        <Descriptions.Item label="E-Mail">{data.email}</Descriptions.Item>
        <Descriptions.Item label="Сообщение">
          <pre style={{ whiteSpace: "pre-wrap" }}>{data.message}</pre>
        </Descriptions.Item>
        <Descriptions.Item label="Информация">{data.info}</Descriptions.Item>
        <Descriptions.Item label="Время">
          {data.created_at_datetime}
        </Descriptions.Item>
        <Descriptions.Item label="IP">{data.ip}</Descriptions.Item>
        <Descriptions.Item label="User Agent">
          {data.user_agent}
        </Descriptions.Item>
        <Descriptions.Item label="Url">{data.url}</Descriptions.Item>
        <Descriptions.Item label="Источник">{data.referrer}</Descriptions.Item>
        <Descriptions.Item label="Страница входа">
          {data.entrance_page}
        </Descriptions.Item>
      </Descriptions>
      <FileList fileIds={data.files_id} hasControls={false} />
    </>
  );
};

export default OrderPage;
