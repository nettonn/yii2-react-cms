import createGridSlice from "./createGridSlice";
import { $Keys, ValuesType } from "utility-types";

// TODO how to make it right with typescript autocompletion

const slices = {
  page: createGridSlice("page"),
  post: createGridSlice("post"),
  user: createGridSlice("user"),
  chunk: createGridSlice("chunk"),
  redirect: createGridSlice("redirect"),
  setting: createGridSlice("setting"),
  seo: createGridSlice("seo"),
  menu: createGridSlice("menu"),
  menuItem: createGridSlice("menuItem"),
  version: createGridSlice("version"),
  log: createGridSlice("log"),
  queue: createGridSlice("queue"),
};

export const gridReducers = {
  page: slices.page.reducer,
  post: slices.post.reducer,
  user: slices.user.reducer,
  chunk: slices.chunk.reducer,
  redirect: slices.redirect.reducer,
  setting: slices.setting.reducer,
  seo: slices.seo.reducer,
  menu: slices.menu.reducer,
  menuItem: slices.menuItem.reducer,
  version: slices.version.reducer,
  log: slices.log.reducer,
  queue: slices.queue.reducer,
};

export const gridActions = {
  page: slices.page.actions,
  post: slices.post.actions,
  user: slices.user.actions,
  chunk: slices.chunk.actions,
  redirect: slices.redirect.actions,
  setting: slices.setting.actions,
  seo: slices.seo.actions,
  menu: slices.menu.actions,
  menuItem: slices.menuItem.actions,
  version: slices.version.actions,
  log: slices.log.actions,
  queue: slices.queue.actions,
};

export type DataGridSelector = $Keys<typeof slices>;

export type DataGridActions = ValuesType<typeof gridActions>;

// const selectors: readonly string[] = [
//   "page",
//   "post",
//   "user",
//   "chunk",
//   "redirect",
//   "setting",
//   "seo",
//   "menu",
//   "menuItem",
//   "version",
//   "log",
//   "queue",
// ];
//
// type ArrayToObjectType<KS extends readonly string[], T> = { [K in KS[number]]: T };
//
// type SlicesType = ArrayToObjectType<
//   typeof selectors,
//   ReturnType<typeof createGridSlice>
//   >;
//
// const slices: SlicesType = selectors.reduce((object: SlicesType, selector) => {
//   object[selector] = createGridSlice(selector);
//   return object;
// }, {} as any);
//
// type ReducersType = ArrayToObjectType<typeof selectors, Reducer<DataGridState>>;
//
// export const gridReducers: ReducersType = selectors.reduce(
//   (object: ReducersType, selector) => {
//     object[selector] = slices[selector].reducer;
//     return object;
//   },
//   {} as any
// );
//
// type ActionsType = ArrayToObjectType<
//   typeof selectors,
//   CaseReducerActions<SliceCaseReducers<DataGridState>>
//   >;
//
// export const gridActions: ActionsType = selectors.reduce(
//   (object: ActionsType, selector) => {
//     object[selector] = slices[selector].actions;
//     return object;
//   },
//   {} as any
// );
