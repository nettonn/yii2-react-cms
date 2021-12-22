import React from "react";
import { DollarCircleOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const OrdersPage = React.lazy(() => import("../../pages/Order/OrdersPage"));
const OrderPage = React.lazy(() => import("../../pages/Order/OrderPage"));

const names = {
  index: "/orders",
  update: "/orders/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/orders/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: OrdersPage },
  {
    path: names.update,
    element: OrderPage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: DollarCircleOutlined,
};

const order = { names, routes, icons };

export default order;
