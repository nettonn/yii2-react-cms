import { combineReducers, configureStore } from "@reduxjs/toolkit";
import { mainReducer } from "./reducers/main";
import { authReducer } from "./reducers/auth";
import { gridReducers } from "./reducers/grid";

const rootReducer = combineReducers({
  ...mainReducer,
  ...authReducer,
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
