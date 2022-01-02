import React from "react";
import { stringReplace } from "../../utils/functions";
import { IRoute } from "../../types";
import { BlockOutlined } from "@ant-design/icons";
const BlockItemsPage = React.lazy(
  () => import("../../pages/Block/BlockItemsPage")
);
const BlockItemPage = React.lazy(
  () => import("../../pages/Block/BlockItemPage")
);

const names = {
  index: "/blocks/:blockId/items",
  create: "/blocks/:blockId/items/create",
  update: "/blocks/:blockId/items/:id",
  indexUrl: (blockId?: string | number) =>
    stringReplace("/blocks/:blockId/items", { ":blockId": blockId }),
  createUrl: (blockId?: string | number) =>
    stringReplace("/blocks/:blockId/items/create", { ":blockId": blockId }),
  updateUrl: (blockId?: string | number, id?: string | number) =>
    stringReplace("/blocks/:blockId/items/:id", {
      ":blockId": blockId,
      ":id": id,
    }),
};

const routes: IRoute[] = [
  { path: names.index, element: BlockItemsPage },
  {
    path: names.create,
    element: BlockItemPage,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: BlockItemPage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: BlockOutlined,
};

const blockItem = { names, routes, icons };

export default blockItem;
