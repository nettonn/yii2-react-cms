import common from "./parts/common";
import error from "./parts/error";
import user from "./parts/user";
import post from "./parts/post";
import page from "./parts/page";
import chunk from "./parts/chunk";
import redirect from "./parts/redirect";
import setting from "./parts/setting";
import seo from "./parts/seo";
import menu from "./parts/menu";
import menuItem from "./parts/menu-item";
import version from "./parts/version";
import log from "./parts/log";
import queue from "./parts/queue";
import order from "./parts/order";
import { IRoute } from "../types";

export const routeNames = {
  ...common.names,
  error: error.names,
  user: user.names,
  post: post.names,
  page: page.names,
  chunk: chunk.names,
  redirect: redirect.names,
  setting: setting.names,
  seo: seo.names,
  menu: menu.names,
  menuItem: menuItem.names,
  version: version.names,
  log: log.names,
  queue: queue.names,
  order: order.names,
};

export const routes: readonly IRoute[] = [
  ...common.routes,
  ...error.routes,
  ...user.routes,
  ...post.routes,
  ...page.routes,
  ...chunk.routes,
  ...redirect.routes,
  ...setting.routes,
  ...seo.routes,
  ...menu.routes,
  ...menuItem.routes,
  ...version.routes,
  ...log.routes,
  ...queue.routes,
  ...order.routes,
];

export const routeIcons = {
  ...common.icons,
  ...user.icons,
  ...post.icons,
  ...page.icons,
  ...chunk.icons,
  ...redirect.icons,
  ...setting.icons,
  ...seo.icons,
  ...menu.icons,
  ...menuItem.icons,
  ...version.icons,
  ...log.icons,
  ...queue.icons,
  ...order.icons,
};
