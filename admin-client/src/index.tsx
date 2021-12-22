import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import { BrowserRouter } from "react-router-dom";
import { QueryClientProvider } from "react-query";
import { ReactQueryDevtools } from "react-query/devtools";
import { ConfigProvider } from "antd";
import { ErrorBoundary } from "react-error-boundary";
import { queryClient } from "./http/query-client";
import { setupStore } from "./store";
import ErrorFallback from "./components/ui/ErrorFallback";
import "./wdyr";
import "antd/dist/antd.css";
import locale from "antd/lib/locale/ru_RU";
import "moment/locale/ru";

import App from "./App";

const store = setupStore();

ReactDOM.render(
  <Provider store={store}>
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
  </Provider>,
  document.getElementById("root")
);
