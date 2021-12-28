import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import { BrowserRouter } from "react-router-dom";
import { QueryClientProvider } from "react-query";
import { ReactQueryDevtools } from "react-query/devtools";
import { ConfigProvider } from "antd";
import { ErrorBoundary } from "react-error-boundary";
import { queryClient } from "./http/query-client";
import getStore from "./store";
import ErrorFallback from "./components/ui/ErrorFallback";
import "./wdyr";
import "antd/dist/antd.css";
import "./css/app.css";
import locale from "antd/lib/locale/ru_RU";
import "moment/locale/ru";

import App from "./App";
import { PersistGate } from "redux-persist/integration/react";

const { store, persistor } = getStore();

ReactDOM.render(
  <Provider store={store}>
    <PersistGate loading={null} persistor={persistor}>
      <QueryClientProvider client={queryClient}>
        <ConfigProvider locale={locale}>
          <BrowserRouter basename={process.env.PUBLIC_URL}>
            <ErrorBoundary FallbackComponent={ErrorFallback}>
              <App />
            </ErrorBoundary>
          </BrowserRouter>
        </ConfigProvider>
        <ReactQueryDevtools initialIsOpen={false} />
      </QueryClientProvider>
    </PersistGate>
  </Provider>,
  document.getElementById("root")
);
