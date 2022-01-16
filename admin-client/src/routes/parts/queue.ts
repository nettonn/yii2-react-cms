import React from "react";
import { ScheduleOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const QueuesPage = React.lazy(() => import("../../pages/Queue/QueuesPage"));
const QueuePage = React.lazy(() => import("../../pages/Queue/QueuePage"));

const names = {
  index: "/queues",
  update: "/queues/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/queues/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: QueuesPage },
  {
    path: names.update,
    element: QueuePage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: ScheduleOutlined,
};

const queue = { names, routes, icons };

export default queue;
