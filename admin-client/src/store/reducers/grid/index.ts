import createGridSlice, { DataGridState } from "./createGridSlice";
import { Reducer } from "redux";

const selectors = [
  "page",
  "post",
  "user",
  "chunk",
  "redirect",
  "setting",
  "seo",
  "menu",
  "menuItem",
  "version",
  "log",
  "queue",
  "order",
] as const;

type Selector = typeof selectors[number];

const sliceTyped = (name: Selector) => createGridSlice<Selector>(name);

type Slice = ReturnType<typeof sliceTyped>;

type SlicesType = Record<Selector, Slice>;

const slices = selectors.reduce<SlicesType>((object, selector) => {
  object[selector] = createGridSlice(selector);
  return object;
}, {} as any);

type ReducersType = Record<Selector, Reducer<DataGridState>>;

export const gridReducers = selectors.reduce<ReducersType>(
  (object, selector) => {
    object[selector] = slices[selector].reducer;
    return object;
  },
  {} as any
);

const actionsTyped = (name: Selector) => slices[name].actions;

type Actions = ReturnType<typeof actionsTyped>;

type GridActionsType = Record<Selector, Actions>;

export const gridActions = selectors.reduce<GridActionsType>(
  (object, selector) => {
    object[selector] = slices[selector].actions;
    return object;
  },
  {} as any
);

export type DataGridSelector = Selector;

export type DataGridActions = Actions;

// TODO how to make it right with typescript autocompletion

// const slices = {
//   page: createGridSlice("page"),
//   post: createGridSlice("post"),
//   user: createGridSlice("user"),
//   chunk: createGridSlice("chunk"),
//   redirect: createGridSlice("redirect"),
//   setting: createGridSlice("setting"),
//   seo: createGridSlice("seo"),
//   menu: createGridSlice("menu"),
//   menuItem: createGridSlice("menuItem"),
//   version: createGridSlice("version"),
//   log: createGridSlice("log"),
//   queue: createGridSlice("queue"),
//   order: createGridSlice("order"),
// };
//
// export const gridReducers = {
//   page: slices.page.reducer,
//   post: slices.post.reducer,
//   user: slices.user.reducer,
//   chunk: slices.chunk.reducer,
//   redirect: slices.redirect.reducer,
//   setting: slices.setting.reducer,
//   seo: slices.seo.reducer,
//   menu: slices.menu.reducer,
//   menuItem: slices.menuItem.reducer,
//   version: slices.version.reducer,
//   log: slices.log.reducer,
//   queue: slices.queue.reducer,
//   order: slices.order.reducer,
// };
//
// export const gridActions = {
//   page: slices.page.actions,
//   post: slices.post.actions,
//   user: slices.user.actions,
//   chunk: slices.chunk.actions,
//   redirect: slices.redirect.actions,
//   setting: slices.setting.actions,
//   seo: slices.seo.actions,
//   menu: slices.menu.actions,
//   menuItem: slices.menuItem.actions,
//   version: slices.version.actions,
//   log: slices.log.actions,
//   queue: slices.queue.actions,
//   order: slices.order.actions,
// };
//
// export type DataGridSelector = $Keys<typeof slices>;
//
// export type DataGridActions = ValuesType<typeof gridActions>;
