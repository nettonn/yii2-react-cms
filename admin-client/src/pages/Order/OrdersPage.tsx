import React, { FC } from "react";
import DataGridTable from "../../components/crud/grid/DataGridTable";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import { routeNames } from "../../routes";
import { ColumnsType } from "antd/lib/table/interface";
import { Link } from "react-router-dom";
import { Order, OrderModelOptions } from "../../models/Order";
import { orderService } from "../../api/OrderService";
import useDataGrid from "../../hooks/dataGrid.hook";

const modelRoutes = routeNames.order;

const OrdersPage: FC = () => {
  const dataGridHook = useDataGrid<Order, OrderModelOptions>(
    orderService,
    "order"
  );

  const getColumns = (modelOptions: OrderModelOptions): ColumnsType<Order> => [
    {
      title: "Id",
      dataIndex: "id",
      sorter: true,
      width: 80,
    },
    {
      title: "Тема",
      dataIndex: "subject",
      sorter: true,
      ellipsis: true,
      render: (value, record) => {
        return <Link to={modelRoutes.updateUrl(record.id)}>{value}</Link>;
      },
    },
    {
      title: "Имя",
      dataIndex: "name",
      sorter: true,
      ellipsis: true,
    },
    {
      title: "Телефон",
      dataIndex: "phone",
      sorter: true,
      ellipsis: true,
    },
    {
      title: "E-Mail",
      dataIndex: "email",
      sorter: true,
      ellipsis: true,
    },
    {
      title: "Время",
      dataIndex: "created_at_datetime",
      key: "created_at",
      sorter: true,
      width: 200,
    },
  ];

  return (
    <>
      <PageHeader
        title="Заявки"
        backPath={routeNames.home}
        breadcrumbItems={[
          {
            path: modelRoutes.index,
            label: "Заявки",
          },
        ]}
      />

      <DataGridTable dataGridHook={dataGridHook} getColumns={getColumns} />
    </>
  );
};

export default OrdersPage;
