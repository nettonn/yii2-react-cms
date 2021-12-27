import { combineReducers, configureStore } from "@reduxjs/toolkit";
import { authReducer } from "./reducers/auth";
import { dataGridReducer } from "./reducers/grid";

const rootReducer = combineReducers({
  auth: authReducer,
  grid: dataGridReducer,
});

export const setupStore = () => {
  return configureStore({
    reducer: rootReducer,
  });
};

export type RootState = ReturnType<typeof rootReducer>;
export type AppStore = ReturnType<typeof setupStore>;
export type AppDispatch = AppStore["dispatch"];
