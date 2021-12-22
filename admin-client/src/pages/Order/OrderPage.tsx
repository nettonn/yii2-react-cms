import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { Descriptions, Spin } from "antd";
import { routeNames } from "../../routes";
import { orderService } from "../../api/OrderService";
import { IOrder, IOrderModelOptions } from "../../models/IOrder";
import { useModelView } from "../../hooks/modelView.hook";
import FileList from "../../components/crud/form/FileUpload/FileList";

const modelRoutes = routeNames.order;

const OrderPage: FC = () => {
  const { id } = useParams();

  const { data, isInit } = useModelView<IOrder, IOrderModelOptions>(
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
        breadcrumbItems={[{ path: modelRoutes.index, label: "Заявки" }]}
      />

      <Descriptions bordered style={{ marginBottom: "30px" }}>
        <Descriptions.Item label="Тема">{data.subject}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="Имя">{data.name}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="Телефон">{data.phone}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="E-Mail">{data.email}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="Сообщение">
          <pre style={{ whiteSpace: "pre-wrap" }}>{data.message}</pre>
        </Descriptions.Item>
        <hr />
        <Descriptions.Item label="Информация">{data.info}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="Время">
          {data.created_at_datetime}
        </Descriptions.Item>
        <hr />
        <Descriptions.Item label="Url">{data.url}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="Источник">{data.referrer}</Descriptions.Item>
        <hr />
        <Descriptions.Item label="Страница входа">
          {data.entrance_page}
        </Descriptions.Item>
      </Descriptions>
      <FileList fileIds={data.files_id} />
    </>
  );
};

export default OrderPage;
