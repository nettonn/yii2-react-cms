import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const PagesPage = React.lazy(() => import("../../pages/Page/PagesPage"));
const PagePage = React.lazy(() => import("../../pages/Page/PagePage"));

const names = {
  index: "/pages",
  create: "/pages/create",
  update: "/pages/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/pages/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: PagesPage },
  {
    path: names.create,
    element: PagePage,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: PagePage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: FormOutlined,
};

const page = { names, routes, icons };

export default page;
