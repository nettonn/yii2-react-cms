import { combineReducers, configureStore } from "@reduxjs/toolkit";
import { appReducer } from "./reducers/app";
import { authReducer } from "./reducers/auth";
import { eventReducer } from "./reducers/event";
import { pageGridActions, pageGridReducer } from "./reducers/grids/pageGrid";
import { postGridActions, postGridReducer } from "./reducers/grids/postGrid";
import { userGridActions, userGridReducer } from "./reducers/grids/userGrid";
import { chunkGridActions, chunkGridReducer } from "./reducers/grids/chunkGrid";
import {
  redirectGridActions,
  redirectGridReducer,
} from "./reducers/grids/redirectGrid";
import {
  settingGridActions,
  settingGridReducer,
} from "./reducers/grids/settingGrid";

const rootReducer = combineReducers({
  app: appReducer,
  auth: authReducer,
  event: eventReducer,
  pageGrid: pageGridReducer,
  postGrid: postGridReducer,
  userGrid: userGridReducer,
  chunkGrid: chunkGridReducer,
  redirectGrid: redirectGridReducer,
  settingGrid: settingGridReducer,
});

export const setupStore = () => {
  return configureStore({
    reducer: rootReducer,
  });
};

export type RootState = ReturnType<typeof rootReducer>;
export type AppStore = ReturnType<typeof setupStore>;
export type AppDispatch = AppStore["dispatch"];

export type DataGridSelector =
  | "pageGrid"
  | "postGrid"
  | "userGrid"
  | "chunkGrid"
  | "redirectGrid"
  | "settingGrid";

export type DataGridActions =
  | typeof pageGridActions
  | typeof postGridActions
  | typeof userGridActions
  | typeof chunkGridActions
  | typeof redirectGridActions
  | typeof settingGridActions;
