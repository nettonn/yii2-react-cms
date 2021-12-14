import { combineReducers, configureStore } from "@reduxjs/toolkit";
import { appReducer } from "./reducers/app";
import { authReducer } from "./reducers/auth";
import { eventReducer } from "./reducers/event";
import { gridReducers } from "./reducers/grid";

const rootReducer = combineReducers({
  app: appReducer,
  auth: authReducer,
  event: eventReducer,
  ...gridReducers,
});

export const setupStore = () => {
  return configureStore({
    reducer: rootReducer,
  });
};

export type RootState = ReturnType<typeof rootReducer>;
export type AppStore = ReturnType<typeof setupStore>;
export type AppDispatch = AppStore["dispatch"];
