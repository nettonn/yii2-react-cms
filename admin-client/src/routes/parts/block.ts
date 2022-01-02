import React from "react";
import { BlockOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
import { IRoute } from "../../types";
const BlocksPage = React.lazy(() => import("../../pages/Block/BlocksPage"));
const BlockPage = React.lazy(() => import("../../pages/Block/BlockPage"));

const names = {
  index: "/blocks",
  create: "/blocks/create",
  update: "/blocks/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/blocks/:id", { ":id": id }),
};

const routes: IRoute[] = [
  { path: names.index, element: BlocksPage },
  {
    path: names.create,
    element: BlockPage,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: BlockPage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: BlockOutlined,
};

const block = { names, routes, icons };

export default block;
