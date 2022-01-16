import createGridSlice, { DataGridState } from "./createGridSlice";
import { Reducer } from "redux";

const selectors = [
  "page",
  "postSection",
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
  "block",
  "blockItem",
] as const;

type Selector = typeof selectors[number];

const sliceTyped = (name: Selector) => createGridSlice<Selector>(name);

type Slice = ReturnType<typeof sliceTyped>;

type Slices = Record<Selector, Slice>;

const slices = selectors.reduce<Slices>((object, selector) => {
  object[selector] = createGridSlice(selector);
  return object;
}, {} as any);

type Reducers = Record<Selector, Reducer<DataGridState>>;

export const dataGridReducers = selectors.reduce<Reducers>(
  (object, selector) => {
    object[selector] = slices[selector].reducer;
    return object;
  },
  {} as any
);

const actionsTyped = (name: Selector) => slices[name].actions;

type Actions = ReturnType<typeof actionsTyped>;

type GridActions = Record<Selector, Actions>;

export const dataGridActions = selectors.reduce<GridActions>(
  (object, selector) => {
    object[selector] = slices[selector].actions;
    return object;
  },
  {} as any
);

export type DataGridSelector = Selector;

export type DataGridActions = Actions;
