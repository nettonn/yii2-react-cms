import "antd/dist/antd.css";
import "./wdyr";
import locale from "antd/lib/locale/ru_RU";
import "moment/locale/ru";
import React, { FC } from "react";
import { Provider } from "react-redux";
import { BrowserRouter } from "react-router-dom";
import { QueryClientProvider } from "react-query";
import { ReactQueryDevtools } from "react-query/devtools";
import { ConfigProvider } from "antd";
import { ErrorBoundary } from "react-error-boundary";
import { queryClient } from "./http/query-client";
import { setupStore } from "./store";
import ErrorFallback from "./components/ui/ErrorFallback";
import Router from "./components/routers/Router";

const store = setupStore();

const App: FC = () => {
  return (
    <Provider store={store}>
      <QueryClientProvider client={queryClient}>
        <ConfigProvider locale={locale}>
          <BrowserRouter basename={process.env.PUBLIC_URL}>
            <ErrorBoundary FallbackComponent={ErrorFallback}>
              <Router />
            </ErrorBoundary>
          </BrowserRouter>
        </ConfigProvider>
        <ReactQueryDevtools initialIsOpen={false} />
      </QueryClientProvider>
    </Provider>
  );
};

export default App;
