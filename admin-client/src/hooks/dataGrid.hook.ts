import RestService, { IRestServiceIndexQueryParams } from "../api/RestService";
import { message } from "antd";
import { TablePaginationConfig } from "antd/lib/table/interface";
import { IFiltersParam, IModel, IModelOptions } from "../types";
import { useQuery, useMutation } from "react-query";
import { useAppActions, useAppSelector } from "./redux";
import { DataGridActions, DataGridSelector } from "../store";
import { useEffect, useState } from "react";
import { appActions } from "../store/reducers/app";
import { queryClient } from "../http/query-client";

export default function useDataGrid<
  T extends IModel = IModel,
  M extends IModelOptions = any
>(
  modelService: RestService,
  dataGridSelector: DataGridSelector,
  dataGridActions: DataGridActions
) {
  const [isInit, setIsInit] = useState(false);
  const { currentDataGridSelector } = useAppSelector((state) => state.app);
  const { setCurrentDataGridSelector } = useAppActions(appActions);

  const {
    currentPage,
    pageSize,
    pageCount,
    dataCount,
    sortField,
    sortDirection,
    searchQuery,
    filters,
  } = useAppSelector((state) => state[dataGridSelector]);

  const {
    setCurrentPage,
    setDataCount,
    setFilters,
    setPageCount,
    setPageSize,
    setSearchQuery,
    setSortDirection,
    setSortField,
  } = useAppActions(dataGridActions);

  const {
    data: modelOptions,
    isFetched: modelOptionsIsFetched,
    isFetching: modelOptionsIsFetching,
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
    isFetched: indexIsFetched,
    isFetching: indexIsFetching,
    isSuccess: indexIsSuccess,
    error: indexError,
  } = useQuery(
    [
      modelService.indexQueryKey(),
      { currentPage, sortField, sortDirection, searchQuery, filters },
    ],
    async ({ signal }) => {
      const params: IRestServiceIndexQueryParams = {};

      if (currentPage) params.page = currentPage;
      if (sortField) params.sortField = sortField;
      if (sortDirection) params.sortDirection = sortDirection;
      if (searchQuery) params.search = searchQuery;
      if (filters) params.filters = filters;

      const result = await modelService.index<T>(params, signal);

      if (result.success) {
        if (result.pagination) {
          if (result.pagination.currentPage !== undefined)
            setCurrentPage(result.pagination.currentPage);
          if (result.pagination.totalCount !== undefined)
            setDataCount(result.pagination.totalCount);

          if (result.pagination.perPage !== undefined)
            setPageSize(result.pagination.perPage);

          if (result.pagination.pageCount !== undefined)
            setPageCount(result.pagination.pageCount);
        }
        return result.data;
      }
      message.error(result.error);
      throw new Error(result.error);
    },
    {
      keepPreviousData: true,
    }
  );

  const {
    mutate: deleteHandler,
    isLoading: deleteIsLoading,
    error: deleteError,
  } = useMutation(async (id: number) => {
    const result = await modelService.delete(id);
    if (result.success) {
      queryClient.invalidateQueries(modelService.listQueryKey());
      queryClient.invalidateQueries(modelService.indexQueryKey());
      return true;
    } else {
      message.error(result.error);
      throw new Error(result.error);
    }
  });

  const tableChangeHandler = (
    tablePagination: TablePaginationConfig,
    tableFilters: any,
    tableSorter: any
  ) => {
    if (tablePagination.current) setCurrentPage(tablePagination.current);
    else setCurrentPage(1);

    if ((tableSorter.columnKey || tableSorter.field) && tableSorter.order) {
      setSortField(tableSorter.columnKey || tableSorter.field);
      setSortDirection(tableSorter.order);
    } else {
      setSortField(null);
      setSortDirection(null);
    }
    if (tableFilters) {
      const filterKeys = Object.keys(tableFilters).filter(
        (key: string) => tableFilters[key]
      );
      if (filterKeys.length) {
        const params = {} as IFiltersParam;
        for (const key of filterKeys) {
          params[`${key}`] = tableFilters[key];
        }
        setFilters(params);
      } else {
        setFilters(null);
      }
    }
  };

  const searchChangeHandler = async (value: string) => {
    setSearchQuery(value);
    setCurrentPage(1);
  };

  const error = indexError || modelOptionsError || deleteError;

  const isLoading =
    !indexIsFetched || !modelOptionsIsFetched || deleteIsLoading;

  useEffect(() => {
    if (isInit) return;
    if (
      currentDataGridSelector &&
      currentDataGridSelector === dataGridSelector
    ) {
      setIsInit(true);
      return;
    }

    if (
      indexIsSuccess &&
      !indexIsFetching &&
      modelOptionsIsSuccess &&
      !modelOptionsIsFetching
    ) {
      setCurrentDataGridSelector(dataGridSelector);
      setIsInit(true);
    }
  }, [
    isInit,
    indexIsSuccess,
    indexIsFetching,
    modelOptionsIsSuccess,
    modelOptionsIsFetching,
    currentDataGridSelector,
    setCurrentDataGridSelector,
    dataGridSelector,
  ]);

  return {
    data,
    modelOptions,
    currentPage,
    pageSize,
    dataCount,
    pageCount,
    isInit,
    isLoading,
    error,
    searchQuery,
    sortField,
    sortDirection,
    filters,
    searchChangeHandler,
    tableChangeHandler,
    deleteHandler,
  };
}
