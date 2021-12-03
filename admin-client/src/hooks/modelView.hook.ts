import RestService from "../api/RestService";
import { message } from "antd";
import { useEffect, useState } from "react";
import { IModel, IModelOptions } from "../types";
import { useQuery } from "react-query";
import { useNavigate } from "react-router-dom";
import { RouteNames } from "../routes";

export function useModelView<
  T extends IModel = IModel,
  M extends IModelOptions = IModelOptions
>(id: number | string | undefined, modelService: RestService) {
  const [isInit, setIsInit] = useState(false);
  const [viewUrl, setViewUrl] = useState<string | undefined>();

  const navigate = useNavigate();

  const {
    data: modelOptions,
    isFetching: modelOptionsIsFetching,
    isFetched: modelOptionsIsFetched,
    isSuccess: modelOptionsIsSuccess,
    error: modelOptionsError,
  } = useQuery(
    modelService.modelOptionsQueryKey(),
    async ({ signal }) => {
      const result = await modelService.modelOptions<M>(signal);
      if (result.success) {
        return result.data;
      }
      message.error(result.error);
      throw new Error(result.error);
    },
    {
      refetchOnMount: false,
    }
  );

  const {
    data,
    isFetched: viewIsFetched,
    isFetching: viewIsFetching,
    isSuccess: viewIsSuccess,
    error: viewError,
  } = useQuery(
    [modelService.viewQueryKey(), id],
    async ({ signal }) => {
      if (!id) throw Error("Id not set");
      const result = await modelService.view<T>(id, signal);

      if (result.success) {
        if (result.data?.view_url) {
          setViewUrl(result.data.view_url);
        }
        return result.data;
      }
      if (result.status === 404) {
        navigate(RouteNames.error.e404, { replace: true });
      } else {
        message.error(result.error);
      }
      throw new Error(result.error);
    },
    {
      enabled: !!id,
    }
  );

  useEffect(() => {
    if (isInit) return;
    if (
      viewIsSuccess &&
      !viewIsFetching &&
      modelOptionsIsSuccess &&
      !modelOptionsIsFetching
    ) {
      setIsInit(true);
    }
  }, [
    isInit,
    viewIsSuccess,
    viewIsFetching,
    modelOptionsIsSuccess,
    modelOptionsIsFetching,
  ]);

  const error = viewError || modelOptionsError;

  const isDataLoading = !viewIsFetched || !modelOptionsIsFetched;

  const isNotFound = isInit && id && !data;

  return {
    id,
    viewUrl,
    data,
    modelOptions,
    error,
    isInit,
    isDataLoading,
    isNotFound,
  };
}
