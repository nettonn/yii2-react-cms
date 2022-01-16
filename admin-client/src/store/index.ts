import { combineReducers, configureStore } from "@reduxjs/toolkit";
import { authReducer } from "./reducers/auth";
import { layoutReducer } from "./reducers/layout";
import { dataGridReducer } from "./reducers/grid";
import {
  persistStore,
  persistReducer,
  FLUSH,
  REHYDRATE,
  PAUSE,
  PERSIST,
  PURGE,
  REGISTER,
} from "redux-persist";
import storage from "redux-persist/lib/storage"; // defaults to localStorage for web

const rootReducer = combineReducers({
  auth: authReducer,
  layout: layoutReducer,
  grid: dataGridReducer,
});

const persistConfig = {
  key: "app-state",
  storage,
  blacklist: ["auth"],
};

const persistedReducer = persistReducer(persistConfig, rootReducer);

const setupStore = () => {
  return configureStore({
    reducer: persistedReducer,
    middleware: (getDefaultMiddleware) =>
      getDefaultMiddleware({
        serializableCheck: {
          ignoredActions: [FLUSH, REHYDRATE, PAUSE, PERSIST, PURGE, REGISTER],
        },
      }),
  });
};

const getStore = () => {
  const store = setupStore();
  const persistor = persistStore(store);
  return { store, persistor };
};

export default getStore;

export type RootState = ReturnType<typeof rootReducer>;
export type AppStore = ReturnType<typeof setupStore>;
export type AppDispatch = AppStore["dispatch"];
