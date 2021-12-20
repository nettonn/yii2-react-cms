import RestService from "../api/RestService";
import { useEffect, useState } from "react";
import { IModel, IModelOptions } from "../types";
import { useQuery } from "react-query";
import { useNavigate } from "react-router-dom";
import { routeNames } from "../routes";
import { requestErrorHandler } from "../utils/functions";

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
      return await modelService.modelOptions<M>(signal);
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
      const data = await modelService.view<T>(id, signal);

      if (data?.view_url) {
        setViewUrl(data.view_url);
      }
      return data;
    },
    {
      enabled: !!id,
      onError: (e) => {
        const errors = requestErrorHandler(e);
        if (errors.status === 404) {
          navigate(routeNames.error.e404, { replace: true });
        }
      },
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
